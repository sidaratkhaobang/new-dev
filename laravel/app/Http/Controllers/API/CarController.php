<?php

namespace App\Http\Controllers\API;

use App\Enums\RentalTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Traits\CarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TransferTypeEnum;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;

        $rental = DB::table('cars_rental_categories')
            ->leftjoin('rental_categories', 'rental_categories.id', '=', 'cars_rental_categories.rental_category_id')
            ->select(
                'cars_rental_categories.car_id',
                DB::raw("group_concat(rental_categories.id SEPARATOR ', ') as rental_category_id"),
            )
            ->groupBy('cars_rental_categories.car_id');

        $list = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('cars_rental_categories as car_ren', 'car_ren.car_id', '=', 'cars.id')
            ->leftjoinSub($rental, 'rental', function ($join) {
                $join->on('rental.car_id', '=', 'cars.id');
            })
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('cars.license_plate', 'like', '%' . $s . '%');
                    $q->orWhere('cars.code', 'like', '%' . $s . '%');
                    $q->orWhere('cars.engine_no', 'like', '%' . $s . '%');
                    $q->orWhere('cars.chassis_no', 'like', '%' . $s . '%');
                    $q->orWhere('cars.car_class_id', 'like', '%' . $s . '%');
                    $q->orWhere('car_colors.name', 'like', '%' . $s . '%');
                    $q->orWhere('car_classes.name', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($request->rental_category_id), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('car_ren.rental_category_id', 'like', '%' . $request->rental_category_id . '%');
                });
            })
            ->when(!empty($request->car_class_id), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('car_ren.rental_category_id', $request->car_class_id);
                });
            })
            ->where('cars.rental_type', RentalTypeEnum::SHORT)
            ->groupBy(
                'cars.id',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no'
            )
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = get_car_detail2($request->id);
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $car_accessory = CarAccessory::select(
            'accessories.name',
            'car_accessories.amount',
            'car_accessories.remark')
            ->leftjoin(
                'accessories',
                'accessories.id',
                'car_accessories.accessory_id')
            ->where('car_id', $request->id)
            ->get();
        $transfer_logs = CarTrait::getCarTransferLog($request->id, [ 'transfer_type' => TransferTypeEnum::IN]);
        $data->updated_at = null;
        if (sizeof($transfer_logs) > 0) {
            $first = $transfer_logs->first();
            $data->updated_at = $first->updated_at;
        }
        $data->car_accessories = $car_accessory ?? [];
//        $data = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no', 'car_colors.name as car_color_name', 'car_classes.name as car_class_name', 'branches.name as branch_name')
//            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
//            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
//            ->leftjoin('branches', 'branches.id', '=', 'cars.branch_id')
//
//            ->leftJoin('car_parts as cp1', 'cp1.id', '=', 'cars.gear_id')
//            ->leftJoin('car_parts as cp2', 'cp2.id', '=', 'cars.drive_system_id')
//            ->leftJoin('car_parts as cp3', 'cp3.id', '=', 'cars.car_seat_id')
//            ->leftJoin('car_parts as cp4', 'cp4.id', '=', 'cars.side_mirror_id')
//            ->leftJoin('car_parts as cp5', 'cp5.id', '=', 'cars.air_bag_id')
//            ->leftJoin('car_parts as cp6', 'cp6.id', '=', 'cars.central_lock_id')
//            ->leftJoin('car_parts as cp7', 'cp7.id', '=', 'cars.front_brake_id')
//            ->leftJoin('car_parts as cp8', 'cp8.id', '=', 'cars.rear_brake_id')
//            ->leftJoin('car_parts as cp9', 'cp9.id', '=', 'cars.abs_id')
//            ->leftJoin('car_parts as cp10', 'cp10.id', '=', 'cars.anti_thift_system_id')
//
//            ->leftJoin('car_batteries', 'car_batteries.id', '=', 'cars.car_battery_id')
//            ->leftJoin('car_tires', 'car_tires.id', '=', 'cars.car_tire_id')
//            ->leftJoin('car_wipers', 'car_wipers.id', '=', 'cars.car_wiper_id')
//
//            ->addSelect([
//                'cars.engine_size',
//
//                'cars.gear_id',
//                'cp1.name as gear_name',
//                'cars.drive_system_id',
//                'cp2.name as drive_system_name',
//                'cars.car_seat_id',
//                'cp3.name as car_seat_name',
//                'cars.side_mirror_id',
//                'cp4.name as side_mirror_name',
//                'cars.air_bag_id',
//                'cp5.name as air_bag_name',
//                'cars.central_lock_id',
//                'cp6.name as central_lock_name',
//                'cars.front_brake_id',
//                'cp7.name as front_brake_name',
//                'cars.rear_brake_id',
//                'cp8.name as rear_brake_name',
//                'cars.abs_id',
//                'cp9.name as abs_name',
//                'cars.anti_thift_system_id',
//                'cp10.name as anti_thift_system_name',
//
//                'cars.oil_tank_capacity',
//                'cars.oil_type',
//
//                'cars.car_battery_id',
//                'car_batteries.name as car_battery_name',
//                'cars.car_tire_id',
//                'car_tires.name as car_tire_name',
//                'cars.car_wiper_id',
//                'car_wipers.name as car_wiper_name',
//            ])
//
//            ->where('cars.id', $request->id)
//            ->where('cars.rental_type', RentalTypeEnum::SHORT)
//            ->first();
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
