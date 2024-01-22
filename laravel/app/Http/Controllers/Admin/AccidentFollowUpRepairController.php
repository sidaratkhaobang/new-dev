<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentRepairOrder;
use App\Models\Amphure;
use App\Models\Car;
use App\Models\Cradle;
use App\Models\District;
use App\Models\FollowAccidentRepair;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Traits\AccidentTrait;
use App\Traits\RepairTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccidentFollowUpRepairController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentFollowUpRepair);
        $worksheet = $request->repair_worksheet_no;
        $accident_worksheet_no = $request->accident_worksheet_no;
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
            ->whereIn('accident_repair_orders.status',[AccidentRepairStatusEnum::PROCESS_REPAIR,AccidentRepairStatusEnum::SUCCESS_REPAIR])
            ->sortable(['worksheet_no' => 'desc'])
            ->search($request)
            ->select('accident_repair_orders.*', 'accidents.worksheet_no as accident_worksheet', 'accidents.case', 'cradles.name as cradle_name', 'cars.id as car_id')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $follow_up_repair = FollowAccidentRepair::where('accident_repair_order_id', $item->id)
                ->get();

            if ($follow_up_repair) {
                $follow_up_repair_last = $follow_up_repair->last();
                if ($follow_up_repair_last) {
                    $item->follow_up_status = __('accident_follow_up_repairs.repair_status_' . $follow_up_repair_last->follow_up_status);
                }
            }

            $item->over_complete_date = null;
            if ($item->repair_date && $item->amount_completed && $item->actual_repair_date) {
                $day = $item->amount_completed;
                $scheduled_completion_date = Carbon::parse($item->repair_date)->addDays($day);
                $item->scheduled_completion_date = $scheduled_completion_date->format('d/m/Y');
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
                } else if ($car->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
                $item->license_plate = $text;
            }
            return $item;
        });

        $repair_status_list = AccidentTrait::getAccidentRepairStatus();

        return view('admin.accident-follow-up-repairs.index', [
            'list' => $list,
            'license_plate' => $license_plate,
            'worksheet' => $worksheet,
            'accident_worksheet_no' => $accident_worksheet_no,
            'worksheet_text' => $worksheet_text,
            'repair_status_list' => $repair_status_list,
            'status' => $status,
            'license_plate_text' => $license_plate_text,
            'accident_worksheet_text' => $accident_worksheet_text,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'follow_up.*.follow_up_status' => [
                'required',
            ],
            'follow_up.*.received_data_date' => [
                'required',
            ],
        ], [], [
            'follow_up.*.follow_up_status' => __('accident_follow_up_repairs.repair_status'),
            'follow_up.*.received_data_date' => __('accident_follow_up_repairs.recieve_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->follow_up) {
            foreach ($request->follow_up as $index => $item) {
                $follow_up = FollowAccidentRepair::firstOrNew(['id' => $item['id']]);
                $follow_up->accident_repair_order_id = $request->id;
                $follow_up->follow_up_status = $item['follow_up_status'];
                $format = "d/m/Y";
                $dateTime = DateTime::createFromFormat($format, $item['received_data_date']);
                if ($dateTime) {
                    $received_data_date = $dateTime->format('Y-m-d H:i:s');
                    $follow_up->received_data_date = $received_data_date;
                }

                $follow_up->problem = $item['problem'];
                $follow_up->solution = $item['solution'];
                $follow_up->save();
            }
        }

        $redirect_route = route('admin.accident-follow-up-repairs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(AccidentRepairOrder $accident_follow_up_repair)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentFollowUpRepair);
        $accident = Accident::find($accident_follow_up_repair->accident_id);
        $car_data = RepairTrait::getDataCar($accident->car_id);
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
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident_follow_up_repair);
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
        $repair_status_list = AccidentTrait::getRepairStatusList();

        $province = Province::find($accident->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;


        $repair_list = FollowAccidentRepair::where('accident_repair_order_id', $accident_follow_up_repair->id)->get();
        $repair_list->map(function ($item) {
            $item->is_saved = true;
            $format = "Y-m-d H:i:s";
            $received_data_date = DateTime::createFromFormat($format, $item->received_data_date);
            if ($received_data_date) {
                $item->received_data_date = $received_data_date->format('d/m/Y');
            }

            $item->repair_status_text =  __('accident_follow_up_repairs.repair_status_' . $item->follow_up_status);
        });

        $cradle = Cradle::find($accident->cradle);
        $page_title = $page_title = __('lang.view') . __('accident_follow_up_repairs.page_title');
        return view('admin.accident-follow-up-repairs.form',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident_follow_up_repair,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
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
            'repair_status_list' => $repair_status_list,
            'repair_list' => $repair_list,
            'view' => true,
        ]);
    }

    public function edit(AccidentRepairOrder $accident_follow_up_repair)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentFollowUpRepair);
        $accident = Accident::find($accident_follow_up_repair->accident_id);
        $car_data = RepairTrait::getDataCar($accident->car_id);
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
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident_follow_up_repair);
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
        $repair_status_list = AccidentTrait::getRepairStatusList();

        $province = Province::find($accident->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;


        $repair_list = FollowAccidentRepair::where('accident_repair_order_id', $accident_follow_up_repair->id)->get();
        $repair_list->map(function ($item) {
            $item->is_saved = true;
            $format = "Y-m-d H:i:s";
            $received_data_date = DateTime::createFromFormat($format, $item->received_data_date);
            if ($received_data_date) {
                $item->received_data_date = $received_data_date->format('d/m/Y');
            }

            $item->repair_status_text =  __('accident_follow_up_repairs.repair_status_' . $item->follow_up_status);
        });

        $cradle = Cradle::find($accident->cradle);
        $page_title = $page_title = __('lang.edit') . __('accident_follow_up_repairs.page_title');
        return view('admin.accident-follow-up-repairs.form',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident_follow_up_repair,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
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
            'repair_status_list' => $repair_status_list,
            'repair_list' => $repair_list,
        ]);
    }
}
