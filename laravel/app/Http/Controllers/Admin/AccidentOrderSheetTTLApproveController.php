<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\OfferGMStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentClaimLines;
use App\Models\AccidentRepairLinePrice;
use App\Models\AccidentRepairOrder;
use App\Models\AccidentRepairOrderLine;
use App\Models\Car;
use App\Models\ClaimList;
use App\Models\Cradle;
use App\Models\Province;
use DateTime;
use Illuminate\Http\Request;
use App\Traits\AccidentTrait;
use App\Traits\RepairTrait;
use App\Traits\HistoryTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccidentOrderSheetTTLApproveController extends Controller
{
    public function index(Request $request)
    {
        $worksheet = $request->repair_worksheet_no;
        $accident_worksheet_no = $request->accident_worksheet_no;
        $accident_type = $request->accident_type;
        $license_plate = $request->license_plate;
        $license_plate_text = null;
        if ($license_plate) {
            $license_plate_model = Car::find($license_plate);
            if ($license_plate_model->license_plate) {
                $license_plate_text = $license_plate_model->license_plate;
            } else if ($license_plate_model->engine_no) {
                $license_plate_text = __('inspection_cars.engine_no') . ' ' . $license_plate_model->engine_no;
            } else if ($license_plate_model->chassis_no) {
                $license_plate_text = __('inspection_cars.chassis_no') . ' ' . $license_plate_model->chassis_no;
            }
        }
        $accident = Accident::find($accident_worksheet_no);
        $accident_worksheet_text = $accident && $accident->worksheet_no ? $accident->worksheet_no : null;
        $status = $request->status;
        $accident_repair_order = AccidentRepairOrder::find($worksheet);
        $worksheet_text = $accident_repair_order && $accident_repair_order->worksheet_no ? $accident_repair_order->worksheet_no : null;
        $list = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
            ->leftJoin('cradles', 'cradles.id', '=', 'accident_repair_orders.cradle_id')
            ->leftJoin('cars', 'cars.id', '=', 'accidents.car_id')
            ->sortable(['worksheet_no' => 'desc'])
            ->search($request)
            // ->whereIn('accident_repair_orders.status', [OfferGMStatusEnum::OVER_PRICE,OfferGMStatusEnum::OFFER_NEW_PRICE,OfferGMStatusEnum::CONSIDER_TOTAL_LOSS,OfferGMStatusEnum::CONSIDER_PARTIAL_LOSS])
            ->whereIn('accident_repair_orders.status', [AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL])
            ->select('accident_repair_orders.*', 'accidents.worksheet_no as accident_worksheet', 'cars.license_plate', 'accidents.accident_type', 'cradles.name as cradle_name', 'cars.id as car_id')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $item->over_complete_date = null;
            if ($item->repair_date && $item->amount_completed && $item->actual_repair_date) {
                $day = $item->amount_completed;
                $scheduled_completion_date = Carbon::parse($item->repair_date)->addDays($day);
                $actual_repair_date = new DateTime($item->actual_repair_date);
                $scheduled_completion_date = new DateTime($scheduled_completion_date);
                $diff = $actual_repair_date->diff($scheduled_completion_date);
                $item->over_complete_date = $diff->days;
            }
            $car = Car::find($item->car_id);
            if ($car) {
                if ($car->license_plate) {
                    $text = $car->license_plate;
                } else if ($car->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                }else if ($car->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
                $item->license_plate = $text;
            }
            return $item;
        });

        $repair_status_list = AccidentTrait::getAccidentRepairStatus();
        $accident_status_list = AccidentTrait::getAccidentTypeIndexList();

        return view('admin.accident-order-sheet-ttl-approves.index', [
            'list' => $list,
            'license_plate' => $license_plate,
            'worksheet' => $worksheet,
            'accident_worksheet_no' => $accident_worksheet_no,
            'worksheet_text' => $worksheet_text,
            'repair_status_list' => $repair_status_list,
            'status' => $status,
            'accident_type' => $accident_type,
            'accident_status_list' => $accident_status_list,
            'license_plate_text' => $license_plate_text,
            'accident_worksheet_text' => $accident_worksheet_text,
        ]);
    }

    public function show(AccidentRepairOrder $accident_order_sheet_ttl_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove);
        $page_title = $page_title = __('accident_orders.accident_order_sheet_ttl_approve');
        $garage_list = Cradle::select('id', 'name')->get();
        $province_list = Province::select('id', 'name_th as name')->get();

        $garage_quotation_file = $accident_order_sheet_ttl_approve->getMedia('garage_quotation_file');
        $garage_quotation_file = get_medias_detail($garage_quotation_file);

        $spare_list =  AccidentRepairLinePrice::where('accident_repair_order_id', $accident_order_sheet_ttl_approve->id)->get();
        $spare_list->map(function ($item) {
            $total = $item->spare_parts - $item->discount_spare_parts;
            $item->total = number_format($total);

            return $item;
        });
        $spare_part_list = AccidentTrait::getStatusList();
        $accident_inform = Accident::find($accident_order_sheet_ttl_approve->accident_id);
        $accident_inform->case = __('accident_informs.case_' . $accident_inform->case);
        $accident_claim_line = AccidentRepairOrderLine::where('accident_repair_order_id', $accident_order_sheet_ttl_approve->id)->get();
        $accident_inform->count_accident_line = count($accident_claim_line);
        $getClaimList = $this->getClaimList($accident_inform, $accident_order_sheet_ttl_approve->id);
        $claim_list_data = $getClaimList['claim_list'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = AccidentTrait::getWoundList();

        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order_sheet_ttl_approve->id, ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(AccidentRepairOrder::class, $accident_order_sheet_ttl_approve->id,ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL);

        } else {
            $approve_line_owner = null;
        }


        return view('admin.accident-order-sheet-ttl-approves.form-repair-price-edit',  [
            'page_title' => $page_title,
            'd' => $accident_order_sheet_ttl_approve,
            'accident_order' => $accident_order_sheet_ttl_approve,
            'garage_list' => $garage_list,
            'province_list' => $province_list,
            'garage_quotation_file' => $garage_quotation_file,
            'spare_list' => $spare_list,
            'view' => true,
            'claim_list_data' => $claim_list_data,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'accident_inform' => $accident_inform,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function getClaimList($claim_model, $accident_order_id)
    {
        $accident_repair_order_lines = AccidentRepairOrderLine::where('accident_repair_order_id', $accident_order_id)->pluck('accident_claim_line_id')->toArray();
        $claim_list = AccidentClaimLines::whereIn('id', $accident_repair_order_lines)->get();
        $is_withdraw_true = 0;
        $tls_cost_total = 0;
        $claim_list->map(function ($item) use (&$is_withdraw_true, &$tls_cost_total) {
            if ($item->accident_claim_list_id) {
                $accident_claim_text = ClaimList::find($item->accident_claim_list_id);
                $item->accident_claim_text = $accident_claim_text->name;
            }
            $item->accident_claim_id = ($item->accident_claim_list_id) ? $item->accident_claim_list_id : '';
            $item->wound_characteristics_text = ($item->wound_characteristics) ? __('accident_informs.wound_type_' . $item->wound_characteristics) : '';
            $item->wound_characteristics = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->wound_characteristics_id = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->supplier_text = (!is_null($item->supplier)) ? __('accident_informs.spare_part_status_' . $item->supplier) : '';
            $item->tls_cost = ($item->cost) ? $item->cost : '';

            $before_medias = $item->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->before_files = $before_medias;

            $after_medias = $item->getMedia('after_file');
            $after_medias = get_medias_detail($after_medias);
            $after_medias = collect($after_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->after_files = $after_medias;

            $item->pending_delete_slide_files = [];

            if ($item->is_withdraw_true == 1) {
                $is_withdraw_true += 1;
                $tls_cost_total = $tls_cost_total + intval($item->tls_cost);
            }

            return $item;
        });

        return [
            'claim_list' => $claim_list,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total
        ];
    }

    public function updateAccidentRepairStatus(Request $request)
    {
        if ($request->rp_id) {
            $accident_order = AccidentRepairOrder::find($request->rp_id);
            // update approve step
            $approve_update = new StepApproveManagement();
            //fixed
            $approve_update = $approve_update->updateApprove(AccidentRepairOrder::class, $accident_order->id, $request->rp_status,ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL,$request->reason);
            // $approve_update = $approve_update->updateApprove($request, $accident_order, $request->rp_status, AccidentRepairOrder::class, ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL);
            $approve_update = ($approve_update == AccidentRepairStatusEnum::CONFIRM) ? AccidentRepairStatusEnum::PROCESS_REPAIR : $request->status_ttl_approve;
            $accident_order->status = $approve_update;
            $accident_order->reason = $request->reason;
            if ($request->remark) {
                $accident_order->remark = $request->remark;
            }
            $accident_order->save();

            if(strcmp($request->status_ttl_approve, AccidentRepairStatusEnum::TTL) === 0){
                $accident_order_all = DB::table('accident_repair_orders')->where('accident_id',$accident_order->accident_id)->update(['status' => AccidentRepairStatusEnum::TTL]);
                $accident = Accident::find($accident_order->accident_id);
                $accident->status = AccidentStatusEnum::TTL;
                $accident->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.accident-order-sheet-ttl-approves.index')
            ]);
        }
    }
}
