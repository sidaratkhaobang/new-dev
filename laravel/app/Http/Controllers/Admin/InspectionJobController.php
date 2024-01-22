<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarPartTypeEnum;
use App\Enums\InspectionFormEnum;
use App\Enums\InspectionStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportCarLine;
use App\Models\CarParkTransfer;
use App\Models\Car;
use App\Models\CarStatus;
use App\Models\InspectionFlow;
use App\Models\InspectionStep;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionForm;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobStep;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobLine;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\TransferReasonEnum;
use App\Enums\TransferTypeEnum;
use App\Models\CarParkZone;
use App\Models\Driver;
use App\Models\LongTermRental;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\User;
use App\Traits\InspectionTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Factories\InspectionJobFactory;

class InspectionJobController extends Controller
{
    use InspectionTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarInspection);
        $list = InspectionJob::leftjoin('inspection_flows', 'inspection_flows.id', '=', 'inspection_jobs.inspection_flow_id')
            ->leftjoin('cars', 'cars.id', '=', 'inspection_jobs.car_id')
            ->leftjoin('departments', 'departments.id', '=', 'inspection_jobs.inspection_department_id')
            ->leftJoinSub(get_sub_query_car_park_zones(), 'car_park_zones', function ($join) {
                $join->on('cars.id', '=', 'car_park_zones.car_id');
            })
            //->where('car_park_zones.branch_id', get_branch_id())
            ->select(
                'inspection_jobs.*',
                'inspection_flows.name as inspection_flow_name',
                'cars.engine_no',
                'cars.license_plate',
                'cars.rental_type',
                'departments.name as user_department_name',
                'car_park_zones.car_park_number',
                'car_park_zones.zone_code'
            )
            ->sortable(['worksheet_no' => 'desc'])
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $license_plate_list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')->get();
        $license_plate_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });

        $inspection_form = $request->inspection_form;
        $worksheet_no = $request->worksheet_no;
        $inspection_must_date = $request->inspection_must_date;
        $car_park_zone = $request->car_park_zone;
        $status = $request->status;
        $car_id = $request->car_id;
        $inspection_form_list = InspectionFlow::select('id', 'name')->get();
        $worksheet_list = InspectionJob::select('id', 'worksheet_no as name')->get();
        $car_park_zone_list = CarParkZone::select('id', 'code as name')->branch()->get();
        $status_list = $this->getStatus();

        return view('admin.inspection-jobs.index', [
            'lists' => $list,
            's' => $request->s,
            'inspection_form_list' => $inspection_form_list,
            'inspection_form' => $inspection_form,
            'worksheet_list' => $worksheet_list,
            'worksheet_no' => $worksheet_no,
            'car_park_zone' => $car_park_zone,
            'inspection_must_date' => $inspection_must_date,
            'car_park_zone_list' => $car_park_zone_list,
            'status_list' => $status_list,
            'license_plate_list' => $license_plate_list,
            'status' => $status,
            'car_id' => $car_id,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarInspection);
        $d = new InspectionJob();
        $car_type_list = $this->getRentalType();
        $work_type_list = $this->getRentalTypeWork();
        $inspection_type = InspectionFlow::select('id', 'name')
            ->whereIn('inspection_type', [
                InspectionTypeEnum::NEW_CAR, InspectionTypeEnum::LONG_TERM_RENTAL, InspectionTypeEnum::BOAT, InspectionTypeEnum::SELF_DRIVE,
                InspectionTypeEnum::CARGO_TRUCK, InspectionTypeEnum::CHANGE_TYPE, InspectionTypeEnum::BUS, InspectionTypeEnum::LIMOUSINE, InspectionTypeEnum::MINI_COACH, InspectionTypeEnum::SLIDE_FORKLIFT
            ])
            ->get();
        $car_park_number = [];
        $car_park_zone = [];
        $worksheet = null;

        $page_title = __('lang.create') . __('car_park_transfers.license_table');
        return view('admin.inspection-jobs.form',  [
            'd' => $d,
            'car_type_list' => $car_type_list,
            'inspection_type' => $inspection_type,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'page_title' => $page_title,
            'work_type_list' => $work_type_list,
            'worksheet' => $worksheet,
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object) [
                'id' => RentalTypeEnum::SHORT,
                'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object) [
                'id' => RentalTypeEnum::LONG,
                'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object) [
                'id' => RentalTypeEnum::REPLACEMENT,
                'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::REPLACEMENT),
                'value' => RentalTypeEnum::REPLACEMENT,
            ],
            (object) [
                'id' => RentalTypeEnum::TRANSPORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
                'value' => RentalTypeEnum::TRANSPORT,
            ],
            (object) [
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],
        ]);
        return $rental_type;
    }

    public function getDataInspectionType(Request $request)
    {
        $step_form = InspectionFlow::leftjoin('inspection_steps', 'inspection_steps.inspection_flow_id', '=', 'inspection_flows.id')
            ->leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_steps.inspection_form_id')
            ->select('inspection_forms.name as inspection_step_name', 'inspection_steps.transfer_reason')
            ->where('inspection_flows.id', $request->id)
            ->get()->map(function ($item) {
                $item->transfer_reason_enum = $item->transfer_reason;
                $item->transfer_reason = __('car_inspection_types.status_condition_name_' . $item->transfer_reason);

                return $item;
            });
        // dd($step_form);
        return response()->json([
            'step_form' => $step_form,
        ]);
    }

    function getDefaultCar(Request $request)
    {
        $worksheet_no = $request->worksheet_no;
        $inspection_type = $request->inspection_type;
        $inspection_flow = InspectionFlow::find($inspection_type);
        $inspection_type_enum = $inspection_flow->inspection_type;
        if (in_array($inspection_type_enum, [InspectionTypeEnum::NEW_CAR, InspectionTypeEnum::CHANGE_TYPE])) {
            $car_list = $this->gerCarsByPO($worksheet_no, $request);
        } else {
            $car_list = $this->getCarsByRental($worksheet_no, $request);
        }
        $data = $car_list->toArray();
        return [
            'success' => true,
            'car_id' => $request->car_id,
            'data' => $data,
            'inspection_type_enum' => $inspection_type_enum,
        ];
    }

    function getSelectOptionCars(Request $request)
    {
        $worksheet_no = $request->worksheet_no;
        $inspection_type = $request->inspection_type;
        $result = [];
        if (empty($worksheet_no) || empty($inspection_type)) {
            return response()->json($result);
        }
        $inspection_flow = InspectionFlow::find($inspection_type);
        $inspection_type_enum = $inspection_flow->inspection_type;
        if (in_array($inspection_type_enum, [InspectionTypeEnum::NEW_CAR, InspectionTypeEnum::CHANGE_TYPE])) {
            $car_list = $this->gerCarsByPO($worksheet_no, $request);
        } else {
            $car_list = $this->getCarsByRental($worksheet_no, $request);
        }
        $result = $car_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else {
            }
            $item->id = $item->car_id;
            $item->text = $text;
            return $item;
        });

        return response()->json($result);
    }

    function getDefaultCarByLicensePlate(Request $request)
    {
        $car_id = $request->car_id;
        $data = DB::table('cars')
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'car_classes.oil_type',
                'cars.rental_type',
            )
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            // ->leftJoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->where('cars.id', $car_id)
            ->get()
            ->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                } else {
                }
                $item->id = $item->car_id;
                $item->text = $text;
                return $item;
            })
            ->toArray();
        return [
            'success' => true,
            'car_id' => $request->car_id,
            'data' => $data
        ];
    }

    function getWorksheet(Request $request)
    {
        $car_type = $request->car_type;
        $data = DB::table('rentals')
            ->select(
                'worksheet_no'
            )
            ->where('rental_type', $car_type)
            ->get();
        return [
            'success' => true,
            'car_type' => $request->car_type,
            'data' => $data
        ];
    }

    public function store(Request $request)
    {
        $inspection_flow = InspectionFlow::where('id', $request->inspection_type)->select('id', 'inspection_type', 'is_need_customer_sign_in', 'is_need_customer_sign_out')->first();
        if ($inspection_flow) {
            $inspection_step = InspectionStep::where('inspection_flow_id', $inspection_flow->id)->select('transfer_reason')->get();
            $delivery_count = 0;
            $receive_count = 0;
            foreach ($inspection_step as $data) {
                if ($data->transfer_reason == TransferReasonEnum::DELIVER_CUSTOMER || $data->transfer_reason == TransferReasonEnum::DELIVER_GARAGE) {
                    $delivery_count += 1;
                }
                if ($data->transfer_reason == TransferReasonEnum::RECEIVE_WAREHOUSE) {
                    $receive_count += 1;
                }
            }

            $validator = Validator::make($request->all(), [
                'worksheet_no' => [
                    Rule::when($request->work_type != RentalTypeEnum::OTHER, ['required']),
                ],
                'worksheet_other' => [
                    Rule::when($request->work_type == RentalTypeEnum::OTHER, ['required']),
                ],
                'inspection_must_date_out' => [
                    Rule::when($delivery_count > 0, ['required']),
                ],
                'inspection_must_date_in' => [
                    Rule::when($receive_count > 0, ['required']),
                ],
                'car_type' => ['required'],
                'car_id' => ['required'],
                'inspection_type' => ['required', 'nullable'],
            ], [], [
                'work_type' => __('inspection_cars.work_type'),
                'worksheet_no' => __('inspection_cars.worksheet_no'),
                'worksheet_other' => __('inspection_cars.worksheet_other'),
                'car_type' => __('inspection_cars.car_type'),
                'car_id' => __('inspection_cars.license_plate'),
                'inspection_type' => __('inspection_cars.inspection_type'),
                'inspection_must_date_out' => __('inspection_cars.out_must_date'),
                'inspection_must_date_in' => __('inspection_cars.in_must_date'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $ijf = new InspectionJobFactory($request->inspection_type, null, $request->worksheet_no, $request->car_id, [
                'inspection_must_date_out' => $request->inspection_must_date_out,
                'inspection_must_date_in' => $request->inspection_must_date_in
            ]);
            $ijf->create();
        }
        $redirect_route = route('admin.inspection-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /* public static function saveInspectionJobStep($step_form_detail, $inspection_job_id, $inspection_flow)
    {
        foreach ($step_form_detail as $step_form_data) {
            $inspection_job_step = new InspectionJobStep();
            $inspection_job_step->inspection_job_id = $inspection_job_id;
            $inspection_job_step->inspection_step_id = $step_form_data->inspection_step_id;
            $inspection_job_step->inspection_form_id = $step_form_data->inspection_form_id;
            $inspection_job_step->transfer_type = $step_form_data->transfer_type;
            $inspection_job_step->transfer_reason = $step_form_data->transfer_reason;
            $inspection_job_step->inspection_department_id = $step_form_data->inspection_department_id;
            $inspection_job_step->is_need_images = $step_form_data->is_need_images;
            $inspection_job_step->is_need_inspector_sign = $step_form_data->is_need_inspector_sign;
            $inspection_job_step->is_need_send_mobile = $step_form_data->is_need_send_mobile;
            $inspection_job_step->is_need_dpf = $step_form_data->is_need_dpf;
            $inspection_job_step->inspection_role_id = $step_form_data->inspection_role_id;
            $inspection_job_step->inspector_type = $step_form_data->is_need_send_mobile == STATUS_ACTIVE ? Driver::class : User::class;
            $inspection_job_step->inspector_id = '';
            $inspection_job_step->save();

            $inspection_section_data = InspectionFormSection::leftjoin('inspection_form_checklists', 'inspection_form_checklists.inspection_form_section_id', '=', 'inspection_form_sections.id')
                ->where('inspection_form_sections.inspection_form_id', $inspection_job_step->inspection_form_id)
                ->select(
                    'inspection_form_sections.*',
                    'inspection_form_checklists.id as checklist_id',
                    'inspection_form_checklists.name as checklist_name',
                    'inspection_form_checklists.car_part as checklist_car_part',
                )
                ->get();
            $section_count = count($inspection_section_data);
            $is_inspection_type_new_car = strcmp($inspection_flow->inspection_type, InspectionTypeEnum::NEW_CAR) == 0;
            $is_form_type_new_car = strcmp($step_form_data->form_type, InspectionFormEnum::NEWCAR) == 0;
            $is_form_type_equiment = strcmp($step_form_data->form_type, InspectionFormEnum::EQUIPMENT) == 0;
            foreach ($inspection_section_data as $inspection_section_key => $inspection_section_data) {
                $inspection_job = InspectionJob::find($inspection_job_id);
                // add accessory to check list
                if ($is_inspection_type_new_car && $is_form_type_new_car) {
                    $accessory_list = (new static)->getAccessoriesByPO($inspection_job->item_id);
                    if (sizeof($accessory_list) > 0) {
                        foreach ($accessory_list as $key => $accessory) {
                            $inspection_job_checklist = new InspectionJobChecklist();
                            $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                            $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                            $inspection_job_checklist->inspection_form_section_name = __('inspection_cars.form_status_' . InspectionFormEnum::ACCESSORY);
                            $inspection_job_checklist->inspection_form_checklist_id = NULL;
                            $inspection_job_checklist->inspection_form_checklist_name = $accessory->name;
                            $inspection_job_checklist->save();
                        }
                    }
                } else {
                    if ($is_inspection_type_new_car && $is_form_type_equiment && strcmp($inspection_section_key, $section_count - 1) == 0) {
                        $inspection_job = InspectionJob::find($inspection_job_id);
                        $accessory_list = (new static)->getAccessoriesByPO($inspection_job->item_id);
                        if (sizeof($accessory_list) > 0) {
                            foreach ($accessory_list as $key => $accessory) {
                                $inspection_job_checklist = new InspectionJobChecklist();
                                $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                                $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                                $inspection_job_checklist->inspection_form_section_name = __('inspection_cars.form_status_' . InspectionFormEnum::ACCESSORY);
                                $inspection_job_checklist->inspection_form_checklist_id = NULL;
                                $inspection_job_checklist->inspection_form_checklist_name = $accessory->name;
                                $inspection_job_checklist->save();
                            }
                        }
                    } else {
                        $inspection_job_checklist = new InspectionJobChecklist();
                        $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                        $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                        $inspection_job_checklist->inspection_form_section_name = $inspection_section_data->name;
                        $inspection_job_checklist->inspection_form_checklist_id = $inspection_section_data->checklist_id;
                        $inspection_job_checklist->inspection_form_checklist_name = $inspection_section_data->checklist_name;
                        if (!empty($inspection_section_data->checklist_car_part)) {
                            $car_id = $inspection_job->car_id;
                            $car_part = (new static)->getCarPartName($inspection_section_data->checklist_car_part, $car_id);
                            $inspection_job_checklist->inspection_form_checklist_name = $inspection_section_data->checklist_name . ' : ' . $car_part->car_part_name;
                        }
                        $inspection_job_checklist->save();
                    }
                }
            }
        }
        return true;
    } */

    public function show(InspectionJob $inspection_car)
    {
        $this->authorize(Actions::View . '_' . Resources::CarInspection);
        $list = InspectionJob::leftjoin('cars', 'cars.id', '=', 'inspection_jobs.car_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('cars.id', $inspection_car->car_id)
            ->select(
                'inspection_jobs.*',
                'cars.engine_no',
                'cars.chassis_no',
                'cars.rental_type',
                'cars.license_plate as license_plate',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
            )
            ->first();

        $car = Car::select('id', 'license_plate as name')->get();
        $car_detail = Car::where('id', $inspection_car)->select('engine_no', 'chassis_no')->get();
        $car_type_list = InspectionFlow::select('id', 'name')->get();
        $inspection_type = InspectionFlow::select('id', 'name')->get();
        $car_park_number = [];
        $car_park_zone = [];

        $page_title = __('lang.create') . __('car_park_transfers.license_table');
        return view('admin.inspection-jobs.form',  [
            'd' => $list,
            'view' => true,
            'car' => $car,
            'car_type_list' => $car_type_list,
            'inspection_type' => $inspection_type,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'page_title' => $page_title,
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public static function getRentalTypeWork()
    {
        $rental_type = collect([
            (object) [
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object) [
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object) [
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],
        ]);
        return $rental_type;
    }

    public function getModelClass($type)
    {
        $mapping = [
            RentalTypeEnum::SHORT => Rental::class,
            RentalTypeEnum::LONG => LongTermRental::class,
            RentalTypeEnum::OTHER => null,
        ];
        return isset($mapping[$type]) ? $mapping[$type] : null;
    }

    public function gerCarsByPO($po_id, $request)
    {
        $s = $request->s;
        $car_type = $request->car_type;
        return Car::leftjoin('import_car_lines', 'import_car_lines.engine_no', '=', 'cars.engine_no')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('purchase_orders.id', $po_id)
            ->when(!empty($s), function ($q) use ($s) {
                $q->where('cars.engine_no', 'like', '%' . $s . '%');
                $q->orWhere('cars.chassis_no', 'like', '%' . $s . '%');
                $q->orWhere('cars.license_plate', 'like', '%' . $s . '%');
            })
            ->when(!empty($car_type), function ($q) use ($car_type) {
                $q->where('cars.rental_type', $car_type);
            })
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'car_classes.oil_type',
                'cars.rental_type'
            )->get()
            ->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                } else {
                }
                $item->id = $item->car_id;
                $item->text = $text;
                return $item;
            });
    }

    public function getCarsByRental($rental_id, $request)
    {
        $s = $request->s;
        $car_type = $request->car_type;
        if ($request->inspection_type) {
            $inspection_type = $request->inspection_type;
            $inspection_flow = InspectionFlow::find($inspection_type);
            $inspection_type_enum = $inspection_flow->inspection_type;
        }

        $query = DB::table('cars')
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'car_classes.oil_type',
                'cars.rental_type',
                DB::raw('DATE(rental_lines.return_date) as return_date'),
                DB::raw('DATE(rental_lines.pickup_date) as pickup_date')
            )
            // ->selectRaw('DATE(rental_lines.return_date)')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->leftJoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id');
        if (in_array($inspection_type_enum, [InspectionTypeEnum::SELF_DRIVE, InspectionTypeEnum::MINI_COACH, InspectionTypeEnum::LIMOUSINE, InspectionTypeEnum::CARGO_TRUCK, InspectionTypeEnum::BOAT, InspectionTypeEnum::BUS, InspectionTypeEnum::SLIDE_FORKLIFT])) {
            $query->where('rental_lines.rental_id', $rental_id);
        }
        $query = $query->when(!empty($s), function ($q) use ($s) {
            $q->where('cars.engine_no', 'like', '%' . $s . '%');
            $q->orWhere('cars.chassis_no', 'like', '%' . $s . '%');
            $q->orWhere('cars.license_plate', 'like', '%' . $s . '%');
        })
            ->when(!empty($car_type), function ($q) use ($car_type) {
                $q->where('cars.rental_type', $car_type);
            })
            ->get();
        $query = $query->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else {
            }
            $item->id = $item->car_id;
            $item->text = $text;
            return $item;
        });
        return $query;
    }


    public function getAccessoriesByPO($po_id)
    {
        return PurchaseOrder::leftjoin('purchase_order_lines', 'purchase_order_lines.purchase_order_id', '=', 'purchase_orders.id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->leftjoin('import_car_lines', 'import_car_lines.import_car_id', '=', 'import_cars.id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('purchase_requisition_line_accessories', 'purchase_requisition_line_accessories.purchase_requisition_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->where('purchase_orders.id', $po_id)
            ->select(
                'accessories.id',
                'accessories.name',
            )
            ->distinct()
            ->get();
    }

    public function getCarPartName($car_part, $car_id)
    {
        return Car::when(strcmp($car_part, CarPartTypeEnum::GEAR) == 0, function ($query) {
            $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.gear_id');
            $query->select('car_parts.name as car_part_name');
        })
            ->when(strcmp($car_part, CarPartTypeEnum::DRIVE_SYSTEM) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.drive_system_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::CAR_SEAT) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.car_seat_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::SIDE_MIRROR) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.side_mirror_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::AIR_BAG) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.air_bag_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::CENTRAL_LOCK) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.central_lock_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::FRONT_BRAKE) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.front_brake_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::REAR_BRAKE) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.rear_brake_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::REAR_BRAKE) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.rear_brake_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::ABS) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.abs_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::ANTI_THIFT_SYSTEM) == 0, function ($query) {
                $query->leftjoin('car_parts', 'car_parts.id', '=', 'cars.anti_thift_system_id');
                $query->select('car_parts.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::BATTERY) == 0, function ($query) {
                $query->leftjoin('car_batteries', 'car_batteries.id', '=', 'cars.car_battery_id');
                $query->select('car_batteries.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::TIRE) == 0, function ($query) {
                $query->leftjoin('car_tires', 'car_tires.id', '=', 'cars.car_tire_id');
                $query->select('car_tires.name as car_part_name');
            })
            ->when(strcmp($car_part, CarPartTypeEnum::WIPER) == 0, function ($query) {
                $query->leftjoin('car_wipers', 'car_wipers.id', '=', 'cars.car_wiper_id');
                $query->select('car_wipers.name as car_part_name');
            })
            ->where('cars.id', $car_id)
            ->first();
    }


    public static function getStatus()
    {
        $rental_type = collect([
            (object) [
                'id' => InspectionStatusEnum::DRAFT,
                'name' => __('inspection_cars.status_' . InspectionStatusEnum::DRAFT),
                'value' => InspectionStatusEnum::DRAFT,
            ],
            (object) [
                'id' => InspectionStatusEnum::PASS,
                'name' => __('inspection_cars.status_' . InspectionStatusEnum::PASS),
                'value' => InspectionStatusEnum::PASS,
            ],
            (object) [
                'id' => InspectionStatusEnum::NOT_PASS,
                'name' => __('inspection_cars.status_' . InspectionStatusEnum::NOT_PASS),
                'value' => InspectionStatusEnum::NOT_PASS,
            ],
            (object) [
                'id' => InspectionStatusEnum::CANCEL,
                'name' => __('inspection_cars.status_' . InspectionStatusEnum::CANCEL),
                'value' => InspectionStatusEnum::CANCEL,
            ],
        ]);
        return $rental_type;
    }
}
