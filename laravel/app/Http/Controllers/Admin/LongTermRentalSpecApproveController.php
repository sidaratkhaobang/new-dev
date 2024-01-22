<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\DepartmentEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\Resources;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use App\Models\LongTermRentalType;
use App\Traits\CustomerTrait;
use App\Traits\HistoryTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;

class LongTermRentalSpecApproveController extends Controller
{
    use CustomerTrait, LongTermRentalTrait, RentalTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecApprove);
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;

        $lists = LongTermRental::sortable(['created_at' => 'desc'])
            ->select(
                'lt_rentals.id',
                'lt_rentals.worksheet_no',
                'lt_rentals.spec_status',
                'lt_rentals.customer_name',
                'lt_rentals.offer_date',
                'lt_rentals.status',
                'lt_rental_types.name as rental_type'
            )
            ->leftJoin('lt_rental_types', 'lt_rental_types.id', '=', 'lt_rentals.lt_rental_type_id')
            ->whereIn('lt_rentals.spec_status', [SpecStatusEnum::PENDING_REVIEW, SpecStatusEnum::CONFIRM, SpecStatusEnum::REJECT])
            ->when($spec_status_id, function ($query) use ($spec_status_id) {
                return $query->where('lt_rentals.spec_status', $spec_status_id);
            })
            ->orderBy('lt_rentals.worksheet_no')
            ->branch()
            ->search($request->s, $request)->paginate(PER_PAGE);

        $spec_status_list = RentalTrait::getApproveSpecStatusList();
        $customer_list = LongTermRental::select('id', 'customer_name as name')
            ->bySpecStatusApprove()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
            ->bySpecStatusApprove()->get();

        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        return view('admin.long-term-rental-spec-approve.index', [
            's' => $request->s,
            'lists' => $lists,
            'customer_id' => $customer_id,
            'customer_list' => $customer_list,
            'worksheet_list' => $worksheet_list,
            'worksheet_id' => $worksheet_id,
            'spec_status_id' => $spec_status_id,
            'spec_status_list' => $spec_status_list,
            'lt_rental_type' => $lt_rental_type,
            'lt_rental_type_list' => $lt_rental_type_list,
            'from_offer_date' => $request->from_offer_date,
            'to_offer_date' => $request->to_offer_date,
        ]);
    }

    public function show(LongTermRental $rental)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecApprove);
        $long_term_rental_id = $rental->id;
        $long_term_rental = LongTermRental::find($long_term_rental_id);
        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);

        $customer_code = null;
        if ($long_term_rental->customer_id) {
            $customer = Customer::find($long_term_rental->customer_id);
            $customer_code = $customer->customer_code;
            $long_term_rental->customer_type = $customer->customer_type;
        }
        $customer_type_list = CustomerTrait::getCustomerType();
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);

        $tor_line_list = LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tor_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
            )
            ->where('lt_rental_tor_lines.check_delivery', STATUS_ACTIVE)
            ->where('lt_rentals.id', $long_term_rental_id)
            ->orderBy('tor_id')
            ->get();


        $tor_line_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });


        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(LongTermRental::class, $rental->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }
        $approve_line = HistoryTrait::getHistory(LongTermRental::class, $rental->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(LongTermRental::class, $rental->id);
        } else {
            $approve_line_owner = null;
        }

        $month_list = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();

        $page_title = __('lang.view') . __('long_term_rentals.specs_and_equipment');
        return view('admin.long-term-rental-spec-approve.view', [
            'd' => $long_term_rental,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'tor_line_list' => $tor_line_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'view_only' => true,
            'approve_controller' => true,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'month_list' => $month_list,
            'month' => $month,
        ]);
    }


    public function updateSpecStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecApprove);
        $tor_line_check_input = $request->tor_line_check_input;
        if (!$tor_line_check_input || sizeof($tor_line_check_input) < 1) {
            return response()->json([
                'success' => false,
                'message' => __('lang.select_required') . __('long_term_rentals.tor_line_check_input')
            ], 422);
        }

        $lt_rental_id = $request->lt_rental_id;
        $lt_rental = LongTermRental::find($lt_rental_id);
        $tor_line_list = LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tor_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
            )
            ->where('lt_rental_tor_lines.check_delivery', STATUS_ACTIVE)
            ->where('lt_rentals.id', $lt_rental_id)
            ->orderBy('tor_id')
            ->get();
        $tor_check_array = [];
        foreach ($tor_line_list as $key => $lt_tor_line) {
            $tor_check_array[$lt_tor_line->tor_id][$lt_tor_line->id] = false;
            if (in_array($lt_tor_line->id, $tor_line_check_input)) {
                $tor_check_array[$lt_tor_line->tor_id][$lt_tor_line->id] = true;
            }
        }
        foreach ($tor_check_array as $tor) {
            $count = 0;
            foreach ($tor as $item) {
                if ($item) {
                    $count++;
                }
            }
            if ($count == 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('long_term_rentals.at_least_one_must_true')
                ], 422);
            }
        }

        foreach ($tor_line_list as $key => $lt_tor_line) {
            $lt_tor_line->is_rental_line = STATUS_DEFAULT;
            if (in_array($lt_tor_line->id, $tor_line_check_input)) {
                $lt_tor_line->is_rental_line = STATUS_ACTIVE;
            }
           $lt_tor_line->save();
        }

        // update approve step
        // $approve_update = $this->updateApprove($request, $lt_rental);

        // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $lt_rental, $request->spec_status, LongTermRental::class);
        $approve_update = $approve_update->updateApprove(LongTermRental::class, $lt_rental->id, $request->spec_status,null,$request->reject_reason);

        if (in_array($approve_update, [SpecStatusEnum::CONFIRM, SpecStatusEnum::REJECT])) {
            $lt_rental->spec_status = $approve_update;
            if (strcmp($approve_update, SpecStatusEnum::CONFIRM) == 0) {
                $lt_rental->status = LongTermRentalStatusEnum::COMPARISON_PRICE;
                $this->createRentalLines($lt_rental->id);
            }
            if (strcmp($approve_update, SpecStatusEnum::REJECT) == 0) {

                if (isset($request->reject_reason)) {
                    $lt_rental->reject_spec_reason = $request->reject_reason;
                }
            }
            $lt_rental->save();
        }

        if ($request?->spec_status == SpecStatusEnum::REJECT) {
            $this->sendNotificationReject($lt_rental, $lt_rental?->worksheet_no);
        }
        if ($request?->spec_status == SpecStatusEnum::CONFIRM) {
            NotificationTrait::sendNotificationSpecAccessoryApprove($lt_rental?->id, $lt_rental, $lt_rental->worksheet_no);
        }
        $redirect_route = route('admin.long-term-rental.specs-approve.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function createRentalLines($lt_rental_id)
    {
        $lt_rental_tor_line_list = LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->where('lt_rentals.id', $lt_rental_id)
            ->where('lt_rental_tor_lines.is_rental_line', STATUS_ACTIVE)
            ->select('lt_rental_tor_lines.*')
            ->get();
        $lt_month_list = LongTermRentalMonth::where('lt_rental_id', $lt_rental_id)->get();
        foreach ($lt_rental_tor_line_list as $key => $lt_rental_tor_line) {
            $lt_rental_line = new LongTermRentalLine();
            $lt_rental_line->lt_rental_id = $lt_rental_id;
            $lt_rental_line->car_class_id = $lt_rental_tor_line->car_class_id;
            $lt_rental_line->car_color_id = $lt_rental_tor_line->car_color_id;
            $lt_rental_line->amount = $lt_rental_tor_line->amount;
            $lt_rental_line->remark = $lt_rental_tor_line->remark;
            $lt_rental_line->have_accessories = $lt_rental_tor_line->have_accessories;
            $lt_rental_line->purchase_options = $lt_rental_tor_line->purchase_options;
            $lt_rental_line->lt_rental_tor_line_id = $lt_rental_tor_line->id;
            $lt_rental_line->save();

            // save rental line month
            foreach ($lt_month_list as $key => $lt_month) {
                $this->saveRentalLineMonths($lt_rental_line->id, $lt_month->id);
            }

            $tor_line_accessory_list = LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $lt_rental_tor_line->id)->get();
            foreach ($tor_line_accessory_list as $key => $tor_line_accessory) {
                $lt_rental_line_accessory = new LongTermRentalLineAccessory();
                $lt_rental_line_accessory->lt_rental_line_id = $lt_rental_line->id;
                $lt_rental_line_accessory->accessory_id = $tor_line_accessory->accessory_id;
                $lt_rental_line_accessory->amount = $tor_line_accessory->amount;
                $lt_rental_line_accessory->tor_section = $tor_line_accessory->tor_section;
                $lt_rental_line_accessory->remark = $tor_line_accessory->remark;
                $lt_rental_line_accessory->type_accessories = $tor_line_accessory->type_accessories;
                $lt_rental_line_accessory->save();
            }
        }
        return true;
    }

    public function sendNotificationReject($modelLongtermRental, $dataWorkSheetNo)
    {
        $dataDepartment = [
            DepartmentEnum::AMO_SALE_ADMIN,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.long-term-rental.specs-approve.show', ['rental' => $modelLongtermRental]);
        $notiTypeChange = new NotificationManagement('ไม่อนุมัติสเปครถและอุปกรณ์', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' ไม่ได้รับการอนุมัติ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [],'danger');
        $notiTypeChange->send();
    }

    public function showTor(Request $request)
    {
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->rental;
        $d = LongTermRentalTor::find($lt_rental_tor_id);
        $have_accessory_list = $this->getHaveAccessoryList();
        $tor_line_list = LongTermRentalTorLine::where('lt_rental_tor_id', $lt_rental_tor_id)
            ->get();

        $tor_line_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        // TODO
        $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
        $car_accessory = [];
        $index = 0;
        foreach ($tor_line_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        $redirect_route = route('admin.long-term-rental.specs-approve.show', ['rental' => $lt_rental_id]);
        $page_title = __('lang.view') . __('long_term_rentals.specs_and_equipment');
        return view('admin.long-term-rental-spec-tors.form', [
            'd' => $d,
            'lt_rental_id' => $lt_rental_id,
            'lt_rental_tor_id' => $lt_rental_tor_id,
            'page_title' => $page_title,
            'have_accessory_list' => $have_accessory_list,
            'car_list' => $tor_line_list,
            'car_accessory' => $car_accessory,
            'view_only' => true,
            'redirect_route' => $redirect_route
        ]);
    }
}
