<?php

namespace App\Traits;

use App\Enums\RegisterColorEnum;
use App\Enums\TransferTypeEnum;
use App\Enums\RegisterSignEnum;
use App\Models\ChangeRegistration;

trait RegisterTrait
{
    public static function getRegisteredColorList()
    {
        return collect([
            (object)[
                'id' => RegisterColorEnum::WHITE,
                'value' => RegisterColorEnum::WHITE,
                'name' => __('registers.registered_color_' . RegisterColorEnum::WHITE),
            ],
            (object)[
                'id' => RegisterColorEnum::BLACK,
                'value' => RegisterColorEnum::BLACK,
                'name' => __('registers.registered_color_' . RegisterColorEnum::BLACK),
            ],
            (object)[
                'id' => RegisterColorEnum::GREY,
                'value' => RegisterColorEnum::GREY,
                'name' => __('registers.registered_color_' . RegisterColorEnum::GREY),
            ],
            (object)[
                'id' => RegisterColorEnum::RED,
                'value' => RegisterColorEnum::RED,
                'name' => __('registers.registered_color_' . RegisterColorEnum::RED),
            ],
            (object)[
                'id' => RegisterColorEnum::BLUE,
                'value' => RegisterColorEnum::BLUE,
                'name' => __('registers.registered_color_' . RegisterColorEnum::BLUE),
            ],
            (object)[
                'id' => RegisterColorEnum::MIX,
                'value' => RegisterColorEnum::MIX,
                'name' => __('registers.registered_color_' . RegisterColorEnum::MIX),
            ],
        ]);
    }

    static function getCarList(): object
    {
        $car_list = ChangeRegistration::leftjoin('cars', 'cars.id', '=', 'change_registrations.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')->get();
        $car_list->map(function ($item) {
            if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });
        return $car_list;
    }

    static function getReciveLicenList(): object
    {
        return collect([
            [
                'id' => RegisterSignEnum::IRON_SIGN,
                'value' => RegisterSignEnum::IRON_SIGN,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::IRON_SIGN),
            ],
            [
                'id' => RegisterSignEnum::TAX_SIGN,
                'value' => RegisterSignEnum::TAX_SIGN,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::TAX_SIGN),
            ],
            [
                'id' => RegisterSignEnum::REGISTRATION_BOOK,
                'value' => RegisterSignEnum::REGISTRATION_BOOK,
                'name' => __('registers.receive_sign_' . RegisterSignEnum::REGISTRATION_BOOK),
            ],
        ]);
    }
}
