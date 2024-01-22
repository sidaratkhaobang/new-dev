<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\DrivingJobStatusEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\WageCalDay;
use App\Enums\WageCalTime;
use App\Enums\WageCalType;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarAuction;
use App\Models\CarPark;
use App\Models\CarParkTransfer;
use App\Models\Driver;
use App\Models\DriverWage;
use App\Models\DriverWageJob;
use App\Models\DriverWageRelation;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\LongTermRental;
use App\Models\Rental;
use App\Models\ReplacementCar;
use App\Models\ServiceType;
use App\Models\TransferCar;
use App\Models\Repair;
use App\Models\RepairOrder;
use Carbon\Carbon;
use GuzzleHttp\TransferStats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Factories\DrivingJobFactory;

class DrivingJobController extends Controller
{
    const RENTAL_MODEL = 'App\\\\Models\\\\Rental';
    const LONG_TERM_RENTAL_MODEL = 'App\\\\Models\\\\LongTermRental';
    const IMPORT_CAR_LINE_MODEL = 'App\\\\Models\\\\ImportCarLine';
    const TRANSFER_CAR_MODEL = 'App\\\\Models\\\\TransferCar';
    const OTHER = 'OTHER';

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DrivingJob);

        $worksheet_no_list = DrivingJob::select('worksheet_no as name', 'id')->orderBy('worksheet_no')->get();
        $work_status_list = $this->getStatusList();
        $job_list = $this->getJobList();
        $self_drive_types = $this->getSelfDriveType();
        $is_confirm_wages = $this->getIsConfirmWagesList();
        $worksheet_no = $request->worksheet_no;
        $work_status = $request->work_status;
        $job_type = $request->job_type;
        $driver_id = $request->driver_id;
        $self_drive_type = $request->self_drive_type;
        $driver_name = find_name_by_id($request->driver_id, Driver::class);

        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $pickup_return_date = null;
        $start_date = null;

        $is_confirm_wage = is_null($request->is_confirm_wage) ? null : intval($request->is_confirm_wage);

        $rental_subquery = Rental::select(
            'rentals.id',
            'rentals.worksheet_no as ref_worksheet_no',
            'service_types.name as service_type_name',
            'rentals.pickup_date as ref_start_date',
            'rentals.return_date as ref_end_date',
        )
            ->leftjoin('service_types', 'service_types.id', '=', 'rentals.service_type_id');


        $import_car_line_subquery = ImportCarLine::select(
            'import_car_lines.id',
            'purchase_orders.po_no as ref_worksheet_no',
            // 'service_types.name as service_type_name',
            'import_car_lines.delivery_date as ref_start_date',
        )
            ->leftjoin('import_cars', 'import_cars.id', '=', 'import_car_lines.import_car_id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'import_cars.po_id');

        $main_query = DrivingJob::select(
            'driving_jobs.id',
            'driving_jobs.worksheet_no',
            'driving_jobs.driver_name',
            'driving_jobs.job_type',
            'driving_jobs.job_id',
            'rentals.service_type_name',
            'driving_jobs.is_confirm_wage',
            'driving_jobs.status',
            'driving_jobs.self_drive_type',
            'driving_jobs.branch_id',
        )
            ->addSelect(
                DB::raw("
        CASE
            WHEN driving_jobs.job_type = '" . self::RENTAL_MODEL . "' THEN rentals.ref_worksheet_no
            WHEN driving_jobs.job_type = '" . self::LONG_TERM_RENTAL_MODEL . "' THEN lt_rentals.worksheet_no
            WHEN driving_jobs.job_type = '" . self::IMPORT_CAR_LINE_MODEL . "' THEN import_car_lines.ref_worksheet_no
            WHEN driving_jobs.job_type = '" . self::TRANSFER_CAR_MODEL . "' THEN transfer_cars.worksheet_no
            ELSE null
        END as ref_worksheet_no"),
                DB::raw("
        CASE
            WHEN driving_jobs.job_type = '" . self::RENTAL_MODEL . "' THEN rentals.ref_start_date
            WHEN driving_jobs.job_type = '" . self::LONG_TERM_RENTAL_MODEL . "' THEN lt_rentals.contract_start_date
            WHEN driving_jobs.job_type = '" . self::IMPORT_CAR_LINE_MODEL . "' THEN import_car_lines.ref_start_date
            WHEN driving_jobs.job_type = '" . self::TRANSFER_CAR_MODEL . "' THEN transfer_cars.delivery_date
            WHEN driving_jobs.job_type = '" . self::OTHER . "' THEN driving_jobs.start_date
            ELSE null
        END as ref_start_date"),
                DB::raw("
        CASE
            WHEN driving_jobs.job_type = '" . self::RENTAL_MODEL . "' THEN rentals.ref_end_date
            WHEN driving_jobs.job_type = '" . self::LONG_TERM_RENTAL_MODEL . "' THEN lt_rentals.contract_end_date
            WHEN driving_jobs.job_type = '" . self::OTHER . "' THEN driving_jobs.end_date
            ELSE null
        END as ref_end_date")
            )
            ->leftJoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->leftJoinSub($rental_subquery, 'rentals', function ($join) {
                $join->on('rentals.id', '=', 'driving_jobs.job_id');
            })
            ->leftJoinSub($import_car_line_subquery, 'import_car_lines', function ($join) {
                $join->on('import_car_lines.id', '=', 'driving_jobs.job_id');
            })
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'driving_jobs.job_id')
            ->leftjoin('transfer_cars', 'transfer_cars.id', '=', 'driving_jobs.job_id')
            ->where('driving_jobs.branch_id', get_branch_id());

        $list = DrivingJob::select('driving_jobs2.*')->joinSub($main_query, 'driving_jobs2', function ($join) {
            $join->on('driving_jobs2.id', '=', 'driving_jobs.id');
        })
            ->when($job_type, function ($query) use ($job_type) {
                $query->where('driving_jobs2.job_type', $job_type);
            })
            ->when($self_drive_type, function ($query) use ($self_drive_type) {
                $query->where('driving_jobs2.self_drive_type', $self_drive_type);
            })
            ->where(function ($query) use ($is_confirm_wage) {
                if (!is_null($is_confirm_wage)) {
                    $query->where('driving_jobs2.is_confirm_wage', $is_confirm_wage);
                }
            })
            ->where(function ($query) use ($from_date, $to_date) {
                if ((!empty($from_date)) && (!empty($to_date))) {
                    $query->whereDate('driving_jobs2.ref_start_date', '>=', $from_date);
                    $query->whereDate('driving_jobs2.ref_start_date', '<=', $to_date);
                } else if (!empty($from_date)) {
                    $query->whereDate('driving_jobs2.ref_start_date', '>=', $from_date);
                } else if (!empty($to_date)) {
                    $query->whereDate('driving_jobs2.ref_start_date', '<=', $to_date);
                }
            })
            ->where('driving_jobs2.branch_id', get_branch_id())
            ->search($request->s, $request)
            ->sortable(['created_at' => 'desc'])
            ->orderBy('driving_jobs2.worksheet_no')
            ->paginate(PER_PAGE);

        $rental_model = Rental::class;
        $lt_rental_model = LongTermRental::class;
        $import_car_line_model = ImportCarLine::class;
        $transfer_car_model = TransferCar::class;

        return view('admin.driving-jobs.index', [
            's' => $request->s,
            'list' => $list,
            'worksheet_no_list' => $worksheet_no_list,
            'worksheet_no' => $worksheet_no,
            'work_status_list' => $work_status_list,
            'work_status' => $work_status,
            'job_list' => $job_list,
            'job_type' => $job_type,
            'driver_id' => $driver_id,
            'driver_name' => $driver_name,
            'pickup_return_date' => $pickup_return_date,
            'start_date' => $start_date,
            'self_drive_types' => $self_drive_types,
            'self_drive_type' => $self_drive_type,
            'is_confirm_wages' => $is_confirm_wages,
            'is_confirm_wage' => $is_confirm_wage,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'rental_model' => $rental_model,
            'lt_rental_model' => $lt_rental_model,
            'import_car_line_model' => $import_car_line_model,
            'transfer_car_model' => $transfer_car_model,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingJob);
        $d = new DrivingJob();
        $d->job_type = DrivingJobTypeStatusEnum::OTHER;
        $d->driving_job_type = DrivingJobTypeStatusEnum::SIDE_JOB;
        $job_list = $this->getJobList();
        $self_drive_types = $this->getSelfDriveTypeCreate();
        $driver_name = null;
        $car_name = null;
        /* $job_name = null;
        $driver_wage_job_list = [];
        $parent_no = null;
        $service_type_rental = null;
        $self_drive_sataus = null;
        $status_name = null; */
        //$short_term_model = Rental::class;
        /* $car_type_list = $this->getRentalType();
        $cars = Car::select('id', 'license_plate as name')->get(); */
        $page_title = __('lang.create') . __('driving_jobs.page_title');
        return view('admin.driving-jobs.form-other', [
            'd' => $d,
            'page_title' => $page_title,
            'job_list' => $job_list,
            'driver_name' => $driver_name,
            /* 'job_name' => $job_name,
            'driver_wage_job_list' => $driver_wage_job_list,
            'parent_no' => $parent_no,
            'car_type_list' => $car_type_list,
            'cars' => $cars,
            'service_type_rental' => $service_type_rental,
            'self_drive_sataus' => $self_drive_sataus, */
            'self_drive_types' => $self_drive_types,
            //'short_term_model' => $short_term_model,
            'car_name' => $car_name,
            /* 'status_name' => $status_name, */
            'zone' => null,
            'page' => 'info',
            'car' => null
        ]);
    }

    public function edit(DrivingJob $driving_job, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingJob);
        $page = $request->page ?: 'info';

        //$job_list = $this->getJobList();
        $self_drive_types = $this->getSelfDriveType();
        $driver_name = $driving_job->driver ? $driving_job->driver->name : null;
        //$job_name = null;

        if (!in_array($driving_job->job_type, [DrivingJobTypeStatusEnum::OTHER])) {
            if (!$driving_job->job) {
                return redirect()->back();
            }
        }

        /* if (in_array($driving_job->job_type, [Rental::class, LongTermRental::class, RepairOrder::class])) {
            $job_name = ($driving_job->job) ? $driving_job->job->worksheet_no : '';
            if (strcmp($driving_job->job_type, Rental::class) === 0) {
                $rental = Rental::find($driving_job->job_id);
                $origin_remark = null;
                $destination_remark = null;
                if ($rental) {
                    if ($rental->origin_remark) {
                        $origin_remark = "($rental->origin_remark)";
                    }
                    if ($rental->destination_remark) {
                        $destination_remark = "($rental->destination_remark)";
                    }
                    $driving_job->parent_customer = $rental->customer_name . '(' . $rental->customer_tel . ')';
                    $driving_job->rental_start_date = ($rental->pickup_date) ? $rental->pickup_date : null;
                    $driving_job->rental_end_date = ($rental->return_date) ? $rental->return_date : null;
                    $driving_job->rental_origin = ($rental->origin) ? $rental->origin->name . $origin_remark : $rental->origin_name . $origin_remark;
                    $driving_job->rental_destination = ($rental->destination) ? $rental->destination->name . $destination_remark : $rental->destination_name . $destination_remark;
                }
            }

            if (strcmp($driving_job->job_type, LongTermRental::class) === 0) {
                $long_rental = LongTermRental::find($driving_job->job_id);
                if ($long_rental) {
                    $driving_job->parent_customer = $long_rental->customer_name . '(' . $long_rental->customer_tel . ')';
                    $driving_job->contract_start_date = ($long_rental->contract_start_date) ? $long_rental->contract_start_date : null;
                    $driving_job->contract_end_date = ($long_rental->contract_end_date) ? $long_rental->contract_end_date : null;
                }
            }
        } else {
            if (strcmp($driving_job->job_type, ImportCarLine::class) === 0) {
                $import_car_line = ImportCarLine::find($driving_job->job_id);
                $job_name = '';
                if ($import_car_line) {
                    $driving_job->delivery_date = ($import_car_line->delivery_date) ? $import_car_line->delivery_date : null;
                    $driving_job->import_delivery_place = ($import_car_line->delivery_location) ? $import_car_line->delivery_location : null;
                    $import_car = ImportCar::find($import_car_line->import_car_id);
                    if ($import_car) {
                        $driving_job->dealer = ($import_car->purchaseOrder && $import_car->purchaseOrder->creditor) ? $import_car->purchaseOrder->creditor->name : null;
                        $job_name = ($import_car->purchaseOrder) ? $import_car->purchaseOrder->po_no : '';
                        // $import_car_line = ImportCarLine::select('id', 'delivery_date', 'delivery_location')->where('id', $driving_job->car_id)->where('import_car_id', $import_car->id)->first();
                    }
                }
            } else {
                if (strcmp($driving_job->job_type, DrivingJobTypeStatusEnum::OTHER) == 0) {
                    $job_name = null;
                } else {
                    if (strcmp($driving_job->job_type, InstallEquipment::class) == 0) {
                        $job_name = ($driving_job->job) ? $driving_job->job->worksheet_no : '';
                        if ($driving_job->job) {
                            $driving_job->supplier_name = $driving_job->job->supplier ? $driving_job->job->supplier->name : '';
                            $driving_job->ie_destination = $driving_job->job->supplier ? $driving_job->job->supplier->address : '';
                        }
                    }
                }
            }
        } */
        /* $car_text = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where('id', $driving_job->car_id)
            ->first();
        $car_name = null;
        if ($car_text && $car_text->license_plate) {
            $car_name = $car_text->license_plate;
        } else {
            if ($car_text && $car_text->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car_text->engine_no;
            } else {
                if ($car_text && $car_text->chassis_no) {
                    $car_name = __('inspection_cars.chassis_no') . ' ' . $car_text->chassis_no;
                }
            }
        } */

        $driver_wage_job_list = $this->getDriverWageJobList($driving_job->id);
        $parent_no = null;
        //$short_term_model = Rental::class;
        /* if (!empty($driving_job->parent_id)) {
            $parent = DrivingJob::find($driving_job->parent_id);
            if ($parent) {
                $parent_no = $parent->worksheet_no;
            }
        } */

        /* $service_type = (in_array($driving_job->job_type, [Rental::class, LongTermRental::class]) && $driving_job->job->serviceType) ? $driving_job->job->serviceType->service_type : null;
        $service_type_rental = null;
        if (!empty($service_type)) {
            $service_type_rental = __('driving_jobs.service_type_' . $service_type);
        } */

        /* if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::PICKUP) == 0) {
            $self_drive_sataus = SelfDriveTypeEnum::PICKUP;
        } else {
            if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::SEND) == 0) {
                $self_drive_sataus = SelfDriveTypeEnum::SEND;
            } else {
                $self_drive_sataus = SelfDriveTypeEnum::OTHER;
            }
        } */

        /* $status_name = null;
        if ($driving_job->status) {
            $status_name = __('driving_jobs.status_' . $driving_job->status . '_text');
        }

        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $driving_job->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();

        $car_type_list = $this->getRentalType();
        $cars = Car::select('id', 'license_plate as name')->get(); */
        $page_title = __('lang.edit') . __('driving_jobs.page_title');

        $car = get_car_detail($driving_job->car_id);

        $view_blade = 'admin.driving-jobs.form';
        if (strcmp($driving_job->job_type, DrivingJobTypeStatusEnum::OTHER) == 0) {
            $view_blade = 'admin.driving-jobs.form-other';
        }

        return view($view_blade, [
            'd' => $driving_job,
            'page' => $page,
            'page_title' => $page_title,
            'driver_name' => $driver_name,
            'driver_wage_job_list' => $driver_wage_job_list,
            /* 'job_list' => $job_list,
            'job_name' => $job_name,
            'driver_wage_job_list' => $driver_wage_job_list,
            'parent_no' => $parent_no,
            'car_type_list' => $car_type_list,
            'cars' => $cars,
            'service_type_rental' => $service_type_rental,
            'self_drive_sataus' => $self_drive_sataus, */
            'self_drive_types' => $self_drive_types,
            //'short_term_model' => $short_term_model,
            /* 'car_name' => $car_name, */
            /* 'status_name' => $status_name,
            'zone' => $zone, */
            'car' => $car
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingJob);
        $driving_job = DrivingJob::find($id);
        $driving_job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function show(DrivingJob $driving_job, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DrivingJob);
        $page = $request->page ?: 'info';
        /* $job_list = $this->getJobList(); */
        $self_drive_types = $this->getSelfDriveType();
        $driver_name = $driving_job->driver ? $driving_job->driver->name : null;
        /* $job_name = null; */
        if (!in_array($driving_job->job_type, [DrivingJobTypeStatusEnum::OTHER])) {
            if (!$driving_job->job) {
                return redirect()->back();
            }
        }
        /* if (in_array($driving_job->job_type, [Rental::class, LongTermRental::class, RepairOrder::class])) {
            $job_name = ($driving_job->job) ? $driving_job->job->worksheet_no : '';
            if (strcmp($driving_job->job_type, Rental::class) === 0) {
                $rental = Rental::find($driving_job->job_id);
                $origin_remark = null;
                $destination_remark = null;
                if ($rental) {
                    if ($rental->origin_remark) {
                        $origin_remark = "($rental->origin_remark)";
                    }
                    if ($rental->destination_remark) {
                        $destination_remark = "($rental->destination_remark)";
                    }
                    $driving_job->parent_customer = $rental->customer_name . '(' . $rental->customer_tel . ')';
                    $driving_job->rental_start_date = ($rental->pickup_date) ? $rental->pickup_date : null;
                    $driving_job->rental_end_date = ($rental->return_date) ? $rental->return_date : null;
                    $driving_job->rental_origin = ($rental->origin) ? $rental->origin->name . $origin_remark : $rental->origin_name . $origin_remark;
                    $driving_job->rental_destination = ($rental->destination) ? $rental->destination->name . $destination_remark : $rental->destination_name . $destination_remark;
                }
            }

            if (strcmp($driving_job->job_type, LongTermRental::class) === 0) {
                $long_rental = LongTermRental::find($driving_job->job_id);
                if ($long_rental) {
                    $driving_job->parent_customer = $long_rental->customer_name . '(' . $long_rental->customer_tel . ')';
                    $driving_job->contract_start_date = ($long_rental->contract_start_date) ? $long_rental->contract_start_date : null;
                    $driving_job->contract_end_date = ($long_rental->contract_end_date) ? $long_rental->contract_end_date : null;
                }
            }
        } else {
            if (strcmp($driving_job->job_type, ImportCarLine::class) === 0) {
                $import_car_line = ImportCarLine::find($driving_job->job_id);
                $job_name = '';
                if ($import_car_line) {
                    $driving_job->delivery_date = ($import_car_line->delivery_date) ? $import_car_line->delivery_date : null;
                    $driving_job->import_delivery_place = ($import_car_line->delivery_location) ? $import_car_line->delivery_location : null;
                    $import_car = ImportCar::find($import_car_line->import_car_id);
                    if ($import_car) {
                        $driving_job->dealer = ($import_car->purchaseOrder && $import_car->purchaseOrder->creditor) ? $import_car->purchaseOrder->creditor->name : null;
                        $job_name = ($import_car->purchaseOrder) ? $import_car->purchaseOrder->po_no : '';
                        // $import_car_line = ImportCarLine::select('id', 'delivery_date', 'delivery_location')->where('id', $driving_job->car_id)->where('import_car_id', $import_car->id)->first();
                    }
                }
            } else {
                if (strcmp($driving_job->job_type, InstallEquipment::class) == 0) {
                    $job_name = ($driving_job->job) ? $driving_job->job->worksheet_no : '';
                    if ($driving_job->job) {
                        $driving_job->supplier_name = $driving_job->job->supplier ? $driving_job->job->supplier->name : '';
                        $driving_job->ie_destination = $driving_job->job->supplier ? $driving_job->job->supplier->address : '';
                    }
                }
            }
        } */

        /* $car_text = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where('id', $driving_job->car_id)
            ->first();
        $car_name = null;
        if ($car_text && $car_text->license_plate) {
            $car_name = $car_text->license_plate;
        } else {
            if ($car_text && $car_text->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car_text->engine_no;
            } else {
                if ($car_text && $car_text->chassis_no) {
                    $car_name = __('inspection_cars.chassis_no') . ' ' . $car_text->chassis_no;
                }
            }
        } */

        $driver_wage_job_list = $this->getDriverWageJobList($driving_job->id);
        /* $status_job = true;
        $parent_no = null;
        $short_term_model = Rental::class;
        if (!empty($driving_job->parent_id)) {
            $parent = DrivingJob::find($driving_job->parent_id);
            if ($parent) {
                $parent_no = $parent->worksheet_no;
            }
        }
        $service_type = (in_array($driving_job->job_type, [Rental::class, LongTermRental::class]) && $driving_job->job->serviceType) ? $driving_job->job->serviceType->service_type : null;
        $service_type_rental = null;
        if (!empty($service_type)) {
            $service_type_rental = __('driving_jobs.service_type_' . $service_type);
        }
        if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::PICKUP) == 0) {
            $self_drive_sataus = SelfDriveTypeEnum::PICKUP;
        } else {
            if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::SEND) == 0) {
                $self_drive_sataus = SelfDriveTypeEnum::SEND;
            } else {
                $self_drive_sataus = SelfDriveTypeEnum::OTHER;
            }
        }

        $status_name = null;
        if ($driving_job->status) {
            $status_name = __('driving_jobs.status_' . $driving_job->status . '_text');
        }

        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $driving_job->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();

        $car_type_list = $this->getRentalType();
        $cars = Car::select('id', 'license_plate as name')->get(); */

        $car = get_car_detail($driving_job->car_id);

        $view_blade = 'admin.driving-jobs.form';
        if (strcmp($driving_job->job_type, DrivingJobTypeStatusEnum::OTHER) == 0) {
            $view_blade = 'admin.driving-jobs.form-other';
        }

        $page_title = __('lang.view') . __('driving_jobs.page_title');
        return view($view_blade, [
            'd' => $driving_job,
            'page' => $page,
            'page_title' => $page_title,
            'driver_name' => $driver_name,
            'view' => true,
            /* 'job_list' => $job_list,
            'job_name' => $job_name, */
            'driver_wage_job_list' => $driver_wage_job_list,
            /* 'parent_no' => $parent_no,
            'car_type_list' => $car_type_list,
            'cars' => $cars,
            'service_type_rental' => $service_type_rental,
            'self_drive_sataus' => $self_drive_sataus, */
            'self_drive_types' => $self_drive_types,
            //'short_term_model' => $short_term_model,
            /* 'car_name' => $car_name, */
            /* 'status_name' => $status_name,
            'zone' => $zone, */
            'car' => $car
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingJob);
        if (strcmp($request->job_type, DrivingJobTypeStatusEnum::OTHER) == 0) {
            $validator = Validator::make($request->all(), [
                'job_type' => ['required'],
                'remark' => ['required'],
                'self_drive_type' => ['required'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
                'driver_id' => ['nullable'],
                'origin' => ['required_if:self_drive_type,PICKUP', 'max:100'],
                'destination' => ['required_if:self_drive_type,SEND', 'max:100'],
                'car_id' => ['required'],
            ], [], [
                'job_type' => __('driving_jobs.worksheet_type'),
                'remark' => __('driving_jobs.description'),
                'self_drive_type' => __('driving_jobs.job_type'),
                'start_date' => __('driving_jobs.start_date'),
                'end_date' => __('driving_jobs.end_date'),
                'driver_id' => __('driving_jobs.driver_name'),
                'origin' => __('driving_jobs.origin'),
                'destination' => __('driving_jobs.destination'),
                'car_id' => __('cars.license_plate_chassis_engine'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'self_drive_type' => ['required'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
                'remark' => ['nullable'],
                'origin' => ['required_if:self_drive_type,PICKUP', 'max:100'],
                'destination' => ['required_if:self_drive_type,SEND', 'max:100'],
                'driver_id' => ['nullable'],
            ], [], [
                'remark' => __('driving_jobs.remark'),
                'self_drive_type' => __('driving_jobs.job_type'),
                'start_date' => __('driving_jobs.start_date'),
                'end_date' => __('driving_jobs.end_date'),
                'driver_id' => __('driving_jobs.driver_name'),
                'origin' => __('driving_jobs.origin'),
                'destination' => __('driving_jobs.destination'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        /* if ($request->driver_id) {
            $driver = Driver::find($request->driver_id);
            $driver_name = ($driver && $driver->name) ? $driver->name : null;
        } else {
            if ($request->driver_name) {
                $driver_name = $request->driver_name;
            } else {
                $driver_name = null;
            }
        } */

        $driving_job = DrivingJob::firstOrNew(['id' => $request->id]);
        if (!$driving_job->exists) {
            $djf = new DrivingJobFactory(DrivingJobTypeStatusEnum::OTHER, null, $request->car_id, [
                'driving_job_type' => $request->driving_job_type,
                'self_drive_type' => $request->self_drive_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'driver_id' => $request->driver_id,
                'driver_name' => find_name_by_id($request->driver_id, Driver::class),
                'remark' => $request->remark,
            ]);
            $djf->create();
        } else {
            $driving_job->start_date = $request->start_date;
            $driving_job->end_date = $request->end_date;
            $driving_job->remark = $request->remark;
            if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::SEND) == 0) {
                $driving_job->destination = $request->destination;
            } else if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::PICKUP) == 0) {
                $driving_job->origin = $request->origin;
            } else if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::OTHER) == 0) {
                $driving_job->origin = $request->origin;
                $driving_job->destination = $request->destination;
            }
            $driving_job->save();
        }

        /* if (in_array($request->status, [DrivingJobStatusEnum::COMPLETE, DrivingJobStatusEnum::CANCEL])) {
            $driving_job->status = $request->status;

            if ($request->status == DrivingJobStatusEnum::COMPLETE) {
                $wage_job_list = $this->getDefaultDriverWageJob($driving_job);

                if (!$wage_job_list['success']) {
                    return $this->responseFailed($wage_job_list['message']);
                }
                $this->saveDriverWageJob($wage_job_list['data'], $driving_job);
            }
        } */

        $this->saveDriverWageJob($request->wage_job, $driving_job);


        /* $driving_job->save();

        $car_park_transfer = CarParkTransfer::firstOrNew(['driving_job_id' => $driving_job->id]);;
        $car_park_transfer_count = CarParkTransfer::all()->count() + 1;
        $prefix = 'CT';
        if (!($car_park_transfer->exists)) {
            $car_park_transfer->worksheet_no = generateRecordNumber($prefix, $car_park_transfer_count);
        }
        $car_park_transfer->driving_job_id = $driving_job->id;
        $car_park_transfer->car_id = $driving_job->car_id;
        $car_park_transfer->start_date = $driving_job->start_date;
        $car_park_transfer->end_date = $driving_job->end_date;
        $car_park_transfer->save(); */

        $redirect_route = route('admin.driving-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function updateStatusJob(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingJob);
        $driving_job = DrivingJob::where('id', $request->id)->first();

        if ($request->is_confirm_wage == BOOL_TRUE) {
            $driving_job->is_confirm_wage = BOOL_TRUE;
        } else {
            $driving_job->is_confirm_wage = BOOL_FALSE;
        }
        $driving_job->save();

        if ($driving_job->id && !empty($request->wage_job)) {
            $this->saveDriverWageJob($request->wage_job, $driving_job);
        }

        $redirect_route = route('admin.driving-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public static function saveDriverWageJob($wage_job, $driving_job)
    {
        DriverWageJob::where('driving_job_id', $driving_job->id)->delete();
        foreach ($wage_job as $request_wage_job) {
            $driver_wage_job = new DriverWageJob();
            $driver_wage_job->driving_job_id = $driving_job->id;
            $driver_wage_job->driver_wage_id = $request_wage_job['driver_wage_id'];
            $driver_wage_job->driver_wage_name = $request_wage_job['driver_wage_text'];
            $driver_wage_job->amount = $request_wage_job['amount'];
            $driver_wage_job->amount_type = $request_wage_job['amount_type'];
            $driver_wage_job->remark = $request_wage_job['remark'];
            $driver_wage_job->save();
        }
        return true;
    }

    public static function getDriverWageJobList($driving_job_id)
    {
        $driver_wage_job_list = DriverWageJob::where('driving_job_id', $driving_job_id)->get();
        $driver_wage_job_list->map(function ($item) {
            $item->driving_job_id = ($item->driving_job_id) ? $item->driving_job_id : '';
            $item->driver_wage_text = $item->driver_wage_name ? $item->driver_wage_name : '';
            $item->amount = ($item->amount) ? $item->amount : '';
            $item->amount_type = ($item->amount_type) ? $item->amount_type : '';
            $item->service_type_id = $item->driver_wage?->service_type?->service_type;
            $item->remark = ($item->remark) ? $item->remark : '';
            return $item;
        });
        return $driver_wage_job_list;
    }

    public static function getConditionDriverWageJob($drivingJob)
    {
        $driver_id = $drivingJob->driver_id;

        if (!isset($drivingJob->actual_start_date)) {
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูล "เวลาเข้างาน" ของพนักงานขับรถ',
            ];
        } elseif (!isset($drivingJob->actual_end_date)) {
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูล "เวลาออกงาน" ของพนักงานขับรถ',
            ];
        } elseif (!isset($drivingJob->start_date)) {
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูล "วันที่เริ่มงาน" ของใบงานคนขับ',
            ];
        } elseif (!isset($drivingJob->end_date)) {
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูล "วันที่สิ้นสุดงาน" ของใบงานคนขับ',
            ];
        }

        $start_date = strtotime($drivingJob->start_date); // แปลงวันที่เริ่มต้นเป็น timestamp
        $end_date = strtotime($drivingJob->end_date); // แปลงวันที่สิ้นสุดเป็น timestamp

        $driver = Driver::find($driver_id);

        $start_working_time = Carbon::parse($driver->start_working_time)->format('H:i:s');
        $start_working_time = Carbon::parse($start_working_time);

        $end_working_time = Carbon::parse($driver->end_working_time)->format('H:i:s');
        $end_working_time = Carbon::parse($end_working_time);

        $actual_start_time = Carbon::parse($drivingJob->actual_start_date)->format('H:i:s');
        $actual_start_date_time = Carbon::parse($actual_start_time);

        $actual_end_time = Carbon::parse($drivingJob->actual_end_date)->format('H:i:s');
        $actual_end_date_time = Carbon::parse($actual_end_time);

        if ($actual_start_time > $actual_end_time) {
            return [
                'success' => false,
                'message' => 'รูปแบบเวลา "เข้างาน/ออกงาน" ของใบงานคนขับไม่ถูกต้อง',
            ];
        }

        if ($start_working_time > $end_working_time) {
            return [
                'success' => false,
                'message' => 'รูปแบบเวลา "เข้างาน/ออกงาน" ของพนักงานขับรถไม่ถูกต้อง',
            ];
        }

        $time_in_work = 0;
        $time_off_work = 0;

        if (($actual_start_date_time->lessThan($start_working_time) && $actual_end_date_time->lessThan($start_working_time)) || $actual_start_date_time->greaterThan($end_working_time) && $actual_end_date_time->greaterThan($end_working_time)) {
            $time_off_work = $actual_start_date_time->diffInMinutes($actual_end_date_time);
        } elseif ($actual_start_date_time->lessThan($start_working_time) && $actual_end_date_time->between($start_working_time, $end_working_time)) {
            $time_off_work = $actual_start_date_time->diffInMinutes($start_working_time);
            $time_in_work = $actual_end_date_time->diffInMinutes($start_working_time);
        } elseif ($actual_start_date_time->between($start_working_time, $end_working_time) && $actual_end_date_time->greaterThan($end_working_time)) {
            $time_off_work = $actual_end_date_time->diffInMinutes($end_working_time);
            $time_in_work = $actual_start_date_time->diffInMinutes($end_working_time);
        } elseif ($actual_start_date_time->between($start_working_time, $end_working_time) && $actual_end_date_time->between($start_working_time, $end_working_time)) {
            $time_in_work = $actual_start_date_time->diffInMinutes($actual_end_date_time);
        } elseif ($actual_start_date_time->lessThan($start_working_time) && $actual_end_date_time->greaterThan($end_working_time)) {
            $time_off_work = $actual_start_date_time->diffInMinutes($start_working_time) + $actual_end_date_time->diffInMinutes($end_working_time);
            $start_working_time = Carbon::parse($driver->start_working_time);
            $time_in_work = $start_working_time->diffInMinutes($end_working_time);
        }

        $is_off_work_day = false;       // วันเริ่มต้น/สิ้นสุดงานขับรถ ตรงกับ วันทำหยุด ของคนขับอย่างน้อย 1 วันหรือไม่ ?
        $is_work_day = false;           // วันเริ่มต้น/สิ้นสุดงานขับรถ ตรงกับ วันทำงาน ของคนขับอย่างน้อย 1 วันหรือไม่ ?

        $list_count_work_day = [];      // นับจำนวนวันทำงานแบบแยกวัน
        $list_count_off_work_day = [];  // นับจำนวนวันหยุดแบบแยกวัน

        $temp_start_date = $start_date;

        // วนลูปเพื่อเช็ควันของงานขับรถทั้งหมด ว่าตรงกับวันหยุด หรือ วันทำงานของคนขับ หรือไม่ ?
        while ($temp_start_date <= $end_date) {
            $dayOfWeek = date('D', $temp_start_date);            // แปลง timestamp เป็นชื่อวันในภาษาอังกฤษ (ตัวย่อ)

            $getKey = 'working_day_' . strtolower($dayOfWeek);
            if ($driver->$getKey == 0) {                    // เช็คว่าวันดังกล่าวเป็นวันหยุดของคนขับหรือไม่ ?
                if (!isset($OFF_WORK_DAY[$dayOfWeek])) {    // เช็คว่าเก็บวันหยุดดังกล่าวไปหรือยัง ?
                    $is_off_work_day = true;
                    $list_count_off_work_day[$dayOfWeek] = 1;
                } else {
                    $list_count_off_work_day[$dayOfWeek] += 1;
                }
            } elseif ($driver->$getKey == 1) {              // เช็คว่าวันดังกล่าวเป็นวันทำงานของคนขับหรือไม่ ?
                if (!isset($WORK_DAY[$dayOfWeek])) {        // เช็คว่าเก็บวันทำงานดังกล่าวไปหรือยัง ?
                    $is_work_day = true;
                    $list_count_work_day[$dayOfWeek] = 1;
                } else {
                    $list_count_work_day[$dayOfWeek] += 1;
                }
            }
            $temp_start_date = strtotime('+1 day', $temp_start_date); // เพิ่มวันที่ต่อไป
        }

        $actual_start_time = $actual_start_date_time->toTimeString();
        $actual_end_time = $actual_end_date_time->toTimeString();

        return [
            'driving_job_actual_time' => [                                          // เวลา เริ่มงาน/สิ้นสุดงาน ของงานขับรถ
                'start' => $actual_start_time,
                'end' => $actual_end_time
            ],

            'driver_date' => [                                                      // วัน ทำงาน/วันหยุด ของพนักงานขับรถ (true = วันทำงาน | false = วันหยุด)
                'Mon' => $driver->working_day_mon != STATUS_DEFAULT,
                'Tue' => $driver->working_day_tue != STATUS_DEFAULT,
                'Wed' => $driver->working_day_wed != STATUS_DEFAULT,
                'Thu' => $driver->working_day_thu != STATUS_DEFAULT,
                'Fri' => $driver->working_day_fri != STATUS_DEFAULT,
                'Sat' => $driver->working_day_sat != STATUS_DEFAULT,
                'Sun' => $driver->working_day_sun != STATUS_DEFAULT,
            ],

            'driver_time' => [                                                      // เวลา ทำงาน ของพนักงานขับรถ
                'start' => $driver->start_working_time,
                'end' => $driver->end_working_time,
            ],

            'list_count_work_day' => $list_count_work_day,                          // จำนวนวันทำงานแบบแยกวัน
            'list_count_off_work_day' => $list_count_off_work_day,                  // จำนวนวันหยุดแบบแยกวัน

            'is_work_day' => $is_work_day,                                          // ตรงกับวันทำงานหรือไม่
            'is_off_work_day' => $is_off_work_day,                                  // ตรงกับวันหยุดหรือไม่

            'time_in_work' => $time_in_work,
            'time_off_work' => $time_off_work,

            'success' => true,
        ];
    }

    public function cloneWageJob()
    {
        $driverWages = DriverWage::get();
        $serviceTypes = ServiceType::get();
        $arr = [];

        foreach ($serviceTypes as $serviceType) {
            foreach ($driverWages as $driverWage) {
                $driver_wages = new DriverWage();
                $driver_wages->name = $serviceType->name . ' > ' . $driverWage->name;
                $driver_wages->service_type_id = $serviceType->id;
                $driver_wages->driver_wage_category_id = $driverWage->driver_wage_category_id;
                $driver_wages->seq = $driverWage->seq;
                $driver_wages->is_standard = $driverWage->is_standard;
                $driver_wages->wage_cal_type = $driverWage->wage_cal_type;
                $driver_wages->wage_cal_day = $driverWage->wage_cal_day;
                $driver_wages->wage_cal_time = $driverWage->wage_cal_time;
                $driver_wages->status = $driverWage->status;
                $driver_wages->is_special_wage = $driverWage->is_special_wage;
                $driver_wages->save();
                $arr[] = $driver_wages->getAttributes();
            }
        }

        return $arr;
    }

    public function getDefaultDriverWageJob(DrivingJob $drivingJob)
    {
        $result_condition = $this->getConditionDriverWageJob($drivingJob);

        if (!$result_condition['success']) {
            return $result_condition;
        }

        $is_work_day = $result_condition['is_work_day'];
        $is_off_work_day = $result_condition['is_off_work_day'];

        $time_in_work = $result_condition['time_in_work'];
        $time_off_work = $result_condition['time_off_work'];

        $driver_job_id = $drivingJob->job_id;
        $driver_job_type = $drivingJob->job_type;

        $data = DriverWageRelation::leftJoin('driver_wages', 'driver_wages.id', '=', 'driver_wages_relation.driver_wage_id')
            ->select(
                'driver_wages.id as driver_wage_id',
                'driver_wages.name as driver_wage_name',
                'driver_wages_relation.amount as driver_wage_amount',
                'driver_wages_relation.amount_type as driver_wage_amount_type',
                'driver_wages.service_type_id',
                'driver_wages.wage_cal_type',
                'driver_wages.wage_cal_day',
                'driver_wages.wage_cal_time',
            )
            ->where('driver_wages_relation.driver_id', $drivingJob->driver_id)
            ->where(function ($query) use ($is_work_day, $is_off_work_day) {
                //วันทำงาน
                if ($is_work_day) {
                    $query->orWhere('driver_wages.wage_cal_day', WageCalDay::WORK_DAY);
                }
                //วันหยุด
                if ($is_off_work_day) {
                    $query->orWhere('driver_wages.wage_cal_day', WageCalDay::HOLIDAY);
                }
                // ทั้งหมด
                $query->orWhere('driver_wages.wage_cal_day', WageCalDay::ALL);
            })
            ->where(function ($query) use ($time_in_work, $time_off_work) {
                //เวลาทำงาน
                if ($time_in_work > 0) {
                    $query->orWhere('driver_wages.wage_cal_time', WageCalTime::WORK_TIME);
                }
                //นอกเวลาทำงาน
                if ($time_off_work > 0) {
                    $query->orWhere('driver_wages.wage_cal_time', WageCalTime::OUT_OF_WORK_TIME);
                }
                // ทั้งหมด
                $query->orWhere('driver_wages.wage_cal_time', WageCalTime::ALL);
            })
            ->where(function ($query) use ($driver_job_id, $driver_job_type) {
                if (!empty($driver_job_type)) {
                    if (strcmp($driver_job_type, Rental::class) === 0) {
                        $rental = Rental::find($driver_job_id);
                        $service_type_id = $rental->serviceType->id;
                        $query->where('driver_wages.service_type_id', $service_type_id);
                    } else {
                        $query->whereNull('driver_wages.service_type_id');
                    }
                }
            })
            ->whereNotIn('driver_wages.wage_cal_type', [WageCalType::PER_MONTH])->get();
        $data->map(function ($item) {
            $item->driver_wage_id = ($item->driver_wage_id) ? $item->driver_wage_id : '';
            $item->driver_wage_text = ($item->driver_wage_name) ? $item->driver_wage_name : '';
            $item->amount = ($item->driver_wage_amount) ? $item->driver_wage_amount : '';
            $item->amount_type = ($item->driver_wage_amount_type) ? $item->driver_wage_amount_type : '';
            $item->remark = '';
            return $item;
        });

        return [
            'success' => true,
            'driver_id' => $drivingJob->driver_id,
            'data' => $data
        ];
    }

    private function getStatusList()
    {
        return collect([
            (object)[
                'id' => DrivingJobStatusEnum::INITIAL,
                // 'value' => DrivingJobStatusEnum::INITIAL,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::INITIAL . '_text'),
            ],
            (object)[
                'id' => DrivingJobStatusEnum::PENDING,
                // 'value' => DrivingJobStatusEnum::PENDING,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::PENDING . '_text'),
            ],
            (object)[
                'id' => DrivingJobStatusEnum::IN_PROCESS,
                // 'value' => DrivingJobStatusEnum::PENDING,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::IN_PROCESS . '_text'),
            ],
            (object)[
                'id' => DrivingJobStatusEnum::COMPLETE,
                // 'value' => DrivingJobStatusEnum::COMPLETE,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::COMPLETE . '_text'),
            ],
            (object)[
                'id' => DrivingJobStatusEnum::CANCEL,
                // 'value' => DrivingJobStatusEnum::CANCEL,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::CANCEL . '_text'),
            ],
        ]);
    }

    private function getJobList()
    {
        return collect([
            (object)[
                'id' => Rental::class,
                'value' => Rental::class,
                'name' => __('driving_jobs.job_type_' . Rental::class),
            ],
            (object)[
                'id' => LongTermRental::class,
                'value' => LongTermRental::class,
                'name' => __('driving_jobs.job_type_' . LongTermRental::class),
            ],
            (object)[
                'id' => ImportCarLine::class,
                'value' => ImportCarLine::class,
                'name' => __('driving_jobs.job_type_' . ImportCarLine::class),
            ],
            (object)[
                'id' => DrivingJobTypeStatusEnum::OTHER,
                'value' => DrivingJobTypeStatusEnum::OTHER,
                'name' => __('driving_jobs.job_type_' . DrivingJobTypeStatusEnum::OTHER),
            ],
            (object)[
                'id' => InstallEquipment::class,
                'value' => InstallEquipment::class,
                'name' => __('driving_jobs.job_type_' . InstallEquipment::class),
            ],
            (object)[
                'id' => TransferCar::class,
                'value' => TransferCar::class,
                'name' => __('driving_jobs.job_type_' . TransferCar::class),
            ],
            (object)[
                'id' => RepairOrder::class,
                'value' => RepairOrder::class,
                'name' => __('driving_jobs.job_type_' . RepairOrder::class),
            ],
            (object)[
                'id' => CarAuction::class,
                'value' => CarAuction::class,
                'name' => __('driving_jobs.job_type_' . CarAuction::class),
            ],
        ]);
    }

    private function getSelfDriveType()
    {
        return collect([
            (object)[
                'id' => SelfDriveTypeEnum::SEND,
                'value' => SelfDriveTypeEnum::SEND,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::SEND),
            ],
            (object)[
                'id' => SelfDriveTypeEnum::PICKUP,
                'value' => SelfDriveTypeEnum::PICKUP,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::PICKUP),
            ],
            (object)[
                'id' => SelfDriveTypeEnum::OTHER,
                'value' => SelfDriveTypeEnum::OTHER,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::OTHER),
            ],
            (object)[
                'id' => SelfDriveTypeEnum::SELF_DRIVE,
                'value' => SelfDriveTypeEnum::SELF_DRIVE,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::SELF_DRIVE),
            ],
        ]);
    }

    private function getSelfDriveTypeCreate()
    {
        return collect([
            (object)[
                'id' => SelfDriveTypeEnum::SEND,
                'value' => SelfDriveTypeEnum::SEND,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::SEND),
            ],
            (object)[
                'id' => SelfDriveTypeEnum::PICKUP,
                'value' => SelfDriveTypeEnum::PICKUP,
                'name' => __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::PICKUP),
            ],
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object)[
                'id' => RentalTypeEnum::SHORT,
                'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object)[
                'id' => RentalTypeEnum::LONG,
                'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            // (object) [
            //     'id' => RentalTypeEnum::REPLACEMENT,
            //     'name' => __('inspection_cars.rental_type_' . RentalTypeEnum::REPLACEMENT),
            //     'value' => RentalTypeEnum::REPLACEMENT,
            // ],
            // (object) [
            //     'id' => RentalTypeEnum::TRANSPORT,
            //     'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
            //     'value' => RentalTypeEnum::TRANSPORT,
            // ],
            // (object) [
            //     'id' => RentalTypeEnum::OTHER,
            //     'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
            //     'value' => RentalTypeEnum::OTHER,
            // ],
        ]);
        return $rental_type;
    }

    private function getWorkStatusList()
    {
        return collect([
            (object)[
                'id' => DrivingJobStatusEnum::COMPLETE,
                'value' => DrivingJobStatusEnum::COMPLETE,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::COMPLETE . '_text'),
            ],
            (object)[
                'id' => DrivingJobStatusEnum::CANCEL,
                'value' => DrivingJobStatusEnum::CANCEL,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::CANCEL . '_text'),
            ],
        ]);
    }

    private function getIsConfirmWagesList()
    {
        return collect([
            (object)[
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('driving_jobs.is_confirm_wage_' . BOOL_FALSE . '_text'),
            ],
            (object)[
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('driving_jobs.is_confirm_wage_' . BOOL_TRUE . '_text'),
            ],
        ]);
    }

    function getDefaultCarByLicensePlate(Request $request)
    {
        $car_id = $request->car_id;
        $job_id = $request->job_id;
        $job_type = $request->job_type;
        $data_import = [];

        $data = DB::table('cars')
            ->select(
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'cars.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'cars.oil_type',
                'cars.rental_type',
            )
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'cars.gear_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'cars.car_tire_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            // ->leftJoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->where('cars.id', $car_id)
            ->get()
            ->toArray();

        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();

        if (strcmp($job_type, ImportCarLine::class) == 0) {
            // $import_car = ImportCar::find($job_id);
            $import_car_line = ImportCarLine::find($car_id);
            if ($import_car_line) {
                $data_import['delivery_date'] = ($import_car_line->delivery_date) ? $import_car_line->delivery_date : null;
                $data_import['import_delivery_place'] = ($import_car_line->delivery_location) ? $import_car_line->delivery_location : null;
                $data_import['job_type'] = ImportCarLine::class;
            }
        }
        return [
            'success' => true,
            'car_id' => $request->car_id,
            'data' => $data,
            'data_import' => $data_import,
            'zone' => $zone,
        ];
    }

    function getDefaultServiceTypeRental(Request $request)
    {
        $job_id = $request->job_id;
        $job_type = $request->job_type;
        $service_type_rental = null;
        $service_type = null;
        $data = [];
        $data['parent_worksheet_no'] = null;
        $data['dealer'] = null;
        if (strcmp($job_type, Rental::class) == 0) {
            $origin_remark = null;
            $destination_remark = null;
            $rental = Rental::find($job_id);
            $service_type = $rental->serviceType->service_type;
            $service_type_rental = __('driving_jobs.service_type_' . $service_type);
            if ($rental->origin_remark) {
                $origin_remark = "($rental->origin_remark)";
            }
            if ($rental->destination_remark) {
                $destination_remark = "($rental->destination_remark)";
            }
            $data['parent_worksheet_no'] = $rental->worksheet_no;
            $data['service_type'] = ($rental->serviceType) ? __('driving_jobs.service_type_' . $rental->serviceType->service_type) : null;
            $data['parent_customer'] = $rental->customer_name . '(' . $rental->customer_tel . ')';
            $data['rental_start_date'] = $rental->pickup_date;
            $data['rental_end_date'] = $rental->return_date;
            $data['rental_origin'] = ($rental->origin) ? $rental->origin->name . $origin_remark : $rental->origin_name . $origin_remark;
            $data['rental_destination'] = ($rental->destination) ? $rental->destination->name . $destination_remark : $rental->destination_name . $destination_remark;
        } else {
            if (strcmp($job_type, LongTermRental::class) == 0) {
                $long_rental = LongTermRental::find($job_id);
                $data['parent_worksheet_no'] = ($long_rental->worksheet_no) ? $long_rental->worksheet_no : null;
                $data['parent_customer'] = $long_rental->customer_name . '(' . $long_rental->customer_tel . ')';
                $data['contract_start_date'] = $long_rental->contract_start_date;
                $data['contract_end_date'] = $long_rental->contract_end_date;
            } else {
                if (strcmp($job_type, ImportCarLine::class) == 0) {
                    $import_car_line = ImportCarLine::find($job_id);
                    if ($import_car_line) {
                        $import_car = ImportCar::find($import_car_line->import_car_id);
                        if ($import_car) {
                            $data['parent_worksheet_no'] = ($import_car->purchaseOrder) ? $import_car->purchaseOrder->po_no : null;
                            $data['dealer'] = ($import_car->purchaseOrder && $import_car->purchaseOrder->creditor) ? $import_car->purchaseOrder->creditor->name : null;
                        }
                    }
                } else {
                    if (strcmp($job_type, InstallEquipment::class) == 0) {
                        $install_equipment = InstallEquipment::find($job_id);
                        if ($install_equipment) {
                            $data['parent_worksheet_no'] = $install_equipment->worksheet_no ?? null;
                            $data['supplier'] = ($install_equipment->supplier) ? $install_equipment->supplier->name : null;
                            $data['destination'] = ($install_equipment->supplier) ? $install_equipment->supplier->address : null;
                        }
                    }
                }
            }
        }

        return [
            'success' => true,
            'job_id' => $request->job_id,
            'service_type_rental' => $service_type_rental,
            'service_type' => $service_type,
            'data' => $data,
            'job_type' => $job_type,
        ];
    }

    public function showCalendar(Request $request)
    {
        $driver_id = $request->driver_id;
        $driver_name = [];
        $driver = Driver::find($request->driver_id);
        if ($driver) {
            $driver_name = $driver->name;
        }
        $driving_arr = [];
        $driving_jobs = DrivingJob::select(
            'driving_jobs.id',
            'drivers.name as title',
            'driving_jobs.start_date as start',
            'driving_jobs.end_date as end',
            'driving_jobs.status',
            'driving_jobs.self_drive_type'
        )
            ->leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->when($driver_id, function ($query) use ($driver_id) {
                $query->where('driving_jobs.driver_id', $driver_id);
            })
            ->whereNotNull('driving_jobs.driver_id')
            ->whereNotIn('driving_jobs.status', [DrivingJobStatusEnum::INITIAL])
            ->get();
        foreach ($driving_jobs as $driving_job) {
            $driving = [];
            $driving['id'] = $driving_job->id;
            if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
                $driving['title'] = $driving_job->title . " (ส่งรถ)";
            } else {
                if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::PICKUP) === 0) {
                    $driving['title'] = $driving_job->title . " (รับรถ)";
                } else {
                    if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::OTHER) === 0) {
                        $driving['title'] = $driving_job->title . " (พร้อมคนขับ)";
                    } else {
                        if (strcmp($driving_job->self_drive_type, SelfDriveTypeEnum::SELF_DRIVE) === 0) {
                            $driving['title'] = $driving_job->title . " (ยืมรถ)";
                        }
                    }
                }
            }

            $driving['start'] = $driving_job->start;
            $driving['end'] = $driving_job->end;
            $driving['status'] = $driving_job->status;
            $driving['job_type'] = $driving_job->job_type;
            $driving['job_id'] = $driving_job->job_id;
            $driving_arr[] = $driving;
        }
        return view('admin.driving-jobs.calendar', ['rental' => $driving_arr, 'driver_id' => $driver_id, 'driver_name' => $driver_name]);
    }

    public function getCalendar(Request $request)
    {
        $driving = DrivingJob::find($request->id);
        $driving_detail = DrivingJob::leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->leftjoin('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->where('driving_jobs.id', $driving->id)
            ->when($driving->job_type == Rental::class, function ($query) {
                $query->leftjoin('rentals', 'rentals.id', '=', 'driving_jobs.job_id');
                $query->leftjoin('locations as origin', 'origin.id', '=', 'rentals.origin_id');
                $query->leftjoin('locations as destination', 'destination.id', '=', 'rentals.destination_id');
                $query->select(
                    'driving_jobs.worksheet_no',
                    'drivers.name as driver_name',
                    'driving_jobs.parent_id',
                    'driving_jobs.self_drive_type',
                    'driving_jobs.job_type',
                    'cars.license_plate',
                    // 'driving_jobs.start_date',
                    // 'driving_jobs.end_date',
                    'driving_jobs.origin as origin_name',
                    'driving_jobs.destination as destination_name',
                    'rentals.customer_name',
                    'rentals.customer_tel',
                    'rentals.worksheet_no as ref_no',
                    DB::raw('DATE_FORMAT(driving_jobs.start_date, "%d/%m/%Y %H:%i") as start_date'),
                    DB::raw('DATE_FORMAT(driving_jobs.end_date, "%d/%m/%Y %H:%i") as end_date')
                );
            })
            ->when($driving->job_type == LongTermRental::class, function ($query) {
                $query->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'driving_jobs.job_id');
                $query->select(
                    'driving_jobs.worksheet_no',
                    'drivers.name as driver_name',
                    'driving_jobs.parent_id',
                    'driving_jobs.self_drive_type',
                    'driving_jobs.job_type',
                    'cars.license_plate',
                    // 'driving_jobs.start_date',
                    // 'driving_jobs.end_date',
                    // 'origin.name as origin_name',
                    // 'destination.name as destination_name',
                    'driving_jobs.origin as origin_name',
                    'driving_jobs.destination as destination_name',
                    'lt_rentals.customer_name',
                    'lt_rentals.customer_tel',
                    'lt_rentals.worksheet_no as ref_no',
                    DB::raw('DATE_FORMAT(driving_jobs.start_date, "%d/%m/%Y %H:%i") as start_date'),
                    DB::raw('DATE_FORMAT(driving_jobs.end_date, "%d/%m/%Y %H:%i") as end_date')
                );
            })
            ->when($driving->job_type == ImportCarLine::class, function ($query) {
                $query->leftjoin('import_cars', 'import_cars.id', '=', 'driving_jobs.job_id');
                $query->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'import_cars.po_id');
                $query->leftjoin('creditors', 'creditors.id', '=', 'purchase_orders.creditor_id');
                $query->select(
                    'driving_jobs.worksheet_no',
                    'drivers.name as driver_name',
                    'driving_jobs.parent_id',
                    'driving_jobs.self_drive_type',
                    'driving_jobs.job_type',
                    'cars.chassis_no as license_plate',
                    'creditors.name as customer_name',
                    'creditors.tel as customer_tel',
                    'purchase_orders.po_no as ref_no',
                    'driving_jobs.origin as origin_name',
                    'driving_jobs.destination as destination_name',
                    DB::raw('DATE_FORMAT(driving_jobs.start_date, "%d/%m/%Y %H:%i") as start_date'),
                    DB::raw('DATE_FORMAT(driving_jobs.end_date, "%d/%m/%Y %H:%i") as end_date')
                );
            })
            ->when($driving->job_type == DrivingJobTypeStatusEnum::OTHER, function ($query) {
                $query->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'driving_jobs.job_id');
                $query->select(
                    'driving_jobs.worksheet_no',
                    'drivers.name as driver_name',
                    'driving_jobs.parent_id',
                    'driving_jobs.self_drive_type',
                    'driving_jobs.job_type',
                    'cars.license_plate',
                    'driving_jobs.origin as origin_name',
                    'driving_jobs.destination as destination_name',
                    DB::raw('DATE_FORMAT(driving_jobs.start_date, "%d/%m/%Y %H:%i") as start_date'),
                    DB::raw('DATE_FORMAT(driving_jobs.end_date, "%d/%m/%Y %H:%i") as end_date')
                );
            })
            ->first();

        if (strcmp($driving_detail->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
            $driving_detail->self_drive_type = __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::SEND);
        } else {
            if (strcmp($driving_detail->self_drive_type, SelfDriveTypeEnum::PICKUP) === 0) {
                $driving_detail->self_drive_type = __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::PICKUP);
            } else {
                if (strcmp($driving_detail->self_drive_type, SelfDriveTypeEnum::OTHER) === 0) {
                    $driving_detail->self_drive_type = __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::OTHER);
                } else {
                    if (strcmp($driving_detail->self_drive_type, SelfDriveTypeEnum::SELF_DRIVE) === 0) {
                        $driving_detail->self_drive_type = __('driving_jobs.self_drive_type_' . SelfDriveTypeEnum::SELF_DRIVE);
                    }
                }
            }
        }

        if (strcmp($driving_detail->job_type, Rental::class) === 0) {
            $driving_detail->job_type_th = __('driving_jobs.job_type_' . Rental::class);
            $driving_detail->label_name_customer = __('driving_jobs.customer');
            $driving_detail->label_name_license_plate = __('driving_jobs.license_plate');
        } else {
            if (strcmp($driving_detail->job_type, LongTermRental::class) === 0) {
                $driving_detail->job_type_th = __('driving_jobs.job_type_' . LongTermRental::class);
                $driving_detail->label_name_customer = __('driving_jobs.customer');
                $driving_detail->label_name_license_plate = __('driving_jobs.license_plate');
            } else {
                if (strcmp($driving_detail->job_type, ImportCarLine::class) === 0) {
                    $driving_detail->job_type_th = __('driving_jobs.job_type_' . ImportCarLine::class);
                    $driving_detail->label_name_customer = __('driving_jobs.dealer');
                    $driving_detail->label_name_license_plate = __('driving_jobs.license_plate_chassis_no');
                } else { // other job_type
                    $driving_detail->job_type_th = __('driving_jobs.job_type_' . DrivingJobTypeStatusEnum::OTHER);
                    $driving_detail->label_name_license_plate = __('driving_jobs.license_plate');
                    $driving_detail->label_name_customer = __('driving_jobs.customer');
                }
            }
        }

        return response()->json($driving_detail);
    }
}
