<?php

namespace App\Classes;

use App\Enums\CarEnum;
use App\Enums\CarParkStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\ZoneTypeEnum;
use Exception;
use App\Models\Car;
use App\Models\CarPark;
use App\Models\CarParkArea;
use App\Models\CarParkAreaRelation;

class CarParkManagement
{
    const ERR_INIT = 'ERR_INIT';
    const ERR_DUPLICATE_CAR_SLOT = 'ERR_DUPLICATE_CAR_SLOT';
    const ERR_FULL_SLOT = 'ERR_FULL_SLOT';
    const ERR_NOT_IN_PARK = 'ERR_NOT_IN_PARK';

    public $init_success;
    public $car_id;
    public $branch_id;
    public $car;
    public $car_status;
    public $car_rental_type;
    public $car_group_id;
    public $car_park_area_id;
    public $free_car_park_new;
    public $check_booking;

    public function __construct($car_id, $branch_id = null)
    {
        $this->car_id = $car_id;
        $this->branch_id = $branch_id;
        $this->car = Car::with([
            'carClass.carType.carGroup'
        ])->find($this->car_id);
        if (empty($this->car)) {
            throw new Exception(self::ERR_INIT, 0);
        }
        $this->car_status = $this->car->status;
        $this->car_rental_type = $this->car->rental_type;
        $this->car_group_id = null;
        $this->car_park_area_id = null;
        $this->free_car_park_new = null;
        $this->check_booking = false;
        if ($this->car->carClass) {
            if ($this->car->carClass->carType) {
                if ($this->car->carClass->carType->carGroup) {
                    $this->car_group_id = $this->car->carClass->carType->carGroup->id;
                }
            }
        }
    }

    function checkBooking()
    {
        return $this->check_booking = true;
    }

    function setCarParkArea($car_park_area_id)
    {
        return $this->car_park_area_id = $car_park_area_id;
    }

    function isActivated()
    {
        return CarPark::where('car_id', $this->car_id)->exists();
    }

    function deActivate()
    {
        if (!$this->isActivated()) {
            throw new Exception(self::ERR_NOT_IN_PARK, 0);
        }
        $car_park = CarPark::where('car_id', $this->car_id)->first();
        $car_park->car_id = null;
        $car_park->status = CarParkStatusEnum::FREE;
        $car_park->save();
        // $car_park->update([
        //     'car_id' => null,
        //     'status' => CarParkStatusEnum::FREE
        // ]);
        $this->free_car_park_new = $car_park->id;
    }

    function activate($car_park_id = null)
    {
        if ($this->isActivated()) {
            throw new Exception(self::ERR_DUPLICATE_CAR_SLOT, 0);
        }
        // find slot
        $free_car_parks = $this->getFreeSlots();
        if (!empty($car_park_id)) {
            $free_car_parks = $free_car_parks->filter(function ($item) use ($car_park_id) {
                return (strcmp($item->id, $car_park_id) == 0);
            });
        }
        if (sizeof($free_car_parks) <= 0) {
            throw new Exception(self::ERR_FULL_SLOT, 0);
        }
        $free_car_park = $free_car_parks->first();
        $free_car_park->car_id = $this->car_id;
        $free_car_park->status = CarParkStatusEnum::USED;
        $free_car_park->save();
        $this->free_car_park_new = $free_car_park->id;
    }

    function carPark()
    {
        return $this->free_car_park_new;
    }

    function getFreeSlots()
    {
        // find relation car_group_id
        $car_park_area_relation_ids = [];
        if (!empty($this->car_group_id)) {
            $car_park_area_relation_ids = CarParkAreaRelation::select('car_park_area_id')->where('car_group_id', $this->car_group_id)->pluck('car_park_area_id')->toArray();
        }

        // find car park by zone + area
        $car_parks = CarPark::select('car_parks.id', 'car_parks.car_park_number')
            ->join('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->join('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_park_zones.branch_id', $this->branch_id)
            ->where(function ($query) {
                // check car_status
                if (in_array($this->car_status, [CarEnum::NEWCAR, CarEnum::NEWCAR_PENDING])) {
                    $query->where('car_park_areas.zone_type', ZoneTypeEnum::NEWCAR);
                } else if (in_array($this->car_status, [CarEnum::PENDING_REVIEW, CarEnum::READY_TO_USE])) {
                    if (strcmp($this->car_rental_type, RentalTypeEnum::SHORT) == 0) {
                        $query->where('car_park_areas.zone_type', ZoneTypeEnum::SHORT);
                    } else if (strcmp($this->car_rental_type, RentalTypeEnum::LONG) == 0) {
                        $query->where('car_park_areas.zone_type', ZoneTypeEnum::LONG);
                    } else {
                        $query->where('car_park_areas.zone_type', ZoneTypeEnum::POOL);
                    }
                } else {
                    $query->where('car_park_areas.zone_type', ZoneTypeEnum::POOL);
                }
            })
            ->where(function ($query) use ($car_park_area_relation_ids) {
                // check car_group
                if (sizeof($car_park_area_relation_ids) > 0) {
                    $query->whereIn('car_park_areas.id', $car_park_area_relation_ids);
                }
                if (!empty($this->car_park_area_id)) {
                    $query->where('car_park_areas.id', $this->car_park_area_id);
                }

                if ($this->check_booking) {
                    $query->where('car_parks.status', CarParkStatusEnum::BOOKING);
                } else {
                    $query->where('car_parks.status', CarParkStatusEnum::FREE);
                }
            })
            ->orderBy('car_park_zones.name')
            ->orderBy('car_parks.car_park_number')
            ->get();
        return $car_parks;
    }
}
