<?php

namespace App\Traits;

use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\CarParkTransferLog;
use Illuminate\Support\Facades\DB;

trait CarTrait
{
    public static function getLicensePlateList($request = null)
    {
        $license_plate_list = Car::select('id', 'license_plate as name')
            ->whereNotNull('license_plate')
            ->get();
        return $license_plate_list;
    }

    public static function getChassisList($request = null)
    {
        $chassis_list = Car::select('id', 'chassis_no as name')
            ->whereNotNull('chassis_no')
            ->get();
        return $chassis_list;
    }

    public static function getEngineList($request = null)
    {
        $engine_no_list = Car::select('id', 'engine_no as name')
            ->whereNotNull('engine_no')
            ->get();
        return $engine_no_list;
    }

    public static function getDefaultCarZone($car_id, $car_park_area_id = null)
    {
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
            ->where(function ($query) use ($car_park_area_id) {
                if (!empty($car_park_area_id)) {
                    $query->where('car_park_areas.id', $car_park_area_id);
                }
            })
            ->select(
                'car_park_areas.car_park_zone_id as car_park_zone_id',
                'car_park_zones.name as car_park_zone_name',
                'car_parks.id as car_park_id',
                'car_parks.car_park_number as car_park_number',
            )->get();
        return $data;
    }

    static function getCarTransferLog($car_id, $optionals = [])
    {
        $car_park_logs = CarParkTransferLog::leftjoin('car_park_transfers', 'car_park_transfer_logs.car_park_transfer_id', '=', 'car_park_transfers.id')
            ->select('car_park_transfer_logs.*')
            ->where('car_park_transfers.car_id', $car_id)
            ->where(function ($query) use ($optionals) {
                if (isset($optionals['transfer_type'])) {
                    $query->where('car_park_transfer_logs.transfer_type', $optionals['transfer_type']);
                }
            })
            ->orderBy('car_park_transfer_logs.updated_at', 'desc')
            ->get();
        return $car_park_logs;
    }
}
