<?php

namespace App\Http\Controllers\API;

use App\Models\CarClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CarClassController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $car_brand_id = $request->car_brand_id;
        $car_type_id = $request->car_type_id;
        $list = CarClass::leftJoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->select(
                'car_classes.id',
                'car_classes.name',
                'car_classes.full_name',
                'car_types.id as car_type_id',
                'car_types.name as car_type_name',
                'car_brands.id as car_brand_id',
                'car_brands.name as car_brand_name',
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('car_classes.name', 'like', '%' . $s . '%');
                    $q->orWhere('car_classes.code', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($car_brand_id), function ($query) use ($car_brand_id) {
                return $query->where('car_brands.id', $car_brand_id);
            })
            ->when(!empty($car_type_id), function ($query) use ($car_type_id) {
                return $query->where('car_types.id', $car_type_id);
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = CarClass::leftJoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')

            ->leftJoin('car_parts as cp1', 'cp1.id', '=', 'car_classes.gear_id')
            ->leftJoin('car_parts as cp2', 'cp2.id', '=', 'car_classes.drive_system_id')
            ->leftJoin('car_parts as cp3', 'cp3.id', '=', 'car_classes.car_seat_id')
            ->leftJoin('car_parts as cp4', 'cp4.id', '=', 'car_classes.side_mirror_id')
            ->leftJoin('car_parts as cp5', 'cp5.id', '=', 'car_classes.air_bag_id')
            ->leftJoin('car_parts as cp6', 'cp6.id', '=', 'car_classes.central_lock_id')
            ->leftJoin('car_parts as cp7', 'cp7.id', '=', 'car_classes.front_brake_id')
            ->leftJoin('car_parts as cp8', 'cp8.id', '=', 'car_classes.rear_brake_id')
            ->leftJoin('car_parts as cp9', 'cp9.id', '=', 'car_classes.abs_id')
            ->leftJoin('car_parts as cp10', 'cp10.id', '=', 'car_classes.anti_thift_system_id')

            ->leftJoin('car_batteries', 'car_batteries.id', '=', 'car_classes.car_battery_id')
            ->leftJoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftJoin('car_wipers', 'car_wipers.id', '=', 'car_classes.car_wiper_id')

            ->select(
                'car_classes.id',
                'car_classes.name',
                'car_classes.full_name',
                'car_types.id as car_type_id',
                'car_types.name as car_type_name',
                'car_brands.id as car_brand_id',
                'car_brands.name as car_brand_name',
                'car_classes.description',
                'car_classes.engine_size',
                'car_classes.manufacturing_year',

                'car_classes.gear_id',
                'cp1.name as gear_name',
                'car_classes.drive_system_id',
                'cp2.name as drive_system_name',
                'car_classes.car_seat_id',
                'cp3.name as car_seat_name',
                'car_classes.side_mirror_id',
                'cp4.name as side_mirror_name',
                'car_classes.air_bag_id',
                'cp5.name as air_bag_name',
                'car_classes.central_lock_id',
                'cp6.name as central_lock_name',
                'car_classes.front_brake_id',
                'cp7.name as front_brake_name',
                'car_classes.rear_brake_id',
                'cp8.name as rear_brake_name',
                'car_classes.abs_id',
                'cp9.name as abs_name',
                'car_classes.anti_thift_system_id',
                'cp10.name as anti_thift_system_name',

                'car_classes.oil_tank_capacity',
                'car_classes.oil_type',

                'car_classes.car_battery_id',
                'car_batteries.name as car_battery_name',
                'car_classes.car_tire_id',
                'car_tires.name as car_tire_name',
                'car_classes.car_wiper_id',
                'car_wipers.name as car_wiper_name',

                'car_classes.remark',
                'car_classes.website',
                'car_classes.status',
            )
            ->where('car_classes.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
