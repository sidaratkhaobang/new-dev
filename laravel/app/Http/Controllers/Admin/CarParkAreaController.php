<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarParkStatusEnum;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarPark;
use App\Models\CarParkTransferLog;
use App\Models\CarParkZone;
use App\Models\User;
use App\Traits\CarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarParkAreaController extends Controller
{
    use CarTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ParkingZone);
        $s = $request->s;
        $area_id = $request->area_id;
        $license_plate = $request->license_plate;
        $engine_no = $request->engine_no;
        $chassis_no = $request->chassis_no;
        $slot_number = $request->slot_number;
        $est_transfer_date = $request->est_transfer_date;
        $transfer_date = $request->transfer_date;
        $status = $request->status;


        $zone_detail = CarParkZone::leftjoin('car_park_areas', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->where('car_park_areas.id', $area_id)
            ->branch()
            ->select(
                'car_park_zones.id',
                'car_park_zones.code',
                'car_park_zones.name',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
            )
            ->first();
        if (empty($area_id) || empty($zone_detail)) {
            return redirect()->route('admin.parking-lots.index');
        }

        $list = CarPark::leftjoin('cars', 'cars.id', '=', 'car_parks.car_id')
            ->leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->leftjoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('car_park_zones.branch_id', get_branch_id())
            ->where(function ($q)
            use (
                $s,
                $status,
                $license_plate,
                $engine_no,
                $chassis_no,
                $slot_number
            ) {
                if (!is_null($s)) {
                    $q->where('car_park_zones.name', 'like', '%' . $s . '%');
                    $q->orWhere('car_park_zones.code', 'like', '%' . $s . '%');
                    $q->orWhere('car_parks.car_park_number', 'like', '%' . $s . '%');
                    $q->orWhere('cars.license_plate', 'like', '%' . $s . '%');
                    $q->orWhere('cars.engine_no', 'like', '%' . $s . '%');
                    $q->orWhere('cars.chassis_no', 'like', '%' . $s . '%');
                }
                if (!is_null($status)) {
                    $q->where('car_parks.status', $status);
                }
                if (!is_null($license_plate)) {
                    $q->where('cars.id', $license_plate);
                }
                if (!is_null($engine_no)) {
                    $q->where('cars.id', $engine_no);
                }
                if (!is_null($chassis_no)) {
                    $q->where('cars.id', $chassis_no);
                }
                if (!is_null($slot_number)) {
                    $q->where('car_parks.car_park_number', $slot_number);
                }
                // if (!is_null($est_transfer_date)) {
                //     $q->where('car_park_transfers.est_transfer_date', $est_transfer_date);
                // }
                // if (!is_null($transfer_date)) {
                //     $q->where('car_park_transfer_logs.transfer_date', $transfer_date);
                // }
            })
            ->where('car_park_areas.id', $area_id)
            ->select(
                DB::raw("CONCAT(car_park_zones.code, car_parks.car_park_number) AS car_park_name_code"),
                'car_park_zones.code as zone_code',
                'car_parks.id as car_park_id',
                'cars.id as car_id',
                'car_parks.car_park_number as car_park_number',
                'car_classes.name as class_name',
                'car_groups.name as car_group_name',
                'car_types.name as type_name',
                'car_park_areas.area_size as area_size',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_parks.status as status',
                'cars.status as car_status',
                // 'car_park_transfers.start_date as start_date',
                // 'car_park_transfers.end_date as end_date',
                // 'car_park_transfer_logs.transfer_date as transfer_date',
                // 'car_park_transfers.est_transfer_date as est_transfer_date',
            )
            ->orderBy('car_parks.car_park_number')
            ->paginate(PER_PAGE);
        $license_plate_list = CarTrait::getLicensePlateList();
        $chassis_no_list = CarTrait::getChassisList();
        $engine_no_list = CarTrait::getEngineList();
        $status_list = $this->getStatusList();
        $slot_number_list = $this->getSlotNumberList($zone_detail->start_number, $zone_detail->end_number);
        return view('admin.car-park-areas.index', [
            'list' => $list,
            'area_id' => $area_id,
            'zone_detail' => $zone_detail,
            's' => $request->s,
            'license_plate' => $license_plate,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'slot_number' => $slot_number,
            'status' => $status,
            'transfer_date' => $transfer_date,
            'est_transfer_date' => $est_transfer_date,
            'license_plate_list' => $license_plate_list,
            'chassis_no_list' => $chassis_no_list,
            'engine_no_list' => $engine_no_list,
            'slot_number_list' => $slot_number_list,
            'status_list' => $status_list
        ]);
    }

    public function getSlotNumberList($start_number, $end_number)
    {
        $arr_result = [];
        for ($i = $start_number; $i < $end_number; $i++) {
            $arr = (object)[
                'id' => $i,
                'value' => $i,
                'name' => $i,
            ];
            array_push($arr_result, $arr);
        }
        return collect($arr_result);
    }

    public function getStatusList()
    {
        $status = collect([
            (object) [
                'id' => CarParkStatusEnum::FREE,
                'name' => __('parking_lots.slot_status_' . CarParkStatusEnum::FREE),
                'value' => CarParkStatusEnum::FREE,
            ],
            (object) [
                'id' => CarParkStatusEnum::USED,
                'name' => __('parking_lots.slot_status_' . CarParkStatusEnum::USED),
                'value' => CarParkStatusEnum::USED,
            ],
            (object) [
                'id' => CarParkStatusEnum::BOOKING,
                'name' => __('parking_lots.slot_status_' . CarParkStatusEnum::BOOKING),
                'value' => CarParkStatusEnum::BOOKING,
            ],
            (object) [
                'id' => CarParkStatusEnum::DISABLED,
                'name' => __('parking_lots.slot_status_' . CarParkStatusEnum::DISABLED),
                'value' => CarParkStatusEnum::DISABLED,
            ],
        ]);
        return $status;
    }

    public function updateCarParkDisableDate(Request $request)
    {
        $today = date('Y-m-d');
        $rules = [
            'start_disabled_date' => 'required|date_format:Y-m-d|after_or_equal:' . $today,
        ];

        if (!empty($request->end_disabled_date)) {
            $rules['end_disabled_date'] = 'date_format:Y-m-d|after_or_equal:start_disabled_date';
        }
        $validator = Validator::make($request->all(), $rules, [], [
            'start_disabled_date' => __('parking_lots.start_disabled_date'),
            'end_disabled_date' => __('parking_lots.end_disabled_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $car_park = CarPark::find($request->car_park_id);
        $car_park->start_disabled_date = $request->start_disabled_date;
        $car_park->end_disabled_date = $request->end_disabled_date;
        $car_park->is_permanent_disabled = boolval($request->is_permanent_disabled);

        if ($request->start_disabled_date == $today) {
            $car_park->status = CarParkStatusEnum::DISABLED;
        }
        $car_park->save();

        return response()->json([
            'success' => true,
            'message' => __('lang.store_success_message'),
        ]);
    }

    public function updateCarParkStatus(Request $request)
    {
        if ($request->car_park_id && $request->car_park_status) {
            $car_park = CarPark::find($request->car_park_id);
            if (empty($car_park)) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang.not_found')
                ]);
            }
            if (in_array($request->car_park_status, [CarParkStatusEnum::FREE])) {
                $car_park->start_disabled_date = NULL;
                $car_park->end_disabled_date = NULL;
                $car_park->is_permanent_disabled = false;
            }
            $car_park->status = $request->car_park_status;
            $car_park->save();
            return response()->json([
                'success' => true,
                'message' => __('lang.store_success_message'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
            ]);
        }
    }

    public function viewAllParkingHistory(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ParkingZone);
        $transfer_type = $request->transfer_type;
        $in_date = $request->in_date;
        $from_delivery_date = $request->from_delivery_date;
        $to_delivery_date = $request->to_delivery_date;
        $license_plate = null;
        $parking_slot = $request->zone_code . $request->car_park_number;

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
        $driver_id = $request->driver_id;

        $car_list = Car::select('id', 'license_plate as name')->get();
        $lists = CarParkTransferLog::sortable()->leftjoin('car_park_transfers', 'car_park_transfers.id', '=', 'car_park_transfer_logs.car_park_transfer_id')
            ->leftjoin('cars', 'cars.id', '=', 'car_park_transfers.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'car_park_transfer_logs.driver_id')
            ->leftjoin('car_statuses', 'car_statuses.id', '=', 'car_park_transfers.car_status_id')
            ->leftJoin('car_parks', 'car_parks.id', '=', 'car_park_transfers.car_park_id')
            ->leftJoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->select(
                'car_park_transfer_logs.*',
                'car_park_transfers.est_transfer_date',
                'car_park_transfers.start_date as date_start',
                'car_park_transfers.end_date as date_end',
                'car_statuses.name as car_type_name',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'car_park_transfers.worksheet_no',
                'drivers.name as fullname',
                'car_parks.car_park_number as car_park_number',
                'car_park_zones.code as zone_code',
                'car_groups.name as car_group_name',
                'cars.rental_type as car_rental_type'
            )
            ->where(function ($q) use ($transfer_type, $in_date, $from_delivery_date, $to_delivery_date, $driver_id, $request) {
                if (!is_null($transfer_type)) {
                    $q->where('car_park_transfer_logs.transfer_type', $transfer_type);
                }
                if (!is_null($in_date)) {
                    $q->where('car_park_transfer_logs.transfer_date', 'like', '%' . $in_date . '%');
                    $q->where('car_park_transfer_logs.transfer_type', 1);
                }
                if (!is_null($from_delivery_date) && !is_null($to_delivery_date)) {
                    $q->whereDate('car_park_transfers.start_date', '<=', $from_delivery_date)->whereDate('car_park_transfers.end_date', '>=', $to_delivery_date);
                }
                // if (!is_null($request->car_id) || !is_null($request->engine_no) || !is_null($request->chassis_no)) {
                //     $q->whereIn('car_park_transfers.car_id', [$request->car_id,$request->engine_no,$request->chassis_no] );
                // }
                if (!is_null($request->engine_no)) {
                    $q->where('cars.id', $request->engine_no);
                }
                if (!is_null($request->chassis_no)) {
                    $q->where('cars.id', $request->chassis_no);
                }
                if (!is_null($request->car_id)) {
                    $q->where('cars.id', $request->car_id);
                }
                if (!is_null($driver_id)) {
                    $q->where('car_park_transfer_logs.driver_id', $driver_id);
                }
            })
            ->where('car_park_transfer_logs.car_park_id', $request->car_park_id)
            // ->where('car_park_transfer_logs.car_park_number',$request->car_park_number)
            ->search($request->s, $request)->orderBy('car_park_transfer_logs.transfer_date', 'DESC')->paginate(PER_PAGE);
        $transfer_type_list = $this->getTransferType();
        $license_plate_list = Car::select('license_plate as name', 'id')->orderBy('license_plate')->get();
        $engine_no_list = Car::select('engine_no as name', 'id')->orderBy('engine_no')->get();
        $chassis_no_list = Car::select('chassis_no as name', 'id')->orderBy('chassis_no')->get();
        $driver_list = User::select('name', 'id')->orderBy('name')->get();

        $zone_detail = CarParkZone::leftjoin('car_park_areas', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->leftjoin('car_parks', 'car_parks.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->where('car_parks.id', $request->car_park_id)
            ->branch()
            ->select(
                'car_park_zones.id',
                'car_park_zones.code',
                'car_park_zones.name',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_parks.car_park_number as car_park_number',
                'car_park_zones.code as zone_code'
            )
            ->first();

        return view('admin.car-park-areas.index-parking-history', [
            's' => $request->s,
            'lists' => $lists,
            'car_id' => $request->car_id,
            'engine_no_id' => $request->engine_no,
            'chassis_no_id' => $request->chassis_no,
            'car_list' => $car_list,
            'transfer_type_list' => $transfer_type_list,
            'transfer_type' => $transfer_type,
            'in_date' => $in_date,
            'from_delivery_date' => $from_delivery_date,
            'to_delivery_date' => $to_delivery_date,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'license_plate' => $license_plate,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'driver_list' => $driver_list,
            'driver_id' => $driver_id,
            'zone_detail' => $zone_detail,
            'car_park_number' => $request->car_park_number,
        ]);
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

    public function getEngineNo($id)
    {
        if ($id == '0') {
            $engine = Car::pluck("engine_no", "id");
        } else {
            $engine = Car::Where("id", $id)->pluck("engine_no", "id");
        }

        return json_encode($engine);
    }
    public function getChassisNo($id)
    {
        if ($id == '0') {
            $chassis = Car::pluck("chassis_no", "id");
        } else {
            $chassis = Car::Where("id", $id)->pluck("chassis_no", "id");
        }
        return json_encode($chassis);
    }
}
