<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarParkStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\InstallEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\CarParkTransfer;
use App\Models\CarParkTransferLog;
use App\Models\CarCategory;
use App\Models\Car;
use App\Models\CarStatus;
use App\Models\CarPark;
use App\Models\DrivingJob;
use App\Models\CarParkZone;
use App\Enums\TransferTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Jobs\ClearCarParkBooked;
use App\Jobs\SetCarParkBooked;
use App\Models\LongTermRental;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Models\ImportCar;
use DateTime;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\Resources;
use App\Models\RepairOrder;

class CarParkTransferController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarParkTransfer);
        $query_log = DB::table('car_park_transfer_logs')
            ->select('car_park_transfer_id', DB::raw('MAX(transfer_date) as transfer_date'), DB::raw('MAX(transfer_type) as transfer_type'))
            ->groupBy('car_park_transfer_id');

        $list = CarParkTransfer::sortable(['created_at' => 'desc'])
            ->leftJoin('cars', 'cars.id', '=', 'car_park_transfers.car_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->leftJoin('car_statuses', 'car_statuses.id', '=', 'car_park_transfers.car_status_id')
            ->leftJoinSub(get_sub_query_car_park_zones(), 'car_park_zones', function ($join) {
                $join->on('cars.id', '=', 'car_park_zones.car_id');
            })
            ->leftJoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->leftjoinSub($query_log, 'car_park_transfer_logs', function ($join) {
                $join->on('car_park_transfers.id', '=', 'car_park_transfer_logs.car_park_transfer_id');
            })
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_park_transfers.id as id',
                'car_park_transfers.worksheet_no as worksheet_no',
                'car_park_transfers.transfer_type as transfer_type',
                'car_park_transfers.est_transfer_date as est_transfer_date',
                'car_park_transfers.start_date as start_date',
                'car_park_transfers.end_date as end_date',
                'car_park_transfers.status as status',
                'car_statuses.name as car_status_name',
                'car_park_transfer_logs.transfer_date as transfer_date',
                'car_park_transfer_logs.transfer_type as transfer_type_log',
                'car_park_zones.car_park_number',
                'car_park_zones.zone_code',
                'driving_jobs.worksheet_no as driving_job_no',
                'car_categories.name as car_categories_name'
            )
            ->where('car_park_transfers.branch_id', get_branch_id())
            ->sortable('worksheet_no')
            ->orderBy('car_park_transfers.created_at', 'desc')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $today = date('Y-m-d');
        $list->map(function ($item) use ($today) {
            if ($today < $item->start_date) {
                $item->status_car  = 'warning';
            } elseif ($today >= $item->start_date && $today <= $item->end_date) {
                $item->status_car  = 'info';
            } elseif ($today > $item->end_date) {
                $item->status_car  = 'success';
            }
            if ($item->status === STATUS_INACTIVE) {
                $item->status_car  = 'danger';
            }
            return $item;
        });

        $status_list = $this->getStatus();
        $transfer_type_list = $this->getTransferType();

        $license_plate = null;
        if (!empty($request->car_id)) {
            $car = Car::find($request->car_id);
            $license_plate = $car->license_plate;
        }
        $engine_no = null;
        if (!empty($request->engine_no)) {
            $car = Car::find($request->engine_no);
            $engine_no = $car->engine_no;
        }
        $chassis_no = null;
        if (!empty($request->chassis_no)) {
            $car = Car::find($request->chassis_no);
            $chassis_no = $car->chassis_no;
        }

        return view('admin.car-park-transfers.index', [
            's' => $request->s,
            'list' => $list,
            'status_list' => $status_list,
            'transfer_type_list' => $transfer_type_list,
            'car_id' => $request->car_id,
            'license_plate' => $license_plate,
            'est_transfer_date' => $request->est_transfer_date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'transfer_type' => $request->transfer_type,
            'engine_no_id' => $request->engine_no,
            'chassis_no_id' => $request->chassis_no,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'status' => $request->status,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarParkTransfer);
        $d = new CarParkTransfer();
        $car = Car::select('id', 'license_plate as name')->get();
        $driving_jobs = DrivingJob::select('id', 'worksheet_no as name')->get();
        $car_status = CarStatus::select('id', 'name')->get();
        $transfer_type = $this->getTransferType();
        $car_park_number = [];
        $car_park_zone = [];
        $job_name = null;
        $car_name = null;
        $is_difference_branch_list =  $this->getYesNoList();
        $is_singular_list = $this->getYesNoList([
            __('car_park_transfers.is_singular_1'),
            __('car_park_transfers.is_singular_0'),
        ]);
        $branch_list = $this->getBranchList();

        $page_title = __('lang.create') . __('car_park_transfers.license_table');
        return view('admin.car-park-transfers.form',  [
            'd' => $d,
            'page_title' => $page_title,
            'transfer_type' => $transfer_type,
            'car' => $car,
            'car_status' => $car_status,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'driving_jobs' => $driving_jobs,
            'job_name' => $job_name,
            'car_name' => $car_name,
            'is_difference_branch_list' => $is_difference_branch_list,
            'is_singular_list' => $is_singular_list,
            'branch_list' => $branch_list
        ]);
    }

    public function edit(CarParkTransfer $car_park_transfer)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarParkTransfer);
        $car = Car::select('id', 'license_plate as name')->get();
        $car_status = CarStatus::select('id', 'name')->get();
        $driving_jobs = DrivingJob::select('id', 'worksheet_no as name')->get();
        $transfer_type = $this->getTransferType();
        $car_park_number = ($car_park_transfer->carPark) ? $car_park_transfer->carPark->car_park_number : null;
        $car_park_zone = $car_park_transfer->carPark && $car_park_transfer->carPark->carParkArea && $car_park_transfer->carPark->carParkArea->carParkZone ? $car_park_transfer->carPark->carParkArea->carParkZone->code . ' : ' . $car_park_transfer->carPark->carParkArea->carParkZone->name : null;

        $job_name = null;
        if ($car_park_transfer->driving_job) {
            if (strcmp($car_park_transfer->driving_job?->job_type, Rental::class) == 0) {
                $job_name = $car_park_transfer->driving_job?->job?->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job?->job_type, LongTermRental::class) == 0) {
                $job_name = $car_park_transfer->driving_job?->job?->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job?->job_type, ImportCar::class) == 0) {
                if ($car_park_transfer->driving_job?->job?->purchaseOrder) {
                    $job_name = $car_park_transfer->driving_job?->job?->purchaseOrder->po_no;
                }
            } else if (strcmp($car_park_transfer->driving_job?->job_type, InstallEquipment::class) == 0) {
                $job_name = $car_park_transfer->driving_job?->job?->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job?->job_type, RepairOrder::class) == 0) {
                $job_name = $car_park_transfer->driving_job?->job?->worksheet_no;
            }
        }

        $car_text = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where('id', $car_park_transfer->car_id)
            ->first();
        $car_name = null;
        if ($car_text && $car_text->license_plate) {
            $car_name = $car_text->license_plate;
        } else if ($car_text && $car_text->engine_no) {
            $car_name = __('inspection_cars.engine_no') . ' ' . $car_text->engine_no;
        } else if ($car_text && $car_text->chassis_no) {
            $car_name = __('inspection_cars.chassis_no') . ' ' . $car_text->chassis_no;
        }

        $is_difference_branch_list =  $this->getYesNoList();
        $is_singular_list = $this->getYesNoList([
            __('car_park_transfers.is_singular_1'),
            __('car_park_transfers.is_singular_0'),
        ]);
        $branch_list = $this->getBranchList();

        $page_title = __('lang.edit') . __('car_park_transfers.license_table');
        return view('admin.car-park-transfers.form',  [
            'd' => $car_park_transfer,
            'page_title' => $page_title,
            'transfer_type' => $transfer_type,
            'car' => $car,
            'car_status' => $car_status,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'driving_jobs' => $driving_jobs,
            'job_name' => $job_name,
            'car_name' => $car_name,
            'is_difference_branch_list' => $is_difference_branch_list,
            'is_singular_list' => $is_singular_list,
            'branch_list' => $branch_list
        ]);
    }

    public function show(CarParkTransfer $car_park_transfer)
    {
        $this->authorize(Actions::View . '_' . Resources::CarParkTransfer);
        $car = Car::select('id', 'license_plate as name')->get();
        $car_status = CarStatus::select('id', 'name')->get();
        $driving_jobs = DrivingJob::select('id', 'worksheet_no as name')->get();
        $transfer_type = $this->getTransferType();
        $car_park_number = ($car_park_transfer->carPark) ? $car_park_transfer->carPark->car_park_number : null;
        $car_park_zone = $car_park_transfer->carPark && $car_park_transfer->carPark->carParkArea && $car_park_transfer->carPark->carParkArea->carParkZone ? $car_park_transfer->carPark->carParkArea->carParkZone->code . ' : ' . $car_park_transfer->carPark->carParkArea->carParkZone->name : null;

        $transfer_logs = CarParkTransferLog::select('car_park_transfer_logs.transfer_type', 'car_park_transfer_logs.transfer_date', 'users.name as driver_name')
            ->leftJoin('car_park_transfers', 'car_park_transfers.id', '=', 'car_park_transfer_logs.car_park_transfer_id')
            ->leftJoin('users', 'users.id', '=', 'car_park_transfer_logs.driver_id')
            ->where('car_park_transfer_logs.car_park_transfer_id', $car_park_transfer->id)
            ->orderBy('car_park_transfer_logs.transfer_date', 'desc')
            ->get();

        $job_name = null;
        if ($car_park_transfer->driving_job && $car_park_transfer->driving_job->job_type) {
            if (strcmp($car_park_transfer->driving_job->job_type, Rental::class) == 0) {
                $job_name = $car_park_transfer->driving_job->job->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job->job_type, LongTermRental::class) == 0) {
                $job_name = $car_park_transfer->driving_job->job->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job->job_type, ImportCar::class) == 0) {
                $job_name = $car_park_transfer->driving_job->job->purchaseOrder->po_no;
            } else if (strcmp($car_park_transfer->driving_job->job_type, InstallEquipment::class) == 0) {
                $job_name = $car_park_transfer->driving_job->job->worksheet_no;
            } else if (strcmp($car_park_transfer->driving_job->job_type, RepairOrder::class) == 0) {
                $job_name = $car_park_transfer->driving_job->job->worksheet_no;
            }
        }
        $car_text = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where('id', $car_park_transfer->car_id)
            ->first();
        $car_name = null;
        if ($car_text && $car_text->license_plate) {
            $car_name = $car_text->license_plate;
        } else if ($car_text && $car_text->engine_no) {
            $car_name = __('inspection_cars.engine_no') . ' ' . $car_text->engine_no;
        } else if ($car_text && $car_text->chassis_no) {
            $car_name = __('inspection_cars.chassis_no') . ' ' . $car_text->chassis_no;
        }

        $is_difference_branch_list =  $this->getYesNoList();
        $is_singular_list = $this->getYesNoList([
            __('car_park_transfers.is_singular_1'),
            __('car_park_transfers.is_singular_0'),
        ]);
        $branch_list = $this->getBranchList();

        $page_title = __('lang.view') . __('car_park_transfers.license_table');
        return view('admin.car-park-transfers.form',  [
            'd' => $car_park_transfer,
            'page_title' => $page_title,
            'transfer_type' => $transfer_type,
            'car' => $car,
            'car_status' => $car_status,
            'view' => true,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'transfer_logs' => $transfer_logs,
            'driving_jobs' => $driving_jobs,
            'job_name' => $job_name,
            'car_name' => $car_name,
            'is_difference_branch_list' => $is_difference_branch_list,
            'is_singular_list' => $is_singular_list,
            'branch_list' => $branch_list
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarParkTransfer);
        $validator = Validator::make($request->all(), [
            // 'transfer_type' => ['required'],
            /* 'driving_job_id' => 'required', */
            'est_transfer_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . date('Y-m-d'),
            'origin_branch_id' => 'required_if:is_difference_branch,1',
            'destination_branch_id' => 'required_if:is_difference_branch,1',
            // 'start_date' => ['required'],
            // 'end_date' => ['required'],
        ], [], [
            // 'transfer_type' => __('car_park_transfers.transfer_type'),
            /* 'driving_job_id' => __('car_park_transfers.driving_job'), */
            'is_difference_branch' => __('car_park_transfers.is_difference_branch'),
            'est_transfer_date' => __('car_park_transfers.est_transfer_date'),
            'origin_branch_id' => __('car_park_transfers.origin_branch_id'),
            'destination_branch_id' => __('car_park_transfers.destination_branch_id')
            // 'start_date' => __('car_park_transfers.start_date'),
            // 'end_date' => __('car_park_transfers.end_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_park_transfer = CarParkTransfer::firstOrNew(['id' => $request->id]);
        $car_park_transfer_count = CarParkTransfer::all()->count() + 1;
        $prefix = 'CT';
        if (!($car_park_transfer->exists)) {
            $car_park_transfer->worksheet_no = generateRecordNumber($prefix, $car_park_transfer_count);
            $car_park_transfer->branch_id = get_branch_id();
        }

        $is_difference_branch = boolval($request->is_difference_branch);
        $origin_branch_id = null;
        $destination_branch_id = null;
        if ($is_difference_branch) {
            $origin_branch_id = $request->origin_branch_id;
            $destination_branch_id = $request->destination_branch_id;
        }

        //$car_park_transfer->transfer_type = boolval($request->transfer_type);
        $car_park_transfer->reason = $request->reason;
        //$car_park_transfer->est_transfer_date = $request->est_transfer_date;
        $car_park_transfer->start_date = $request->start_date;
        $car_park_transfer->end_date = $request->end_date;
        $car_park_transfer->is_difference_branch = $is_difference_branch;
        $car_park_transfer->origin_branch_id = $origin_branch_id;
        $car_park_transfer->destination_branch_id = $destination_branch_id;
        $car_park_transfer->is_singular = boolval($request->is_singular);
        //$car_park_transfer->car_id = $request->car_id;
        // check car status && car_statuses table again
        // $car_park_transfer->car_status_id = $request->car_status_id;
        //$car_park_transfer->driving_job_id = $request->driving_job_id;
        // $car_park_transfer->car_park_id = $request->car_park_id;
        $car_park_transfer->status = STATUS_ACTIVE;
        $car_park_transfer->save();

        if (!empty($request->est_transfer_date)) {
            $est_transfer_date = $request->est_transfer_date;
            if (!empty($car_park_transfer->car_park_id)) {
                ClearCarParkBooked::dispatch($car_park_transfer->car_park_id);
                $car_park_transfer->car_park_id = null;
                $car_park_transfer->save();
            }
            $today = date('Y-m-d');
            if ($est_transfer_date <= $today) {
                SetCarParkBooked::dispatch($car_park_transfer->car_id, $car_park_transfer->id);
            }
        }

        $redirect_route = route('admin.car-park-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function updateStatus(Request $request)
    {
        if (empty($request->cancel_reason)) {
            $validator = Validator::make($request->all(), [
                'cancel_reason' => [
                    'required', 'max:255'
                ],
            ], [], [
                'cancel_reason' => __('lang.reason'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if (!empty($request->car_park_transfer)) {
            foreach ($request->car_park_transfer as $car_park_transfer_id) {
                $cancel_car_park_transfer = CarParkTransfer::find($car_park_transfer_id);
                $cancel_car_park_transfer->status = $request->cancel_status;
                $cancel_car_park_transfer->cancel_reason = $request->cancel_reason;
                $cancel_car_park_transfer->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' =>  route('admin.car-park-transfers.index'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => route('admin.car-park-transfers.index'),
            ]);
        }
    }

    public function destroy()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarParkTransfer);
        $redirect_route = route('admin.car-park-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getTransferType()
    {
        return collect([
            (object)[
                'id' => TransferTypeEnum::IN,
                'value' => TransferTypeEnum::IN,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::IN),
            ],
            (object)[
                'id' => TransferTypeEnum::OUT,
                'value' => TransferTypeEnum::OUT,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::OUT),
            ],

        ]);
    }

    public function getStatus()
    {
        $status = collect([
            (object) [
                'id' => 0,
                'name' => __('car_park_transfers.text_warning'),
                'value' => 0,
            ],
            (object) [
                'id' => 1,
                'name' => __('car_park_transfers.text_info'),
                'value' => 1,
            ],
            (object) [
                'id' => 3,
                'name' => __('car_park_transfers.text_success'),
                'value' => 3,
            ],
            (object) [
                'id' => STATUS_INACTIVE,
                'name' => __('car_park_transfers.text_danger'),
                'value' => STATUS_INACTIVE,
            ],
        ]);
        return $status;
    }

    function getDefaultCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = DB::table('cars')
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'cars.status as status',
            )
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->join('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->join('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->join('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('cars.id', $car_id)
            ->get()
            ->map(function ($item) {
                $item->car_status = __('cars.status_' . $item->status);
                return $item;
            })
            ->toArray();
        return [
            'success' => true,
            'car_id' => $request->car_id,
            'data' => $data
        ];
    }

    function getDefaultCarZone(Request $request)
    {
        $car_id = $request->car_id;
        $data = DB::table('cars')
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->join('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->Join('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->Join('car_park_areas_relation', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->Join('car_parks', 'car_parks.car_park_area_id', '=', 'car_park_areas_relation.car_park_area_id')
            ->Join('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->Join('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('cars.id', $car_id)
            ->whereNull('car_parks.car_id')
            ->whereNull('car_park_zones.deleted_at')
            ->where(function ($query) use ($request) {
                if (!empty($request->car_park_area_id)) {
                    $query->where('car_park_areas.id', $request->car_park_area_id);
                }
            })
            ->select(
                'car_park_areas.car_park_zone_id as car_park_zone_id',
                'car_park_zones.name as car_park_zone_name',
                'car_park_zones.code as car_park_zone_code',
                'car_parks.id as car_park_id',
                'car_parks.car_park_number as car_park_number',
            )->get();

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    function getDefaultDrivingJob(Request $request)
    {
        $driving_job_id = $request->driving_job_id;
        $driving_jobs = DrivingJob::where('id', $driving_job_id)->get();
        $driving_jobs->map(function ($item) {
            $start_date_new =  new DateTime($item->start_date);
            $end_date_new =  new DateTime($item->end_date);
            $item->rental_no = null;
            if (in_array($item->job_type, [Rental::class,  LongTermRental::class])) {
                if ($item->job && $item->job->worksheet_no) {
                    $item->rental_no = $item->job->worksheet_no;
                }
            }
            $item->driver_name = $item->driver ? $item->driver->name : null;
            $item->rental_type =  __('driving_jobs.job_type_' . $item->job_type);
            $item->start_date =  $start_date_new->format('Y-m-d');
            $item->end_date =  $end_date_new->format('Y-m-d');
            // $item->car_driving_id = null;
            if ($item->job_type === LongTermRental::class) {
                // To Do
            }
            if ($item->job_type === Rental::class) {
                if ($item->job && $item->job->id) {
                    $rental_line = RentalLine::where('rental_id', $item->job->id)
                        ->whereNotNull('car_id')
                        ->first();
                    if ($rental_line) {
                        $car = Car::find($rental_line->car_id);
                        $item->car_id = $car->id;
                    }
                }
            }
            if ($item->job_type === ImportCar::class) {
                $item->car_id = $item->car_id;
                $item->rental_no = $item->job->purchaseOrder->po_no;;
            }
            if ($item->job_type === DrivingJobTypeStatusEnum::OTHER) {
                if ($item->car_id) {
                    $item->car_id = $item->car_id;
                }
            }


            return $item;
        })->toArray();
        return [
            'success' => true,
            'driving_job_id' => $request->driving_job_id,
            'data' => $driving_jobs
        ];
    }
}
