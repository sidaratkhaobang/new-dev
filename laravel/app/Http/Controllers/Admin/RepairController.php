<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ReplacementCar;
use Illuminate\Http\Request;
use App\Enums\RepairEnum;
use App\Enums\RepairStatusEnum;
use App\Enums\CarEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\Repair;
use App\Models\Car;
use App\Models\Rental;
use App\Models\Contracts;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\RentalLine;
use App\Models\RepairLine;
use App\Models\RepairOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\RepairTrait;
use App\Traits\ReplacementCarTrait;
use DateTime;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Repair);
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $alert_date = $request->alert_date;
        $list = Repair::leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('repair_orders', 'repair_orders.repair_id', '=', 'repairs.id')
            ->select(
                'repairs.id',
                'repairs.status',
                'repairs.worksheet_no',
                'repairs.repair_type',
                'repairs.repair_date',
                'repairs.contact',
                'repairs.in_center_date',
                'cars.license_plate',
                'repair_orders.worksheet_no as order_worksheet_no',
                'repair_orders.expected_repair_date',
                'repair_orders.repair_date as completed_date',
            )
            ->search($request)
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('repairs.car_id', $license_plate);
            })
            ->when($order_worksheet_no, function ($query) use ($order_worksheet_no) {
                return $query->where('repair_orders.id', $order_worksheet_no);
            })
            ->orderBy('repairs.created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_no_list = Repair::select('id', 'worksheet_no as name')->orderBy('worksheet_no')->get();
        $repair_type_list = RepairTrait::getRepairType();
        $license_plate_list = Car::select('id', 'license_plate as name')->get();
        $contact_list = Repair::select('contact as id', 'contact as name')->orderBy('contact')->distinct()->get();
        $order_worksheet_no_list = RepairOrder::select('id', 'worksheet_no as name')->orderBy('worksheet_no')->get();
        $status_list = RepairTrait::getStatus();

        $create_uri = route('admin.repairs.create');
        $edit_uri = 'admin.repairs.edit';
        $view_uri = 'admin.repairs.show';
        $param = 'repair';
        $view_permission = Actions::View . '_' . Resources::Repair;
        $manage_permission = Actions::Manage . '_' . Resources::Repair;
        $page_title = __('repairs.page_title');
        return view('admin.repairs.index', [
            'list' => $list,
            'page_title' => $page_title,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_list' => $worksheet_no_list,
            'repair_type' => $repair_type,
            'repair_type_list' => $repair_type_list,
            'license_plate' => $license_plate,
            'license_plate_list' => $license_plate_list,
            'contact' => $contact,
            'contact_list' => $contact_list,
            'order_worksheet_no' => $order_worksheet_no,
            'order_worksheet_no_list' => $order_worksheet_no_list,
            'status' => $status,
            'status_list' => $status_list,
            'alert_date' => $alert_date,
            'create_uri' => $create_uri,
            'edit_uri' => $edit_uri,
            'view_uri' => $view_uri,
            'param' => $param,
            'view_permission' => $view_permission,
            'manage_permission' => $manage_permission,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Repair);
        $d = new Repair();
        $d->open_by = RepairEnum::REPAIR_DEPARTMENT;
        $d->in_center = BOOL_TRUE;
        $d->out_center = BOOL_TRUE;
        // $d->is_replacement = BOOL_TRUE;
        $date = new DateTime();
        $d->repair_date = $date->format('Y-m-d H:i:s');
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car_license = null;
        $car_data = new Car;
        $rental = 0;
        $index_uri = 'admin.repairs.index';
        $replacement_list = [];
        $yes_no_list = getYesNoList();
        $page_title = __('lang.create') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $d,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'informer_type_list' => $informer_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'informers' => $informers,
            'car_license' => $car_license,
            'car_data' => $car_data,
            'rental' => $rental,
            'condotion_lt_rental' => [],
            'index_uri' => $index_uri,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => [],
            'driving_job_in' => null,
            'inspection_job_in' => null,
            'driving_job_out' => null,
            'inspection_job_out' => null,
            'mode' => MODE_CREATE
        ]);
    }

    public function edit(Repair $repair)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Repair);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car = Car::find($repair->car_id);
        $car_license = null;
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;

        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($repair->car_id);
        $rental = $car_data->rental;
        $check_repair_list = RepairLine::where('repair_id', $repair->id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                return $item;
            });

        $repair_order_id = RepairOrder::where('repair_id', $repair->id)->first()?->id;
        if ($repair_order_id) {
            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($repair);
        $repair_documents_media = $repair->getMedia('repair_documents');
        $repair_documents_files = get_medias_detail($repair_documents_media);
        $yes_no_list = getYesNoList();
        $replacement_list = RepairTrait::getReplacementList($repair->id);
        $index_uri = 'admin.repairs.index';
        $page_title =  __('lang.edit') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $repair,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'informer_type_list' => $informer_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'informers' => $informers,
            'car_license' => $car_license,
            'car_data' => $car_data,
            'rental' => $rental,
            'check_repair_list' => $check_repair_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_UPDATE
        ]);
    }

    public function show(Repair $repair)
    {
        $this->authorize(Actions::View . '_' . Resources::Repair);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car = Car::find($repair->car_id);
        $car_license = null;
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;

        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($repair->car_id);
        $rental = $car_data->rental;
        $check_repair_list = RepairLine::where('repair_id', $repair->id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                return $item;
            });

        $repair_order_id = RepairOrder::where('repair_id', $repair->id)->first()?->id;
        if ($repair_order_id) {
            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($repair);
        $repair_documents_media = $repair->getMedia('repair_documents');
        $repair_documents_files = get_medias_detail($repair_documents_media);
        $yes_no_list = getYesNoList();
        $replacement_list = RepairTrait::getReplacementList($repair->id);
        $index_uri = 'admin.repairs.index';
        $page_title =  __('lang.view') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $repair,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'informer_type_list' => $informer_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'informers' => $informers,
            'car_license' => $car_license,
            'car_data' => $car_data,
            'rental' => $rental,
            'check_repair_list' => $check_repair_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'view' => true,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_VIEW
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize(Actions::Manage . '_' . Resources::RepairList);
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'repair_type' => [
                'required',
            ],
            'repair_date' => [
                'required',
            ],
            'contact' => [
                'required',
            ],
            'tel' => [
                'required', 'numeric', 'digits:10',
            ],
            'mileage' => [
                'required',
            ],
            'is_driver_in_center' => [
                Rule::when($request->in_center == BOOL_FALSE, ['required'])
            ],
            'is_driver_out_center' => [
                Rule::when($request->out_center == BOOL_FALSE, ['required'])
            ],
            'replacement_date' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],
            'replacement_type' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],
            'replacement_place' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],

        ], [], [
            'car_id' => __('repairs.car_no'),
            'repair_type' =>  __('repairs.repair_type'),
            'repair_date' =>  __('repairs.repair_date'),
            'contact' =>  __('repairs.contact'),
            'tel' =>  __('repairs.tel'),
            'mileage' =>  __('repairs.mileage'),
            'is_driver_in_center' =>  __('repairs.is_driver_in_center'),
            'is_driver_out_center' =>  __('repairs.is_driver_out_center'),
            'replacement_date' =>  __('repairs.replacement_date'),
            'replacement_type' =>  __('repairs.replacement_type'),
            'replacement_place' =>  __('repairs.replacement_place'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $repair = Repair::firstOrNew(['id' => $request->id]);
        $repair_count = Repair::all()->count() + 1;
        $prefix = 'RE';
        if (!($repair->exists)) {
            $repair->worksheet_no = generateRecordNumber($prefix, $repair_count);
        }

        $repair->car_id = $request->car_id;
        $repair->job_id = $request->rental_id;
        $repair->job_type = $request->rental_type;
        $repair->repair_type = $request->repair_type;
        $repair->repair_date = $request->repair_date;
        $repair->informer_type = $request->informer_type;
        $repair->informer = $request->informer;
        $repair->contact = $request->contact;
        $repair->tel = $request->tel;
        $mileage = str_replace(',', '', $request->mileage);
        $repair->mileage = $mileage;
        $repair->place = $request->place;
        $repair->remark = $request->remark;
        $repair->in_center = $request->in_center;
        $repair->in_center_date = $request->in_center_date;
        $repair->is_driver_in_center = $request->is_driver_in_center;
        $repair->out_center = $request->out_center;
        $repair->out_center_date = $request->out_center_date;
        $repair->is_driver_out_center = $request->is_driver_out_center;
        $repair->is_replacement = $request->is_replacement;
        $repair->replacement_type = $request->replacement_type;
        $repair->replacement_date = $request->replacement_date;
        $repair->replacement_place = $request->replacement_place;
        $repair->status = RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER;
        $repair->open_by = $request->open_by;
        $repair->save();

        $car = Car::find($repair->car_id);
        if ($car) {
            $car->current_mileage = $repair->mileage;
            $car->save();
        }

        if ($request->del_check_repair != null) {
            RepairLine::whereIn('id', $request->del_check_repair)->delete();
        }
        if (!empty($request->check_repairs)) {
            foreach ($request->check_repairs as $request_repair_line) {
                if (isset($request_repair_line['id'])) {
                    $repair_line = RepairLine::find($request_repair_line['id']);
                } else {
                    $repair_line = new RepairLine();
                }
                $repair_line->repair_id = $repair->id;
                $repair_line->date = $request_repair_line['date'];
                $repair_line->description = $request_repair_line['description'];
                $repair_line->check = $request_repair_line['check'];
                $repair_line->qc = $request_repair_line['qc'];
                $repair_line->save();
            }
        }

        if ($request->repair_documents__pending_delete_ids) {
            $pending_delete_ids = $request->repair_documents__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $repair->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('repair_documents')) {
            foreach ($request->file('repair_documents') as $file) {
                if ($file->isValid()) {
                    $repair->addMedia($file)
                        ->toMediaCollection('repair_documents');
                }
            }
        }

        if (isset($request->replacements) && sizeof($request->replacements) > 0) {
            $user = Auth::user();
            foreach ($request->replacements as $key => $replacement) {
                if (!$replacement['id']) {
                    $replacement_car = new ReplacementCar;
                    $replacement_car_count = DB::table('replacement_cars')->count() + 1;
                    $prefix = 'RC-';
                    $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
                    $replacement_car->replacement_type = $replacement['job_type_id'] ?? null;
                    $replacement_car->job_type = ReplacementJobTypeEnum::REPAIR;
                    $replacement_car->job_id = $repair->id;
                    $replacement_car->branch_id = $user ? $user->branch_id : null;
                    $replacement_car->main_car_id = $replacement['main_car_id'] ?? null;
                    $replacement_car->replacement_expect_date = $replacement['send_pickup_date'] ?? null;
                    $replacement_car->is_need_driver = $replacement['slide_id'] ? false : true;
                    $replacement_car->is_need_slide = $replacement['slide_id'] ? true : false;
                    $replacement_car->slide_id = $replacement['slide_id'] ?? null;
                    $replacement_car->is_cust_receive_replace = filter_var($replacement['is_at_tls'], FILTER_VALIDATE_BOOLEAN);
                    $replacement_car->replacement_expect_place = $replacement['send_pickup_place'] ?? null;
                    $replacement_car->customer_name = $request->contact;
                    $replacement_car->tel = $request->tel;
                    $replacement_car->save();

                    if ((!empty($request->replacement_files)) && (sizeof($request->replacement_files) > 0)) {
                        $all_replacement_files = $request->replacement_files;
                        if (isset($all_replacement_files[$key])) {
                            $replacement_car_files = $all_replacement_files[$key];
                            foreach ($replacement_car_files as $replacement_car_file) {
                                if ($replacement_car_file) {
                                    $replacement_car->addMedia($replacement_car_file)->toMediaCollection('replacement_car_documents');
                                }
                            }
                        }
                    }
                }
            }
        }

        $redirect_route = route($request->redirect_route);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getDefaultCarID(Request $request)
    {
        $data = [];
        $data = Car::select('id', 'license_plate', 'engine_no', 'chassis_no', 'status')
            ->whereNotIn('status', [CarEnum::SOLD_OUT])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->limit(30)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($data);
    }

    public function getDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['car_class_name'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;
        $data['license_plate'] = ($car) ? $car->license_plate : null;
        $data['current_mileage'] = ($car) ? $car->current_mileage : null;
        $data['car_status'] = ($car) ? $car->status : null;
        $data['status_class'] = __('cars.class_' . $car->status);
        $data['status_text'] = __('cars.status_' . $car->status);
        $data['rental'] = 0;
        $data['contract'] = 0;
        $rental_type = null;

        if (strcmp($car->rental_type, RentalTypeEnum::SHORT) == 0) {
            $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                ->where('rental_lines.car_id', $car->id)
                ->where('rentals.status', RentalStatusEnum::PAID)
                ->select('rentals.id', 'rentals.worksheet_no', 'rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = Rental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', Rental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }
        if (strcmp($car->rental_type, RentalTypeEnum::LONG) == 0) {
            $rental = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
                ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
                ->where('lt_rental_pr_lines_cars.car_id', $car->id)
                ->select('lt_rentals.id', 'lt_rentals.worksheet_no', 'lt_rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = LongTermRental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', LongTermRental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }

        $data['rental_type'] = $rental_type;
        if ($rental) {
            $data['rental'] = 1;
            $data['rental_id'] = ($rental) ? $rental->id : null;
            $data['rental_worksheet_no'] = ($rental) ? $rental->worksheet_no : null;
            $data['rental_customer_name'] = ($rental) ? $rental->customer_name : null;

            if ($contract) {
                $data['contract'] = 1;
                $data['contract_worksheet_no'] = ($contract) ? $contract->worksheet_no : null;
                $data['contract_pick_up_date'] = ($contract) ? date('d/m/Y H:i', strtotime($contract->pick_up_date)) : null;
                $data['contract_return_date'] = ($contract) ? date('d/m/Y H:i', strtotime($contract->return_date)) : null;
            }
        }

        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
