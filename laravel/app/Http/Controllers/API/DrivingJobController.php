<?php

namespace App\Http\Controllers\API;

use App\Classes\Sap\SapProcess;
use App\Enums\DrivingJobStatusEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CarPark;
use App\Models\InspectionJob;
use App\Models\InspectionJobStep;
use App\Models\LongTermRental;
use App\Models\Rental;
use App\Models\ServiceType;
use App\Models\TransferCar;
use App\Traits\RentalTrait;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DrivingJob;
use App\Models\DrivingJobCheckin;
use App\Models\Driver;
use App\Models\ImportCarLine;
use Illuminate\Support\Facades\DB;

class DrivingJobController extends Controller
{
    use RentalTrait;

    const RENTAL_MODEL = 'App\\\\Models\\\\Rental';
    const LONG_TERM_RENTAL_MODEL = 'App\\\\Models\\\\LongTermRental';
    const IMPORT_CAR_LINE_MODEL = 'App\\\\Models\\\\ImportCarLine';
    const TRANSFER_CAR_MODEL = 'App\\\\Models\\\\TransferCar';
    const OTHER = 'OTHER';

    public function index(Request $request)
    {
        $s = $request->s;

        $query = $this->getMainQuery($request);
        $list = $query->search($request->s, $request)
            ->orderBy('driving_jobs2.created_at')
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $query = $this->getMainQuery($request);
        $data = $query->where('driving_jobs2.id', $request->id)->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $data->status_name = ((!empty($data->status)) ? __('driving_jobs.status_' . $data->status . '_text') : null);
        $data->rental_status_name = ((!empty($data->rental_status)) ? __('short_term_rentals.status_' . $data->rental_status) : null);

        $data->car = ((!empty($data->car_id)) ? get_car_detail2($data->car_id) : null);
        if ($data->driver_id) {
            $driver = Driver::find($data->driver_id);
            if ($driver) {
                $driver->profile_image_url = $driver->profile_url;
                $data->driver = $driver;
            }
        }
        $data->rental = ((!empty($data->job_id)) ? get_rental_detail($data->job_id) : null);

        /* $inspection_job = InspectionJob::where('item_id', $data->job_id)
        ->select(
        'id as inspection_job_id',
        'inspection_type',
        'transfer_type',
        'inspection_status',
        )->get()->map(function ($item) {
        $job_step_ids = InspectionJobStep::where('inspection_job_id', $item->inspection_job_id)
        ->pluck('id')->toArray();
        $item->inspection_job_step_ids = $job_step_ids;
        return $item;
        })->toArray();
        $data->inspection_jobs = $inspection_job; */

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:driving_jobs,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'est_distance' => ['nullable', 'numeric'],
            'estimate_prepare_date' => ['required', 'date'],
            'estimate_start_date' => ['required', 'date'],
            'estimate_rented_date' => ['date', 'nullable'],
            'estimate_end_job_date' => ['required', 'date'],
            'estimate_arrive_date' => ['required', 'date'],
            'estimate_end_date' => ['required', 'date'],
            'atk_check' => ['boolean', 'nullable'],
            'alcohol_check' => ['boolean', 'nullable'],
            'alcohol' => ['boolean', 'nullable'],
        ], [], [
            'id' => __('driving_jobs.id'),
            'driver_id' => __('driving_jobs.driver_id'),
            'start_date' => __('driving_jobs.start_date'),
            'end_date' => __('driving_jobs.end_date'),
            'est_distance' => __('driving_jobs.est_distance'),
            'estimate_prepare_date' => __('driving_jobs.estimate_prepare_date'),
            'estimate_start_date' => __('driving_jobs.estimate_start_date'),
            'estimate_rented_date' => __('driving_jobs.estimate_rented_date'),
            'estimate_end_job_date' => __('driving_jobs.estimate_end_job_date'),
            'estimate_arrive_date' => __('driving_jobs.estimate_end_job_date'),
            'estimate_end_date' => __('driving_jobs.estimate_end_date'),
            'atk_check' => __('driving_jobs.atk_check'),
            'alcohol_check' => __('driving_jobs.alcohol_check'),
            'alcohol' => __('driving_jobs.alcohol'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $driving_job = DrivingJob::find($request->id);
        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $driving_job->driver_id = $request->driver_id;
        $driving_job->start_date = !empty($request->start_date) ? date('Y-m-d H:i:s', strtotime($request->start_date)) : null;
        $driving_job->end_date = !empty($request->end_date) ? date('Y-m-d H:i:s', strtotime($request->end_date)) : null;
        $driving_job->est_distance = floatval($request->est_distance);

        $driving_job->estimate_prepare_date = !empty($request->estimate_prepare_date) ? date('Y-m-d H:i:s', strtotime($request->estimate_prepare_date)) : null;
        $driving_job->estimate_start_date = !empty($request->estimate_start_date) ? date('Y-m-d H:i:s', strtotime($request->estimate_start_date)) : null;
        if (strcmp($driving_job->job_type, Rental::class) === 0) {
            $rental = Rental::find($driving_job->job_id);
            if (strcmp($rental->serviceType->service_type, ServiceTypeEnum::SELF_DRIVE) != 0) {
                if (empty($request->estimate_rented_date)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณากรอก ' . __('driving_jobs.estimate_rented_date'),
                    ], 422);
                }
                if (strtotime($request->estimate_rented_date) === false) {
                    return response()->json([
                        'success' => false,
                        'message' => __('driving_jobs.estimate_rented_date') . ' ต้องเป็นวันที่เท่านั้น',
                    ], 422);
                }
                $driving_job->estimate_rented_date = date('Y-m-d H:i:s', strtotime($request->estimate_rented_date));
            }
        }
        $driving_job->estimate_end_job_date = !empty($request->estimate_end_job_date) ? date('Y-m-d H:i:s', strtotime($request->estimate_end_job_date)) : null;
        $driving_job->estimate_arrive_date = !empty($request->estimate_arrive_date) ? date('Y-m-d H:i:s', strtotime($request->estimate_arrive_date)) : null;
        $driving_job->estimate_end_date = !empty($request->estimate_end_date) ? date('Y-m-d H:i:s', strtotime($request->estimate_end_date)) : null;
        $driving_job->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
    }

    function startJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:driving_jobs,id'],
            'actual_start_date' => ['required', 'date'],
        ], [], [
            'id' => __('driving_jobs.id'),
            'actual_start_date' => __('driving_jobs.actual_start_date'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $driving_job = DrivingJob::find($request->id);

        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        if ($driving_job->job_type == Rental::class) {
            $rental = Rental::find($driving_job->job_id);
            $driving_job->actual_start_date = date('Y-m-d H:i:s', strtotime($request->actual_start_date));
            if ($driving_job->self_drive_type == SelfDriveTypeEnum::SEND) {
                // update status to waiting receive car
                $rental->status = RentalStatusEnum::AWAIT_RECEIVE;
            } elseif ($driving_job->self_drive_type == SelfDriveTypeEnum::PICKUP) {
                //
            } else {
                $rental->status = RentalStatusEnum::AWAIT_RECEIVE;
            }
            $rental->save();
        } else { // is not short term rental
            $driving_job->actual_start_date = date('Y-m-d H:i:s', strtotime($request->actual_start_date));
        }
        $driving_job->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
    }

    function endJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:driving_jobs,id'],
            'actual_end_job_date' => ['required', 'date'],
            // 'arrived_office' => ['required', Rule::in(['1', '0'])],
        ], [], [
            'id' => __('driving_jobs.id'),
            'actual_end_job_date' => __('driving_jobs.actual_end_job_date'),
            // 'arrived_office' => __('driving_jobs.arrived_office'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $driving_job = DrivingJob::find($request->id);
        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        // $driving_job->arrived_office = boolval($request->arrived_office);

        if ($driving_job->job_type == Rental::class) {
            $driving_job->actual_end_job_date = date('Y-m-d H:i:s', strtotime($request->actual_end_job_date));
            $rental = Rental::find($driving_job->job_id);
            if ($driving_job->self_drive_type == SelfDriveTypeEnum::SEND) {
                // update status to currently renting
                $rental->status = RentalStatusEnum::ACTIVE;
            } elseif ($driving_job->self_drive_type == SelfDriveTypeEnum::PICKUP) {
                $rental->status = RentalStatusEnum::AWAIT_RETURN;
            }
            $rental->save();
        } else { // is not short term rental
            $driving_job->actual_end_job_date = date('Y-m-d H:i:s', strtotime($request->actual_end_job_date));
        }

        $driving_job->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
    }

    function arrived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:driving_jobs,id'],
            'actual_arrive_date' => ['required', 'date'],
            // 'arrived_office' => ['required', Rule::in(['1', '0'])],
        ], [], [
            'id' => __('driving_jobs.id'),
            'actual_arrive_date' => __('driving_jobs.actual_arrive_date'),
            // 'arrived_office' => __('driving_jobs.arrived_office'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $driving_job = DrivingJob::find($request->id);
        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        if ($driving_job->job_type == Rental::class) {
            $rental = Rental::find($driving_job->job_id);
            // if ($driving_job->self_drive_type == SelfDriveTypeEnum::OTHER) {
            $driving_job->actual_arrive_date = date('Y-m-d H:i:s', strtotime($request->actual_arrive_date));
            // } 
            $rental->save();
        } else { // is not short term rental
            $driving_job->actual_arrive_date = date('Y-m-d H:i:s', strtotime($request->actual_arrive_date));
        }


        $driving_job->arrived_office = true;
        $driving_job->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
    }

    function checkin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:driving_jobs,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['required', 'string', 'max:200'],
            'lat' => ['nullable', 'string', 'max:200'],
            'lng' => ['nullable', 'string', 'max:200'],
            'arrived_at' => ['nullable', 'date'],
            'departured_at' => ['nullable', 'date'],
        ], [], [
            'id' => __('driving_jobs.id'),
            'location_id' => __('driving_jobs.location_id'),
            'location_name' => __('driving_jobs.location_name'),
            'lat' => __('driving_jobs.lat'),
            'lng' => __('driving_jobs.lng'),
            'arrived_at' => __('driving_jobs.arrived_at'),
            'departured_at' => __('driving_jobs.departured_at'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $d = new DrivingJobCheckin();
        $d->driving_job_id = $request->id;
        $d->location_id = $request->location_id;
        $d->location_name = $request->location_name;
        $d->lat = $request->lat;
        $d->lng = $request->lng;
        $d->arrived_at = (!empty($request->arrived_at) ? date('Y-m-d H:i:s', strtotime($request->arrived_at)) : null);
        $d->departured_at = (!empty($request->departured_at) ? date('Y-m-d H:i:s', strtotime($request->departured_at)) : null);
        $d->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $d->id, 200);
    }

    public function rentedJob(Request $request)
    {
        $driving_job = DrivingJob::find($request->id);
        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $actual_rented_date = !empty($request->actual_rented_date) ? strtotime($request->actual_rented_date) : strtotime("now");
        $driving_job->actual_rented_date = date('Y-m-d H:i:s', $actual_rented_date);

        if ($driving_job->job_type == Rental::class) {
            $driving_job->actual_end_date = date('Y-m-d H:i:s', strtotime($request->actual_end_date));
            $rental = Rental::find($driving_job->job_id);
            if ($driving_job->self_drive_type == SelfDriveTypeEnum::OTHER) {
                // update status to currently renting
                $rental->status = RentalStatusEnum::ACTIVE;
            }
            $rental->save();
        } else { // is not short term rental
            $driving_job->actual_end_date = date('Y-m-d H:i:s', strtotime($request->actual_end_date));
        }

        $driving_job->save();

        if (strcmp($driving_job->job_type, Rental::class) === 0) {
            $updated_rental = $this->updateSAPRentalDeliverySuccess($driving_job->job_id, $driving_job->id);
            if ($updated_rental) {
                return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
            } else {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
            }
        }
    }

    public function updateSAPRentalDeliverySuccess($rental_id, $driving_job_id)
    {
        $result = false;
        $rental = Rental::find($rental_id);
        $rental_bill_primary = RentalTrait::getRentalBillPrimaryByRentalId($rental_id);
        if (!$rental || !$rental_bill_primary) {
            return $result;
        }
        $rental->status = RentalStatusEnum::ACTIVE;
        $rental->save();

        $sap = new SapProcess();
        $sap->startService($rental_id, $driving_job_id);
        // $sap_coupons = new SapProcess();
        // $sap_coupons->startServiceCouponsCommerce($rental_bill_primary->id);
        return true;
    }

    public function getStatus($id)
    {
        if (empty($id)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $driving_job = DrivingJob::find($id);
        if (empty($driving_job)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        return $this->responseWithCode(true, DATA_SUCCESS, ['status' => $driving_job->status], 200);
    }

    public function getRentalStatus($id)
    {
        $rental = $this->getRentalByDrivingJobId($id);
        return $this->responseWithCode(true, DATA_SUCCESS, ['status_name' => $rental->status], 200);
    }

    public function getRentalCars($id)
    {
        $rental = $this->getRentalByDrivingJobId($id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $data = [];
        $data['service_type_id'] = $rental->service_type_id;
        $data['service_type_name'] = $rental->serviceType?->name;
        $data['product_id'] = $rental->product_id;
        $data['product_name'] = $rental->product?->name;
        $data['pickup_date'] = $rental->pickup_date;
        $data['return_date'] = $rental->return_date;
        $rental_line_car_list = RentalTrait::getRentalLineCarList($rental->id, false, false);
        $cars = [];
        foreach ($rental_line_car_list as $key => $rental_line_car) {
            $cars[]['car_class_name'] = $rental_line_car->class_full_name;
        }
        $data['cars'] = $cars;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function getRentalCarClasses($id)
    {
        $rental = $this->getRentalByDrivingJobId($id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $data = [];
        $rental_line_car_list = RentalTrait::getRentalLineCarList($rental->id, false, false);
        $cars = [];
        foreach ($rental_line_car_list as $key => $rental_line_car) {
            $cars[]['car_class_name'] = $rental_line_car->class_full_name;
        }
        $data['cars'] = $cars;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function getRentalOrigin($id)
    {
        $rental = $this->getRentalByDrivingJobId($id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $data = [];
        $data['orgin_id'] = $rental->orgin_id;
        $data['orgin_name'] = $rental->origin_name;
        $data['orgin_address'] = $rental->orgin_address;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function getRentalByDrivingJobId($id)
    {
        if (empty($id)) {
            return null;
        }

        $driving_job = DrivingJob::find($id);
        if (empty($driving_job) || (strcmp($driving_job->job_type, Rental::class) !== 0) || !$driving_job->job) {
            return null;
        }

        $rental = Rental::find($driving_job->job_id);
        if (empty($rental)) {
            return null;
        }
        return $rental;
    }

    public function getZone($id)
    {
        $driving_job = DrivingJob::find($id);
        if (empty($driving_job)) {
            return null;
        }

        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->leftjoin('branches', 'branches.id', 'car_park_zones.branch_id')
            ->where('car_parks.car_id', $driving_job->car_id)
            ->select(
                'car_park_zones.code',
                'car_parks.car_park_number',
                'car_park_zones.branch_id',
                'branches.name as branch_name',
            )
            ->first();
        $data = [
            'car_park_zone_name' => $zone?->code,
            'car_park_number' => $zone?->car_park_number,
            'branch_id' => $zone?->branch_id,
            'branch_name' => $zone?->branch_name,
        ];
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    function qr(Request $request)
    {
        $driving_job = DrivingJob::select(
            'driving_jobs.id',
            'driving_jobs.worksheet_no',
            'driving_jobs.driver_id',
            /* 'driving_jobs.driver_name',
            'driving_jobs.job_type',
            'driving_jobs.job_id',
            'driving_jobs.car_id',
            'driving_jobs.created_at',
            'driving_jobs.updated_at', */
            'cars.license_plate'
        )->join('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->where('driving_jobs.id', $request->id)
            ->first();
        $data = $driving_job ? $driving_job->getAttributes() : null;
        $data_str = json_encode($data);
        $qrcode = base64_encode(QrCode::encoding('UTF-8')->format('png')->size(160)->generate($data_str));
        return response()->json([
            'data' => $data,
            'qrcode' => $data ? $qrcode : null
        ]);
    }

    public function getMainQuery($request)
    {
        $job_type = $request->job_type;
        $self_drive_type = $request->self_drive_type;
        $is_confirm_wage = is_null($request->is_confirm_wage) ? null : intval($request->is_confirm_wage);
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $rental_subquery = Rental::select(
            'rentals.id',
            'rentals.worksheet_no as ref_worksheet_no',
            'service_types.id as service_type_id',
            'service_types.name as service_type_name',
            'rentals.pickup_date as ref_start_date',
            'rentals.return_date as ref_end_date',
            'rentals.status as rental_status'
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
            'driving_jobs.driver_id',
            'driving_jobs.driver_name',
            'driving_jobs.job_type',
            'driving_jobs.job_id',
            'rentals.service_type_id',
            'rentals.service_type_name',
            'rentals.rental_status',
            'driving_jobs.is_confirm_wage',
            'driving_jobs.status',
            'driving_jobs.self_drive_type',
            'driving_jobs.branch_id',
            'driving_jobs.car_id',
            'driving_jobs.created_at',
            'driving_jobs.updated_at',
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
            ->leftjoin('transfer_cars', 'transfer_cars.id', '=', 'driving_jobs.job_id');
        //->where('driving_jobs.branch_id', get_branch_id());

        $query = DrivingJob::select('driving_jobs2.*')->joinSub($main_query, 'driving_jobs2', function ($join) {
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
            });

        return $query;
    }

    public function getJobList(Request $request)
    {
        $service_type = ServiceType::select('service_types.id', 'service_types.name', 'service_types.service_type')
            ->get();
        $result = collect([]);
        foreach ($service_type as $key => $item) {
            $obj = (object) [
                'name' => $item->name,
                'service_type_id' => $item->id,
                'service_type_enum' => $item->service_type,
                'job_type' => null
            ];
            $result[] = $obj;
        }

        $job_arr = [Rental::class, LongTermRental::class, ImportCarLine::class, TransferCar::class, self::OTHER];
        foreach ($job_arr as $job) {
            $result[] = (object) [
                'name' => __('driving_jobs.job_type_' . $job),
                'service_type_id' => null,
                'service_type_enum' => null,
                'job_type' => $job
            ];
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $result, 200);
    }

    public function getStatusList(Request $request)
    {
        $statuses = collect([
            (object) [
                'id' => DrivingJobStatusEnum::INITIAL,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::INITIAL . '_text'),
            ],
            (object) [
                'id' => DrivingJobStatusEnum::PENDING,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::PENDING . '_text'),
            ],
            (object) [
                'id' => DrivingJobStatusEnum::IN_PROCESS,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::IN_PROCESS . '_text'),
            ],
            (object) [
                'id' => DrivingJobStatusEnum::COMPLETE,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::COMPLETE . '_text'),
            ],
            (object) [
                'id' => DrivingJobStatusEnum::CANCEL,
                'name' => __('driving_jobs.status_' . DrivingJobStatusEnum::CANCEL . '_text'),
            ],
        ]);
        return $this->responseWithCode(true, DATA_SUCCESS, $statuses, 200);
    }
}
