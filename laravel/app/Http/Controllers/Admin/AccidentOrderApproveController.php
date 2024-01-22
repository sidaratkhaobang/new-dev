<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\AccidentRepairStatusEnum;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentClaimLines;
use App\Models\AccidentExpense;
use App\Models\AccidentRepairOrder;
use App\Models\AccidentRepairOrderLine;
use App\Models\AccidentSlide;
use App\Models\Amphure;
use App\Models\Car;
use App\Models\ClaimList;
use App\Models\Cradle;
use App\Models\District;
use App\Models\Province;
use App\Models\ReplacementCar;
use App\Models\Slide;
use Illuminate\Http\Request;
use App\Traits\AccidentTrait;
use DateTime;
use App\Traits\RepairTrait;
use App\Traits\HistoryTrait;
use Carbon\Carbon;

class AccidentOrderApproveController extends Controller
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
            ->whereIn('accident_repair_orders.status', [AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST])
            ->search($request)
            ->select('accident_repair_orders.*', 'accidents.worksheet_no as accident_worksheet', 'cars.license_plate', 'accidents.accident_type', 'cradles.name as cradle_name', 'cars.id as car_id')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $item->over_complete_date = null;
            if ($item->repair_date && $item->amount_completed) {
                $day = $item->amount_completed;
                $scheduled_completion_date = Carbon::parse($item->repair_date)->addDays($day);
                $item->scheduled_completion_date = $scheduled_completion_date->format('d/m/Y');
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

        return view('admin.accident-order-approves.index', [
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


    public function updateAccidentRepairStatus(Request $request)
    {

        if ($request->rp_id) {
            $accident_order = AccidentRepairOrder::find($request->rp_id);
            // update approve step
            $approve_update = new StepApproveManagement();
            //fixed
            $approve_update = $approve_update->updateApprove(AccidentRepairOrder::class, $accident_order->id, $request->rp_status,ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER,$request->reason);
            // $approve_update = $approve_update->updateApprove($request, $accident_order, $request->rp_status, AccidentRepairOrder::class);
            $approve_update = $approve_update == AccidentRepairStatusEnum::CONFIRM ? AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION : AccidentRepairStatusEnum::REJECT;
            $accident_order->status = $approve_update;
            $accident_order->reason = $request->reason;
            $accident_order->save();

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.accident-order-approves.index')
            ]);
        }
    }

    public function show(AccidentRepairOrder $accident_order_approve)
    {
        $accident = Accident::find($accident_order_approve->accident_id);
        $car = Car::find($accident->car_id);
        $car_license = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($accident->car_id);
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident);
        $need_list = AccidentTrait::getNeedList();
        $replace_list = AccidentTrait::getReplacementList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();

        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();

        $province = Province::find($accident->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;
        $cradle = Cradle::find($accident->cradle);
        $replacement_car_files = $accident->getMedia('replacement_car_files');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $slide_list = $this->getSlideList($accident);
        $cost_list = $this->getCostList($accident);

        // $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order_approve->id);
        // $approve_line_list = $approve_line['approve_line_list'];
        // $approve = $approve_line['approve'];
        // $approve_line_logs = $approve_line['approve_line_logs'];
        // if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(AccidentRepairOrder::class, $accident_order_approve->id,ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER);

        // } else {
        //     $approve_line_owner = null;
        // }

        $replacement_list = $this->getReplacementList($accident, $accident_order_approve);
        $cost_list = $this->getCostList($accident_order_approve);
        $accident_slide_list = AccidentTrait::getAccidentSlideList();

        $receive_status_list = $this->getStatusReceiveList();

        $slide_worksheet_list = Slide::where('job_type', Accident::class)->where('job_id', $accident_order_approve->id)->select('id','worksheet_no as name')->get();

        $page_title = $page_title = __('accident_orders.accident_order_approve');
        return view('admin.accident-order-approves.form-accident-edit',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident,
            'accident_order' => $accident_order_approve,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'cradle' => $cradle,
            'slide_list' => $slide_list,
            'cost_list' => $cost_list,
            'view' => true,
            // 'approve_line_list' => $approve_line_list,
            // 'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            // 'approve_line_logs' => $approve_line_logs,
            'accident_slide_list' => $accident_slide_list,
            'replacement_list' => $replacement_list,
            'receive_status_list' => $receive_status_list,
            'slide_worksheet_list' => $slide_worksheet_list,
        ]);
    }

    public function getReplacementList($accident_model, $accident_order)
    {
        $replacement_list = ReplacementCar::where('job_type', Accident::class)->where('job_id', $accident_model->id)->get();
        $replacement_list->map(function ($item) use ($accident_model, $accident_order) {
            $item->replacement_type = ($item->replacement_type) ? $item->replacement_type : '';
            $item->replacement_pickup_date = ($item->replacement_date) ? $item->replacement_date : '';
            $item->slide_worksheet = ($item->slide_id) ? $item->slide_id : '';
            $item->place = ($item->replacement_place) ? $item->replacement_place : '';
            $item->customer_receive = ($item->is_cust_receive_replace) ? $item->is_cust_receive_replace : STATUS_DEFAULT;
            $item->accident_id = $accident_model->id;
            $item->car_id = $accident_model->car_id;
            $item->id = $item->id;
            $item->worksheet = $item->worksheet_no;
            $item->accident_order_id = $accident_order->id;
            $main_car = Car::find($item->main_car_id);
            $item->main_car = $main_car->license_plate;
            $item->replacement_url = route('admin.replacement-cars.show', ['replacement_car' => $item->id]);
            $item->replacement_type_text = __('accident_informs.replace_type_' . $item->replacement_type);
            $slide = Slide::find($item->slide_id);
            if ($slide) {
                $slide_worksheet_no = $slide->worksheet_no;
            } else {
                $slide_worksheet_no = null;
            }
            $item->customer_receive_text = ($item->is_cust_receive_replace) && $item->is_cust_receive_replace == STATUS_ACTIVE ?  __('accident_informs.customer_receive_self') : 'รถสไลด์ : ' . $slide_worksheet_no;

            $replacement_medias = $item->getMedia('replacement_car_files');
            $replacement_medias = get_medias_detail($replacement_medias);
            $replacement_medias = collect($replacement_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->replacement_files = $replacement_medias;
            $item->pending_delete_replacement_files = [];

            // if($item->job_type == Slide::class){
            //     $slide = Slide::find($item->job_id);
            //     $item->slide_id = $slide->id;
            //     $item->slide_worksheet = $slide->worksheet_no;
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE);
            //     $item->origin_place = $slide->origin_place;
            //     $item->origin_contact = $slide->origin_contact;
            //     $item->origin_tel = $slide->origin_tel;
            //     $item->destination_place = $slide->destination_place;
            //     $item->destination_contact = $slide->destination_contact;
            //     $item->destination_tel = $slide->destination_tel;
            //     $item->slide_type_id = AccidentSlideEnum::TLS_SLIDE;

            // }else{
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE);
            //     $item->slide_type_id = AccidentSlideEnum::THIRD_PARTY_SLIDE;
            // }

            return $item;
        });

        return $replacement_list;
    }

    public static function getStatusReceiveList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.receive_status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.receive_status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public function showClaim(AccidentRepairOrder $accident_order_approve)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrderApprove);
        $accident_inform = Accident::find($accident_order_approve->accident_id);
        $repair_list = [];
        $spare_part_list = AccidentTrait::getStatusList();
        $repair_list = AccidentTrait::getRepairList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $getClaimList = $this->getClaimList($accident_inform, $accident_order_approve->id);
        $claim_list_data = $getClaimList['claim_list'];
        $is_withdraw_true = $getClaimList['is_withdraw_true'];
        $tls_cost_total = $getClaimList['tls_cost_total'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = AccidentTrait::getWoundList();
        $responsible_list = AccidentTrait::getResponsibleList();
        $rights_list = AccidentTrait::getRightsList();
        $page_title =  __('accident_orders.accident_order_approve');
        // $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order_approve->id);
        // $approve_line_list = $approve_line['approve_line_list'];
        // $approve = $approve_line['approve'];
        // $approve_line_logs = $approve_line['approve_line_logs'];
        // if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(AccidentRepairOrder::class, $accident_order_approve->id,ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER);
        // } else {
        //     $approve_line_owner = null;
        // }
        return view('admin.accident-order-approves.form-claim-edit',  [
            'page_title' => $page_title,
            'd' => $accident_inform,
            'accident_order' => $accident_order_approve,
            'repair_list' => $repair_list,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'claim_list_data' => $claim_list_data,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total,
            'repair_list' => $repair_list,
            'claim_type_list' => $claim_type_list,
            'responsible_list' => $responsible_list,
            'rights_list' => $rights_list,
            'view' => true,
            // 'approve_line_list' => $approve_line_list,
            // 'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            // 'approve_line_logs' => $approve_line_logs,
        ]);
    }


    public function getSlideList($accident_model)
    {
        $slide_list = AccidentSlide::where('accident_id', $accident_model->id)->get();
        $slide_list->map(function ($item) {
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->slide_driver = ($item->slide_driver) ? $item->slide_driver : '';
            $item->lift_price = ($item->slide_price) ? $item->slide_price : '';
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->lift_from = ($item->slide_from) ? $item->slide_from : '';
            $item->lift_to = ($item->slide_to) ? $item->slide_to : '';

            $slide_medias = $item->getMedia('slide');
            $slide_medias = get_medias_detail($slide_medias);
            $slide_medias = collect($slide_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->slide_files = $slide_medias;
            $item->pending_delete_slide_files = [];

            return $item;
        });

        return $slide_list;
    }

    public function getCostList($cost_model)
    {
        $cost_list = AccidentExpense::where('accident_id', $cost_model->id)->get();
        $cost_list->map(function ($item) {
            $item->cost_name = ($item->list) ? $item->list : '';
            $item->cost_price = ($item->price) ? $item->price : '';
            $item->cost_remark = ($item->remark) ? $item->remark : '';
            $item->cost_date = ($item->created_at) ? $item->created_at : '';
            return $item;
        });

        return $cost_list;
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
}
