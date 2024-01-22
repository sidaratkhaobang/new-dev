<?php

namespace App\Http\Controllers\Admin;

use App\Classes\CarParkManagement;
use App\Enums\Actions;
use App\Enums\CarParkAreaStatusEnum;
use App\Enums\CarParkStatusEnum;
use App\Enums\Resources;
use App\Enums\ZoneTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CarGroup;
use App\Models\CarPark;
use App\Models\CarParkArea;
use App\Models\CarParkAreaRelation;
use App\Models\CarParkZone;
use App\Models\CarType;
use App\Models\ParkingLot;
use App\Traits\CarTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ParkingLotController extends Controller
{
    use CarTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ParkingZone);
        $car_group_id = $request->car_group_id;
        $zone_size_id = $request->zone_size_id;
        $car_group_list = $this->getCarGroupList();
        $car_zone_size_list = $this->getCarZoneSizeList();

        $list = CarParkZone::select('car_park_zones.*')
            ->sortable('code')
            ->when($zone_size_id, function ($query) use ($zone_size_id) {
                $query->where('car_park_zones.zone_size', $zone_size_id);
            })
            ->when($car_group_id, function ($query) use ($car_group_id) {
                $query->leftjoin('car_park_areas', 'car_park_areas.car_park_zone_id', '=', 'car_park_zones.id');
                $query->leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id');
                $query->where('car_park_areas_relation.car_group_id', $car_group_id);
            })
            ->branch()
            ->search($request->s)
            ->distinct()
            ->paginate(PER_PAGE);
        $over_all_total = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->branch()
            ->count();
        $over_all_available = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.status', CarParkStatusEnum::FREE)
            ->branch()
            ->count();
        $over_all_unavailable = $over_all_total - $over_all_available;

        $list->map(function ($item) {
            $car_slot_list = CarParkArea::leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
                ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
                ->where('car_park_areas.car_park_zone_id', $item->id)
                ->select(
                    'car_park_areas.id',
                    'car_park_areas.car_park_zone_id',
                    'car_park_areas.area_size',
                    'car_park_areas.start_number',
                    'car_park_areas.end_number',
                    'car_park_areas.zone_type',
                    'car_park_areas.status',
                    DB::raw("group_concat(car_groups.id  SEPARATOR ', ')  as car_group_array"),
                    DB::raw("group_concat(car_groups.name  SEPARATOR ', ')  as car_group_text")
                )
                ->groupBy(
                    'car_park_areas.id',
                    'car_park_areas.car_park_zone_id',
                    'car_park_areas.area_size',
                    'car_park_areas.start_number',
                    'car_park_areas.end_number',
                    'car_park_areas.zone_type',
                    'car_park_areas.status',
                )
                ->get();

            $sum_total_slot = 0;
            $sum_available_slot = 0;
            $sum_unavailable_slot = 0;

            $car_slot_list->map(function ($car_park_area) use (&$sum_total_slot, &$sum_available_slot, &$sum_unavailable_slot) {
                $available_car_count = $this->countAvailableCarParkByNumber($car_park_area->id, $car_park_area->start_number, $car_park_area->end_number);
                $unavailable_car_count = $this->countUnAvailableCarParkByNumber($car_park_area->id, $car_park_area->start_number, $car_park_area->end_number);
                $car_park_area->area_size_text =  __('parking_lots.area_' . $car_park_area->area_size);
                $car_park_area->total_slot = $car_park_area->end_number - $car_park_area->start_number + 1;
                $car_park_area->car_groups = explode(',', $car_park_area->car_group_array);
                $car_park_area->available_car_count = $available_car_count;
                $car_park_area->unavailable_car_count = $unavailable_car_count;
                $sum_total_slot += $car_park_area->total_slot;
                $sum_available_slot += $available_car_count;
                $sum_unavailable_slot += $unavailable_car_count;
            });
            $item->car_slot_list = $car_slot_list;
            $item->sum_total_slot = $sum_total_slot;
            $item->sum_available_slot = $sum_available_slot;
            $item->sum_unavailable_slot = $sum_unavailable_slot;
        });

        return view('admin.parking-lots.index', [
            'list' => $list,
            's' => $request->s,
            'car_group_id' => $car_group_id,
            'zone_size_id' => $zone_size_id,
            'over_all_total' => $over_all_total,
            'over_all_unavailable' => $over_all_unavailable,
            'over_all_available' => $over_all_available,
            'car_group_list' => $car_group_list,
            'car_zone_size_list' => $car_zone_size_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);
        $d = new CarParkZone();
        $car_group_list = $this->getCarGroupList();
        $car_zone_size_list = $this->getCarZoneSizeList();
        $zone_type_list = $this->getZoneType();

        $page_title = __('parking_lots.zone');
        return view('admin.parking-lots.form', [
            'd' => $d,
            'page_title' => $page_title,
            'car_zone_size_list' => $car_zone_size_list,
            'car_group_list' => $car_group_list,
            'zone_type_list' => $zone_type_list
        ]);
    }

    public function edit(CarParkZone $parking_lot)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);
        $car_group_list = $this->getCarGroupList();
        $car_zone_size_list = $this->getCarZoneSizeList();
        $zone_type_list = $this->getZoneType();
        $page_title = __('parking_lots.zone');
        $zone_id = $parking_lot->id;
        $car_slot_list = CarParkArea::leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->where('car_park_areas.car_park_zone_id', $zone_id)
            ->select(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
                'car_park_areas.zone_type',
                // 'car_park_areas.zone_type as zone_type_name',
                DB::raw("group_concat(car_groups.id  SEPARATOR ', ')  as car_group_array"),
                DB::raw("group_concat(car_groups.name  SEPARATOR ', ')  as car_group_text")
            )
            ->groupBy(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
                'car_park_areas.zone_type',
                // 'car_park_areas.zone_type as zone_type_name',
            )
            ->get();
        $sum_total_slot = 0;
        $sum_available_car_slot_count = 0;
        $sum_unavailable_car_slot_count = 0;

        $car_slot_list->map(function ($item) use (&$sum_total_slot, &$zone_id, &$sum_available_car_slot_count, &$sum_unavailable_car_slot_count) {
            $item->area_size_text =  __('parking_lots.area_' . $item->area_size);
            $item->zone_type_name =  __('parking_lots.zone_type_' . $item->zone_type);
            $item->total_slot = $item->end_number - $item->start_number + 1;
            $item->car_groups = explode(',', $item->car_group_array);
            $available_car_count = $this->countAvailableCarParkByNumber($item->id, $item->start_number, $item->end_number);
            $unavailable_car_count = $this->countUnAvailableCarParkByNumber($item->id, $item->start_number, $item->end_number);
            $item->available_car_slot_count = $available_car_count;
            $item->unavailable_car_slot_count = $unavailable_car_count;
            $sum_total_slot += $item->total_slot;
            $sum_available_car_slot_count += $available_car_count;
            $sum_unavailable_car_slot_count += $unavailable_car_count;
            return $item;
        });
        return view('admin.parking-lots.form', [
            'd' => $parking_lot,
            'page_title' => $page_title,
            'car_zone_size_list' => $car_zone_size_list,
            'car_slot_list' => $car_slot_list,
            'zone_type_list' => $zone_type_list,
            'sum_total_slot' => $sum_total_slot,
            'car_group_list' => $car_group_list,
            'sum_available_car_slot_count' => $sum_available_car_slot_count,
            'sum_unavailable_car_slot_count' => $sum_unavailable_car_slot_count,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('car_park_zones', 'code')->whereNull('deleted_at')->ignore($request->id),

            ],
            'name' => [
                'required', 'string', 'max:255',
            ],
            'zone_size' => 'required'
        ], [], [
            'code' => __('parking_lots.zone_code'),
            'name' => __('parking_lots.zone_name'),
            'zone_size' => __('parking_lots.zone_size'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_park_zone = CarParkZone::firstOrNew(['id' => $request->id]);
        $car_park_zone_exist = $car_park_zone->exists;
        $car_park_zone->name = $request->name;
        $car_park_zone->branch_id = get_branch_id();
        $car_park_zone->code = $request->code;
        $car_park_zone->zone_size = $request->zone_size;
        $car_park_zone->status = STATUS_ACTIVE;
        $car_park_zone->save();
        if (isset($request->deleted_car_park_area)) {
            CarParkArea::whereIn('id', $request->deleted_car_park_area)->delete();
            CarPark::whereIn('car_park_area_id', $request->deleted_car_park_area)->forceDelete();
        }
        if (isset($request->delete_car_park)) {
            CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
                ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
                ->where('car_park_zones.id', $car_park_zone->id)
                ->whereIn('car_parks.car_park_number', $request->delete_car_park)
                ->forceDelete();
        }
        if (isset($request->add_car_park)) {
            $save_data = [];
            foreach ($request->add_car_park as $i => $add_car_park) {
                $save_data[$i]['id'] = (string) Str::orderedUuid();
                $save_data[$i]['car_park_number'] = $add_car_park['number'];
                $save_data[$i]['car_park_area_id'] = $add_car_park['area_id'];
                $save_data[$i]['status'] = CarParkStatusEnum::FREE;
            }
            CarPark::insert($save_data);
        }

        if (isset($request->car_zone)) {
            foreach ($request->car_zone as $key => $car_zone) {
                $start = $car_zone['start_number'];
                $end = $car_zone['end_number'];
                if (isset($car_zone['id'])) {
                    $car_park_area = CarParkArea::find($car_zone['id']);
                } else {
                    $car_park_area = new CarParkArea;
                }
                $car_park_area->car_park_zone_id = $car_park_zone->id;
                $car_park_area->area_size = $car_zone['area_size'];
                $car_park_area->zone_type = $car_zone['zone_type'];
                $car_park_area->start_number = $start;
                $car_park_area->end_number = $end;
                $car_park_area->save();

                if (!isset($car_zone['id'])) {
                    $save_data = [];
                    for ($i = $start; $i <= $end; $i++) {
                        $save_data[$i]['id'] = (string) Str::orderedUuid();
                        $save_data[$i]['car_park_number'] = $i;
                        $save_data[$i]['car_park_area_id'] = $car_park_area->id;
                        $save_data[$i]['status'] = CarParkStatusEnum::FREE;
                    }
                    CarPark::insert($save_data);
                }

                $car_groups = explode(',', $car_zone['car_groups']);

                CarParkAreaRelation::where('car_park_area_id', $car_park_area->id)->delete();
                foreach ($car_groups as $car_group) {
                    $car_park_area_relation = new CarParkAreaRelation;
                    $car_park_area_relation->car_group_id = trim($car_group);
                    $car_park_area_relation->car_park_area_id = $car_park_area->id;
                    $car_park_area_relation->save();
                }
            }
        }

        $redirect_route = route('admin.parking-lots.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(CarParkZone $parking_lot)
    {
        $this->authorize(Actions::View . '_' . Resources::ParkingZone);
        $car_group_list = $this->getCarGroupList();
        $car_zone_size_list = $this->getCarZoneSizeList();
        $zone_type_list = $this->getZoneType();
        $page_title = __('parking_lots.zone');
        $zone_id = $parking_lot->id;
        $car_slot_list = CarParkArea::leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->where('car_park_areas.car_park_zone_id', $zone_id)
            ->select(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
                'car_park_areas.zone_type',
                DB::raw("group_concat(car_groups.id  SEPARATOR ', ')  as car_group_array"),
                DB::raw("group_concat(car_groups.name  SEPARATOR ', ')  as car_group_text")
            )
            ->groupBy(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
                'car_park_areas.zone_type',
            )
            ->get();
        $sum_total_slot = 0;
        $sum_available_car_slot_count = 0;
        $sum_unavailable_car_slot_count = 0;

        $car_slot_list->map(function ($item) use (&$sum_total_slot, &$zone_id, &$sum_available_car_slot_count, &$sum_unavailable_car_slot_count) {
            $item->area_size_text =  __('parking_lots.area_' . $item->area_size);
            $item->zone_type_name =  __('parking_lots.zone_type_' . $item->zone_type);
            $item->total_slot = $item->end_number - $item->start_number + 1;
            $item->car_groups = explode(',', $item->car_group_array);
            $available_car_count = $this->countAvailableCarParkByNumber($item->id, $item->start_number, $item->end_number);
            $unavailable_car_count = $this->countUnAvailableCarParkByNumber($item->id, $item->start_number, $item->end_number);
            $item->available_car_slot_count = $available_car_count;
            $item->unavailable_car_slot_count = $unavailable_car_count;
            $sum_total_slot += $item->total_slot;
            $sum_available_car_slot_count += $available_car_count;
            $sum_unavailable_car_slot_count += $unavailable_car_count;
            return $item;
        });
        return view('admin.parking-lots.view', [
            'd' => $parking_lot,
            'view' => true,
            'page_title' => $page_title,
            'car_zone_size_list' => $car_zone_size_list,
            'car_slot_list' => $car_slot_list,
            'sum_total_slot' => $sum_total_slot,
            'car_group_list' => $car_group_list,
            'zone_type_list' => $zone_type_list,
            'sum_available_car_slot_count' => $sum_available_car_slot_count,
            'sum_unavailable_car_slot_count' => $sum_unavailable_car_slot_count,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);
        $car_exist = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_park_zones.id', $id)
            ->whereNotNull('car_parks.car_id')
            ->exists();
        if ($car_exist) {
            return response()->json([
                'success' => false,
                'message' => __('parking_lots.delete_fail')
            ]);
        }
        $car_park_zone = CarParkZone::find($id);
        $car_park_zone->delete();
        $car_park = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_park_zones.id', $id);
        $car_park->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    private function getCarZoneSizeList()
    {
        $car_zone_lists = [
            (object)[
                'id' => "1",
                'name' => __('parking_lots.small_zone'),
                'value' => "1",
            ],
            (object)[
                'id' => "2",
                'name' => __('parking_lots.big_zone'),
                'value' => "2",
            ]
        ];
        return $car_zone_lists;
    }

    private function getCarGroupList()
    {
        return CarGroup::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function countAvailableCarParkByNumber($area_id, $from, $to)
    {
        return CarPark::when($area_id, function ($query) use ($area_id) {
            $query->where('car_park_area_id', $area_id);
        })
            ->whereBetween('car_park_number', [$from, $to])
            ->where('status', CarParkStatusEnum::FREE)
            ->count();
    }

    public function countUnAvailableCarParkByNumber($area_id, $from, $to)
    {
        return CarPark::when($area_id, function ($query) use ($area_id) {
            $query->where('car_park_area_id', $area_id);
        })
            ->whereBetween('car_park_number', [$from, $to])
            ->whereNotIn('status', [CarParkStatusEnum::FREE])
            ->count();
    }

    public function updateCarParkAreaStatus(Request $request)
    {
        if ($request->car_park_area_id && $request->car_park_area_status) {
            $car_park_area = CarParkArea::find($request->car_park_area_id);
            $car_exist = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
                ->where('car_park_areas.id', $car_park_area->id)
                ->whereNotNull('car_parks.car_id')
                ->exists();
            if ($car_exist) {
                return response()->json([
                    'success' => false,
                    'message' => __('parking_lots.turn_off_fail')
                ]);
            }
            $car_park_area->status = $request->car_park_area_status;
            $car_park_area->save();
            if (strcmp($request->car_park_area_status, CarParkAreaStatusEnum::INACTIVE == 0)) {
                CarPark::where('car_park_area_id', $car_park_area->id)->update([
                    'status' => CarParkStatusEnum::DISABLED
                ]);
            }
            if (strcmp($request->car_park_area_status, CarParkAreaStatusEnum::ACTIVE == 0)) {
                CarPark::where('car_park_area_id', $car_park_area->id)->update([
                    'status' => CarParkStatusEnum::FREE
                ]);
            }

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

    public function showShiftCars(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);
        $area_id = $request->car_park_area_id;
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
                'car_park_zones.name',
                DB::raw("group_concat(car_groups.name  SEPARATOR ', ')  as car_group_list"),
            )
            ->groupBy(
                'car_park_zones.id',
                'car_park_zones.code',
                'car_park_zones.name',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_zones.name',
            )
            ->first();

        $car_list = CarPark::leftjoin('cars', 'cars.id', '=', 'car_parks.car_id')
            ->leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->leftjoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            // ->leftjoin('car_park_transfers', 'car_park_transfers.car_id', '=', 'cars.id')
            // ->leftjoin('car_statuses', 'car_statuses.id', '=', 'car_park_transfers.car_status_id')
            // ->leftjoin('car_park_transfer_logs', 'car_park_transfer_logs.car_park_transfer_id', '=', 'car_park_transfers.id')
            ->where('car_park_areas.id', $area_id)
            // ->whereNotNull('car_parks.car_id')
            ->whereIn('car_parks.status', [CarParkStatusEnum::USED, CarParkStatusEnum::BOOKING])
            ->select(
                'cars.id as car_id',
                'car_park_zones.code as zone_code',
                'car_parks.id as car_park_id',
                'car_parks.car_park_number as car_park_number',
                'car_classes.name as class_name',
                'car_groups.name as group_name',
                'car_park_areas.area_size as area_size',
                'car_categories.reserve_small_size as reserve_small_size',
                'car_categories.reserve_big_size as reserve_big_size',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                // 'car_park_transfers.start_date as start_date',
                // 'car_park_transfers.end_date as end_date',
                // 'car_statuses.name as car_status',
            )
            ->get();

        $page_title = __('parking_lots.shift');
        return view('admin.parking-lots.shift', [
            'page_title' => $page_title,
            'zone_detail' => $zone_detail,
            'car_list' => $car_list,
            'area_id' => $area_id
        ]);
    }

    public function getCarParkAreaDetail(Request $request)
    {
        $car_park_area = CarParkArea::leftjoin('car_park_areas_relation', 'car_park_areas_relation.car_park_area_id', '=', 'car_park_areas.id')
            ->leftjoin('car_groups', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->where('car_park_areas.id', $request->car_park_area_id)
            ->select(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
                DB::raw("group_concat(car_groups.name  SEPARATOR ', ')  as car_group_text")
            )
            ->groupBy(
                'car_park_areas.id',
                'car_park_areas.car_park_zone_id',
                'car_park_areas.area_size',
                'car_park_areas.start_number',
                'car_park_areas.end_number',
                'car_park_areas.status',
            )
            ->first();
        if ($car_park_area) {
            $car_park_area->total_slots = $car_park_area->end_number - $car_park_area->start_number + 1;
            $car_park_area->available_slots = $this->countAvailableCarParkByNumber($car_park_area->id, $car_park_area->start_number, $car_park_area->end_number);
            return response()->json([
                'success' => true,
                'data' => $car_park_area,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
            ]);
        }
    }

    public function shiftCarArea(Request $request)
    {
        $selected_cars = $request->selected_cars;
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required',
            'slot_number' => 'required',
        ], [], [
            'zone_id' => __('parking_lots.zone_text'),
            'slot_number' => __('parking_lots.slot_number'),
            'selected_cars' => __('parking_lots.selected_car_parks'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (empty($selected_cars) || sizeof($selected_cars) < 1) {
            return response()->json([
                'success' => false,
                'message' => __('parking_lots.selected_car_parks_required')
            ], 422);
        }

        $branch_id = get_branch_id();
        foreach ($selected_cars as $key => $car_park_id) {
            $car_park = CarPark::find($car_park_id);
            $car_id = $car_park->car_id;
            try {
                DB::transaction(function () use ($request, $car_id, $branch_id) {
                    $cpm = new CarParkManagement($car_id, $branch_id);
                    if (!$cpm->isActivated()) {
                        throw new Exception('Car not in park', 0);
                    }
                    $cpm->setCarParkArea($request->slot_number);
                    $cpm->deActivate();
                    $cpm->activate();
                });
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }
        return $this->responseComplete();
    }

    public static function getZoneType()
    {
        $status = collect([
            (object) [
                'id' => ZoneTypeEnum::NEWCAR,
                'name' => __('parking_lots.zone_type_' . ZoneTypeEnum::NEWCAR),
                'value' => ZoneTypeEnum::NEWCAR,
            ],
            (object) [
                'id' => ZoneTypeEnum::SHORT,
                'name' => __('parking_lots.zone_type_' . ZoneTypeEnum::SHORT),
                'value' => ZoneTypeEnum::SHORT,
            ],
            (object) [
                'id' => ZoneTypeEnum::LONG,
                'name' => __('parking_lots.zone_type_' . ZoneTypeEnum::LONG),
                'value' => ZoneTypeEnum::LONG,
            ],
            (object) [
                'id' => ZoneTypeEnum::POOL,
                'name' => __('parking_lots.zone_type_' . ZoneTypeEnum::POOL),
                'value' => ZoneTypeEnum::POOL,
            ],
        ]);
        return $status;
    }
}
