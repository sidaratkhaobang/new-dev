<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\Resources;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Creditor;
use App\Models\Customer;
use App\Models\DealerCheckCar;
use App\Models\LongTermRental;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LongTermRentalSpecCheckCarController extends Controller
{
    use RentalTrait, CustomerTrait, LongTermRentalTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecCheckCar);
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;
        $status = $request->status;

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
            ->whereIn('lt_rentals.status', [LongTermRentalStatusEnum::SPECIFICATION, LongTermRentalStatusEnum::CANCEL])
            ->whereIn('lt_rentals.spec_status', [SpecStatusEnum::REJECT, SpecStatusEnum::PENDING_CHECK, SpecStatusEnum::CHANGE_CAR, SpecStatusEnum::NO_DELIVERY])
            ->branch()
            ->search($request->s, $request)->paginate(PER_PAGE);

        $customer_list = LongTermRental::select('id', 'customer_name as name')
            ->bySpecStatus()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
            ->bySpecStatus()->get();

        $status_lists = $this->getStatusList();

        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        $spec_status_list = RentalTrait::getSpecStatusList();
        return view('admin.long-term-rental-spec-check-cars.index', [
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
            'status_lists' => $status_lists,
            'status' => $status,
        ]);
    }

    public static function getStatusList()
    {
        $status_lists = collect([
            (object)[
                'id' => SpecStatusEnum::DRAFT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::DRAFT),
                'value' => SpecStatusEnum::DRAFT,
            ],
            (object)[
                'id' => SpecStatusEnum::REJECT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::REJECT),
                'value' => SpecStatusEnum::REJECT,
            ],
            (object)[
                'id' => SpecStatusEnum::PENDING_CHECK,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::PENDING_CHECK),
                'value' => SpecStatusEnum::PENDING_CHECK,
            ],
            (object)[
                'id' => SpecStatusEnum::CHANGE_CAR,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::CHANGE_CAR),
                'value' => SpecStatusEnum::CHANGE_CAR,
            ],
        ]);
        return $status_lists;
    }

    public function store(Request $request)
    {

        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecCheckCar);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::LT_SPEC_ACCESSORY);
        if (!$is_configured) {
            return $this->responseWithCode(false, __('lang.config_approve_warning') . __('long_term_rentals.spec_car_accessory'), null, 422);
        }
        $lt_rental_tor = LongTermRentalTor::select('id')->where('lt_rental_id', $request->id)->get();
        $lt_rental_tor_line = LongTermRentalTorLine::whereIn('lt_rental_tor_id', $lt_rental_tor)->get();

//      Validate Car If  check_delivery == 1
        $validator = Validator::make($request->all(), [
            'ready_to_delivery' => 'required_if:check_delivery,' . STATUS_ACTIVE

        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseWithCode(false, "กรุณาเลือกรถที่พร้อมส่งมอบ", null, 422);
        }
        $lt_rental_id = $request->id;
        $lt_rental = LongTermRental::find($lt_rental_id);
        if ($lt_rental) {
            $temp_status = $lt_rental->spec_status;
        }
        $lt_rental = LongTermRental::find($lt_rental_id);
        $tor_line_check_input = $request->tor_line_check_input;
        $tor_line_list = $this->getRentalTorLinesFromRentalId($lt_rental_id);
        $is_required_check_accessory = false;
        foreach ($tor_line_list as $key => $lt_tor_line) {
            $lt_tor_line->is_rental_line = STATUS_DEFAULT;
            if ($tor_line_check_input) {
                if (in_array($lt_tor_line->id, $tor_line_check_input)) {
                    $lt_tor_line->is_rental_line = STATUS_ACTIVE;
                }
            }

            if ($lt_tor_line->have_accessories) {
                $is_required_check_accessory = true;
            }
            $lt_tor_line->save();
            if ($lt_tor_line->tor_id) {
                $long_term_car_accessory = $this->saveCarAccessory($request);
            }
        }
        if (isset($request->ready_to_delivery)) {
            $lt_rental_tor_line_uncheck = DB::table('lt_rental_tor_lines')->update(['check_delivery' => STATUS_DEFAULT]);
            foreach ($request->ready_to_delivery as $key => $data) {
                $lt_rental_tor_line = LongTermRentalTorLine::find($key);
                $lt_rental_tor_line->check_delivery = STATUS_ACTIVE;
                $lt_rental_tor_line->save();
            }
        }


        if (strcmp($request->spec_status, SpecStatusEnum::PENDING_CHECK) === 0) {

            if (strcmp($request->check_delivery, STATUS_ACTIVE) === 0) {
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_SPEC_ACCESSORY, LongTermRental::class, $lt_rental_id);
                if ($is_required_check_accessory) {
                    $status = SpecStatusEnum::ACCESSORY_CHECK;
                } else {
                    $status = SpecStatusEnum::PENDING_REVIEW;
                }
            } else if (strcmp($request->check_delivery, STATUS_DEFAULT) === 0) {
                $status = SpecStatusEnum::NO_DELIVERY;
                $lt_rental->status = LongTermRentalStatusEnum::CANCEL;
            } else if (strcmp($request->check_delivery, STATUS_INACTIVE) === 0) {
                $status = SpecStatusEnum::CHANGE_CAR;
            } else {
                $status = SpecStatusEnum::PENDING_CHECK;
            }
            $lt_rental->spec_status = $status;
        }
        $lt_rental->check_delivery = $request->check_delivery;
        $lt_rental->reason_delivery = $request->reason_delivery;
        $this->sendNotificationSpecCheck($request, $lt_rental, $is_required_check_accessory, $lt_rental?->worksheet_no);

        $lt_rental->save();

        $redirect_route = route('admin.long-term-rental.spec-check-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveCarAccessory($request)
    {
        if (!empty($request->cars)) {
            foreach ($request->cars as $car_index => $request_car) {
                if (isset($request_car['id'])) {
                    $lt_rental_tor_line = LongTermRentalTorLine::find($request_car['id']);
                } else {
                    $lt_rental_tor_line = new LongTermRentalTorLine();
                }
                LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $lt_rental_tor_line->id)->delete();
                if (isset($request_car['accessory']) && sizeof($request_car['accessory']) > 0) {
                    foreach ($request_car['accessory'] as $accessory_key => $accessory) {
                        $long_term_accessory = new LongTermRentalTorLineAccessory();
                        $long_term_accessory->lt_rental_tor_line_id = $lt_rental_tor_line->id;
                        $long_term_accessory->accessory_id = $accessory['id'];
                        $long_term_accessory->amount = intval($accessory['amount']);
                        $long_term_accessory->amount_per_car = intval($accessory['amount_per_car']);
                        $long_term_accessory->tor_section = isset($accessory['tor_section']) ? $accessory['tor_section'] : null;
                        $long_term_accessory->remark = $accessory['remark'];
                        $long_term_accessory->type_accessories = $accessory['type_accessories'];
                        $long_term_accessory->save();
                    }
                }
            }
        }
        return true;
    }

    public function sendNotificationSpecCheck($dataRequest, $modelLongtermRental, $is_required_check_accessory, $dataWorkSheetNo)
    {

        $requestCheckDelivery = $dataRequest?->check_delivery;


        if ($requestCheckDelivery === "1" && $is_required_check_accessory == false) {
            NotificationTrait::sendNotificationSpecAccessoryApprove($modelLongtermRental?->id,$modelLongtermRental,$dataWorkSheetNo);
        } else
            if ($requestCheckDelivery === "1" && $is_required_check_accessory == true) {

                $dataDepartment = [
                    DepartmentEnum::PCD_PURCHASE,
                ];
                $url = route('admin.long-term-rental.specs.accessories.edit', ['rental' => $modelLongtermRental]);
                $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $notiTypeChange = new NotificationManagement('ตีสเปคอุปกรณ์', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาตีสเปคอุปกรณ์', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
                $notiTypeChange->send();
            }

    }

    public function show(LongTermRental $rental, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecCheckCar);
        $lt_rental_id = $rental->id;
        $long_term_rental = LongTermRental::find($lt_rental_id);
        if (empty($long_term_rental)) {
            return redirect()->route('admin.long-term-rentals.index');
        }
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
        $tor_line_list = $this->getRentalTorLinesFromRentalId($lt_rental_id);
        $tor_line_list->map(function ($item) use ($lt_rental_id) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            $lt_rental_tor_line = LongTermRentalTor::find($item->lt_rental_tor_id);
            $dealer_check_cars = DealerCheckCar::where('lt_rental_id', $lt_rental_id)
                ->where('tor_line_id', $item->id)->get();
            $dealer_arr = [];
            foreach ($dealer_check_cars as $data) {
                $tor_line = LongTermRentalTorLine::find($data->tor_line_id);
                $dealer = Creditor::find($data->dealer_id);
                $dealer_check_cars_list = [];
                $dealer_check_cars_list['dealer_email'] = $dealer->email;
                $dealer_check_cars_list['dealer_id'] = $data->dealer_id;
                $dealer_check_cars_list['amount'] = $data->amount;
                $dealer_check_cars_list['remark'] = $data->remark;
                $dealer_check_cars_list['is_ready_to_deliver'] = $data->is_ready_to_deliver;
                $dealer_check_cars_list['delivery_month_year'] = $data->delivery_month_year ? date('m/Y', strtotime($data->delivery_month_year)) : '';
                $dealer_check_cars_list['response_date'] = $data->response_date ? date('d/m/Y', strtotime($data->response_date)) : '';
                $tor_line = LongTermRentalTorLine::find($data->tor_line_id);
                $dealer_check_cars_list['dealer'] = ($data->Creditor) ? $data->Creditor->name : '';
                $dealer_check_cars_list['car_class_text'] = ($tor_line->carClass) ? $tor_line->carClass->full_name . ' - ' . $tor_line->carClass->name : '';
                if (count($dealer_check_cars_list) > 0) {
                    $dealer_arr[] = $dealer_check_cars_list;
                }
            }
            $item->dealer = $dealer_arr;
            $item->dealer_check_cars = [];
            $item->require_date_text = get_thai_date_format($item->actual_delivery_date, 'j F Y');
            $item->customer_require = date("m/Y", strtotime($item->actual_delivery_date));
        });

        $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
        $car_accessory = [];
        $index = 0;
        foreach ($tor_line_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }
        $is_haves = $this->getHaveAccessory();

        if (isset($request->create)) {
            $redirect_route = route('admin.long-term-rentals.edit', ['long_term_rental' => $rental]);
        } else {
            $redirect_route = route('admin.long-term-rental.spec-check-cars.index');
        }

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

        $page_title = __('lang.view') . __('long_term_rentals.check_car');
        return view('admin.long-term-rental-spec-check-cars.form', [
            'd' => $long_term_rental,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'redirect_route' => $redirect_route,
            'car_list' => $tor_line_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'is_haves' => $is_haves,
            'car_accessory' => $car_accessory,
            'view_only' => true,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'month_list' => $month_list,
            'month' => $month,
        ]);
    }

    public static function getHaveAccessory()
    {
        $is_haves = collect([
            (object)[
                'id' => 1,
                'name' => __('lang.have'),
                'value' => 1,
            ],
            (object)[
                'id' => 0,
                'name' => __('lang.no_have'),
                'value' => 0,
            ],
        ]);
        return $is_haves;
    }

    public function edit(LongTermRental $rental, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecCheckCar);
        $lt_rental_id = $rental->id;
        $long_term_rental = LongTermRental::find($lt_rental_id);
        if (empty($long_term_rental)) {
            return redirect()->route('admin.long-term-rentals.index');
        }
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
        $tor_line_list = $this->getRentalTorLinesFromRentalId($lt_rental_id);

        $tor_line_list->map(function ($item) use ($lt_rental_id) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            $lt_rental_tor_line = LongTermRentalTor::find($item->lt_rental_tor_id);
            $dealer_check_cars = DealerCheckCar::where('lt_rental_id', $lt_rental_id)
                ->where('tor_line_id', $item->id)->get();

            $dealer_arr = [];
            foreach ($dealer_check_cars as $data) {
                $tor_line = LongTermRentalTorLine::find($data->tor_line_id);
                $dealer = Creditor::find($data->dealer_id);
                $dealer_check_cars_list = [];
                $dealer_check_cars_list['dealer_email'] = $dealer->email;
                $dealer_check_cars_list['dealer_id'] = $data->dealer_id;
                $dealer_check_cars_list['amount'] = $data->amount;
                $dealer_check_cars_list['remark'] = $data->remark;
                $dealer_check_cars_list['is_ready_to_deliver'] = $data->is_ready_to_deliver;
                $dealer_check_cars_list['delivery_month_year'] = $data->delivery_month_year ? date('m/Y', strtotime($data->delivery_month_year)) : '';
                $dealer_check_cars_list['response_date'] = $data->response_date ? date('d/m/Y', strtotime($data->response_date)) : '';
                $tor_line = LongTermRentalTorLine::find($data->tor_line_id);
                $dealer_check_cars_list['dealer'] = ($data->Creditor) ? $data->Creditor->name : '';
                $dealer_check_cars_list['car_class_text'] = ($tor_line->carClass) ? $tor_line->carClass->full_name . ' - ' . $tor_line->carClass->name : '';
                if (count($dealer_check_cars_list) > 0) {
                    $dealer_arr[] = $dealer_check_cars_list;
                }
            }
            $item->dealer = $dealer_arr;
            $item->dealer_check_cars = [];
            $item->require_date_text = get_thai_date_format($item->actual_delivery_date, 'j F Y');
            $item->customer_require = date("m/Y", strtotime($item->actual_delivery_date));
        });

        $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
        $car_accessory = [];
        $index = 0;
        foreach ($tor_line_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }
        $is_haves = $this->getHaveAccessory();

        if (isset($request->create)) {
            $redirect_route = route('admin.long-term-rentals.edit', ['long_term_rental' => $rental]);
        } else {
            $redirect_route = route('admin.long-term-rental.spec-check-cars.index');
        }
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
            $approve_line_owner = $approve_line_owner->checkCanApprove(LongTermRental::class, $rental->id);
        } else {
            $approve_line_owner = null;
        }

        $month_list = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();

        $page_title = __('lang.edit') . __('long_term_rentals.check_car');
        return view('admin.long-term-rental-spec-check-cars.form', [
            'd' => $long_term_rental,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'redirect_route' => $redirect_route,
            'car_list' => $tor_line_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'is_haves' => $is_haves,
            'car_accessory' => $car_accessory,
            // 'dealer_check_cars' => $dealer_check_cars,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'month_list' => $month_list,
            'month' => $month,
        ]);
    }
}
