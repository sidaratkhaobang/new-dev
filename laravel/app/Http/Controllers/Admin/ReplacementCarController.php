<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\ReplacementCarStatusEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\Repair;
use App\Models\ReplacementCar;
use App\Traits\ReplacementCarTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use ConfigApproveTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\RentalTypeEnum;
use App\Traits\HistoryTrait;

class ReplacementCarController extends Controller
{
    use ReplacementCarTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementCar);
        $s = $request->s;
        $worksheet_id = $request->worksheet_id;
        $worksheet_name = null;
        $main_car_id = $request->main_car_id;
        $replacement_car_id = $request->replacement_car_id;
        $main_car_license_plate = null;
        $replacement_car_license_plate = null;

        if ($worksheet_id) {
            $replacement_car = ReplacementCar::find($worksheet_id);
            $worksheet_name = $replacement_car ? $replacement_car->worksheet_no : '';
        }

        if ($main_car_id) {
            $car = Car::find($main_car_id);
            $main_car_license_plate = $car ? $car->license_plate : '';
        }
        if ($replacement_car_id) {
            $car = Car::find($replacement_car_id);
            $replacement_car_license_plate = $car ? $car->license_plate : '';
        }

        $list = ReplacementCar::search($s, $request)
            ->sortable(['created_at' => 'desc'])
            ->paginate(PER_PAGE);
        foreach ($list as $item) {
            $item->job_worksheet_no = null;
            if ($item->job_type === ReplacementJobTypeEnum::REPAIR) {
                $repair = Repair::find($item->job_id);
                $item->job_worksheet_no = $repair ? $repair->worksheet_no : null;
            }
            if ($item->job_type === ReplacementJobTypeEnum::ACCIDENT) {
                $accident = Accident::find($item->job_id);
                $item->job_worksheet_no = $accident ? $accident->worksheet_no : null;
            }
        }
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $page_title = __('replacement_cars.main_page_title');
        return view('admin.replacement-cars.index', [
            's' => $s,
            'replacement_type' => $request->replacement_type,
            'job_type' => $request->job_type,
            'worksheet_id' => $request->worksheet_id,
            'worksheet_name' => $worksheet_name,
            'main_car_id' => $main_car_id,
            'main_car_license_plate' => $main_car_license_plate,
            'replacement_car_id' => $replacement_car_id,
            'replacement_car_license_plate' => $replacement_car_license_plate,
            'list' => $list,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function edit(ReplacementCar $replacement_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCar);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();
        $replacement_car_files = $replacement_car->getMedia('replacement_car_documents');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $main_car = ReplacementCarTrait::getCarInfo($replacement_car->main_car_id);
        $replace_car = ReplacementCarTrait::getCarInfo($replacement_car->replacement_car_id);
        $available_replacement_car = [];
        $required_lower_spec = true;
        if (!$replace_car) {
            $available_replacement_car = $this->getAvailableReplacementCars();
            // $avalable_cars = false;
            if (!$available_replacement_car) {
                $replace_car = (object) [];
                $required_lower_spec = true;
            } else {
                $replace_car = (object) [];
                // $replace_car = ReplacementCarTrait::getCarInfo($avalable_cars[0]->id);
            }
        }
        // dd($avalable_cars);
        $approve_line = HistoryTrait::getHistory(ReplacementCar::class, $replacement_car->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(ReplacementCar::class, $replacement_car->id);
        } else {
            $approve_line_owner = null;
        }

        $update_status_only = in_array($replacement_car->status, [
            ReplacementCarstatusEnum::PENDING,
            ReplacementCarstatusEnum::IN_PROCESS,
            ReplacementCarstatusEnum::COMPLETE
        ]);
        $update_status_list = ReplacementCarTrait::getReplacementUpdateStatusList();
        $route_uri = route('admin.replacement-cars.store');
        $page_title = __('lang.edit') . __('replacement_cars.page_title');
        return view('admin.replacement-cars.form', [
            'd' => $replacement_car,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => $replacement_car_files,
            'main_car' => $main_car,
            'replacement_car' => $replace_car,
            'route_uri' => $route_uri,
            'mode' => MODE_UPDATE,
            'available_replacement_car' => $available_replacement_car,
            'required_lower_spec' => $required_lower_spec,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'update_status_list' => $update_status_list,
            'allow_update_status' => true,
            'update_status_only' => $update_status_only
        ]);
    }

    public function show(ReplacementCar $replacement_car)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementCar);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();
        $replacement_car_files = $replacement_car->getMedia('replacement_car_documents');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $main_car = ReplacementCarTrait::getCarInfo($replacement_car->main_car_id);
        $replace_car = ReplacementCarTrait::getCarInfo($replacement_car->replacement_car_id);
        $available_replacement_car = [];
        $required_lower_spec = false;
        if (!$replace_car) {
            $avalable_cars = $this->getAvailableReplacementCars();
            if (!$avalable_cars) {
                $replace_car = (object) [];
                $required_lower_spec = true;
            } else {
                $replace_car = ReplacementCarTrait::getCarInfo($avalable_cars[0]->id);
            }
        }
        $route_uri = route('admin.replacement-cars.store');
        $page_title = __('lang.edit') . __('replacement_cars.page_title');
        $approve_line = HistoryTrait::getHistory(ReplacementCar::class, $replacement_car->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(ReplacementCar::class, $replacement_car->id);
        } else {
            $approve_line_owner = null;
        }
        $update_status_only = in_array($replacement_car->status, [
            ReplacementCarstatusEnum::PENDING,
            ReplacementCarstatusEnum::IN_PROCESS,
            ReplacementCarstatusEnum::COMPLETE
        ]);
        $update_status_list = ReplacementCarTrait::getReplacementUpdateStatusList();
        return view('admin.replacement-cars.form', [
            'd' => $replacement_car,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => $replacement_car_files,
            'main_car' => $main_car,
            'replacement_car' => $replace_car,
            'route_uri' => $route_uri,
            'mode' => MODE_VIEW,
            'available_replacement_car' => $available_replacement_car,
            'required_lower_spec' => $required_lower_spec,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'update_status_list' => $update_status_list,
            'allow_update_status' => true,
            'update_status_only' => $update_status_only
        ]);
    }

    public function getReplacementCarDetail(Request $request)
    {
        $id = $request->id;
        $car = ReplacementCarTrait::getCarInfo($id);
        return response()->json($car);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCar);
        $replacement_car = ReplacementCar::find($request->id);
        if (!$replacement_car) {
            return;
        }
        if ($replacement_car->status == ReplacementCarStatusEnum::PENDING_INSPECT) {
            $validator = Validator::make($request->all(), [
                'replacement_date' => 'required',
                'replacement_place' => 'required',
                'replacement_car_id' => 'required'
            ], [], [
                    'replacement_date' => __('replacement_cars.replacement_date'),
                    'replacement_place' => __('replacement_cars.replacement_place'),
                    'replacement_car_id' => __('replacement_cars.replacement_info'),
                ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if ($request->replacement_place) {
            $replacement_car->replacement_place = $request->replacement_place;
        }
        if ($request->replacement_date) {
            $replacement_car->replacement_date = $request->replacement_date;
        }
        if ($request->replacement_car_id) {
            $replacement_car->replacement_car_id = $request->replacement_car_id;
        }

        if (isset($request->is_spec_low[0])) {
            $validator = Validator::make($request->all(), [
                'spec_low_reason' => 'required',
            ], [], [
                    'spec_low_reason' => __('replacement_cars.spec_low_reason'),
                ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $replacement_car->is_spec_low = STATUS_ACTIVE;
            $replacement_car->spec_low_reason = $request->spec_low_reason;
        }
        $replacement_car->status = ReplacementCarStatusEnum::PENDING;
        if ($request->replacement_car_id && isset($request->is_spec_low[0])) {

            $step_approve_management = new StepApproveManagement();
            $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::REPLACEMENT_CAR);
            if (!$is_configured) {
                return $this->responseWithCode(false, __('lang.config_approve_warning') . __('replacement_cars.page_title'), null, 422);
            }
            $replacement_car->status = ReplacementCarStatusEnum::PENDING_REVIEW;
        }
        if (!empty($request->status)) {
            $replacement_car->status = $request->status;
        }
        $replacement_car->save();

        if (strcmp($replacement_car->status, ReplacementCarStatusEnum::PENDING) == 0) {
            // if ($replacement_car->is_need_driver) {
            //     ReplacementCarTrait::createDrivingJobByReplacementType($replacement_car);
            // }
            ReplacementCarTrait::createDrivingJobByReplacementType($replacement_car);
            ReplacementCarTrait::createInspectionJobByReplacementType($replacement_car);
            ReplacementCarTrait::createGPSCheckSignalByReplacementType($replacement_car);
        }

        if (strcmp($replacement_car->status, ReplacementCarStatusEnum::PENDING_REVIEW) == 0) {
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::REPLACEMENT_CAR, ReplacementCar::class, $replacement_car->id);
        }
        $redirect_route = route('admin.replacement-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getAvailableReplacementCars($random = true)
    {
        // $replacement_car = Car::where('rental_type', RentalTypeEnum::REPLACEMENT)->get();
        // $replacement_car->map(function ($item) {
        //     $item->id = $item->id;
        //     $item->text = $item->license_plate;
        // });
        // return $replacement_car;

        $is_used_replacement_cars = ReplacementCar::whereNotNull('replacement_car_id')
            ->select('replacement_car_id')->pluck('replacement_car_id')->toArray();
        $replacement_car = Car::where('status', CarEnum::READY_TO_USE)
            ->where('rental_type', RentalTypeEnum::REPLACEMENT)
            ->whereNotIn('id', $is_used_replacement_cars)
            ->first();

        if (!$replacement_car) {
            $rand_num = rand(0, 9999);
            $replacement_car = new Car();
            $replacement_car->code = 'RAND1' . $rand_num;
            $replacement_car->license_plate = 'ทด ' . $rand_num;
            $replacement_car->engine_no = 'RC-E' . $rand_num;
            $replacement_car->chassis_no = 'RC-C' . $rand_num;
            $replacement_car->status = CarEnum::READY_TO_USE;
            $replacement_car->rental_type = RentalTypeEnum::REPLACEMENT;
            $replacement_car->car_class_id = CarClass::inRandomOrder()->first()->id;
            $replacement_car->car_color_id = CarColor::inRandomOrder()->first()->id;
            $replacement_car->save();
        }
        // $replacement_car = Car::where('license_plate', 'ทด 1112')->get();
        $replacement_car = Car::where('status', CarEnum::READY_TO_USE)
            ->where('rental_type', RentalTypeEnum::REPLACEMENT)
            ->whereNotIn('id', $is_used_replacement_cars)
            ->get();

        $replacement_car->map(function ($item) {
            $item->name = $item->license_plate;
        });
        return $replacement_car;
    }

    public function printPdf(Request $request)
    {
        $replacement_job_id = $request->replacement_job_id;
        $worksheet_type = $request->worksheet_type;
        $replacement_car = ReplacementCar::find($replacement_job_id);
        if (!$replacement_car) {
            return abort(404);
        }

        $car_id = $this->getCarForWorksheet($worksheet_type, $replacement_car);
        if (!$car_id) {
            return abort(404);
        }
        $car = Car::find($car_id);
        if (!$car) {
            return abort(404);
        }
        $data = [];
        $data['worksheet_name'] = __('replacement_cars.worksheet_name_' . $worksheet_type . '_' . $replacement_car->replacement_type);
        $data['worksheet_no'] = $replacement_car->worksheet_no;
        $data['customer_name'] = $replacement_car->customer_name;
        $data['customer_address'] = '';
        $data['customer_tel'] = $replacement_car->customer_tel;
        $data['customer_fax'] = '';
        $data['contact_name'] = '';
        $data['contract_no'] = '';
        $data['period_of_time'] = '';
        $data['insurance_company'] = '';
        $data['contact_start_date'] = '';
        $data['contact_end_date'] = '';
        $data['policy_no'] = '';
        $data['insurance_start'] = '';
        $data['insurance_end'] = '';
        $data['car_class'] = $car->carClass?->name;
        $data['year_mfg'] = '';
        $data['car_color'] = $car->carColor?->name;
        $data['car_status'] = __('cars.status_' . $car->status);
        $data['chassis_no'] = $car->chassis_no;
        $data['engine_no'] = $car->engine_no;
        $data['license_plate'] = $car->license_plate;
        $data['mile_no'] = '';
        $data['fuel_tank'] = $car->oil_tank_capacity;
        $data['delivery_place'] = $replacement_car->replacement_place;
        $data['delivery_date'] = $replacement_car->replacement_date ? get_thai_date_format($replacement_car->replacement_date, 'd/m/Y H:i') : '';
        $data['user_name'] = '';
        $pdf = PDF::loadView(
            'admin.layouts.pdf.worksheet',
            [
                'data' => $data
            ]
        );
        return $pdf->stream();
    }

    public function getCarForWorksheet($worksheet_type, $replacement_car)
    {
        $car_id = null;
        if ($worksheet_type == 'SEND') {
            if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, ReplacementTypeEnum::SEND_REPLACE])) {
                $car_id = $replacement_car->replacement_car_id;
            }
            if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE, ReplacementTypeEnum::SEND_MAIN])) {
                $car_id = $replacement_car->main_car_id;
            }
        }
        if ($worksheet_type == 'RECEIVE') {
            if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, ReplacementTypeEnum::RECEIVE_MAIN])) {
                $car_id = $replacement_car->main_car_id;
            }
            if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE, ReplacementTypeEnum::RECEIVE_REPLACE])) {
                $car_id = $replacement_car->replacement_car_id;
            }
        }
        return $car_id;
    }
}