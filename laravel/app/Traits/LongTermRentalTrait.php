<?php

namespace App\Traits;

use App\Enums\CreditorTypeEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\LongTermRentalJobType;
use App\Enums\LongTermRentalStatusEnum;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\Creditor;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use App\Models\LongTermRentalType;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

trait LongTermRentalTrait
{
    public function getHaveAccessoryList()
    {
        return [
            [
                'id' => 1,
                'value' => 'fff',
                'name' => __('lang.have'),
            ],
            [
                'id' => 0,
                'value' => 'dddd',
                'name' => __('lang.no_have'),
            ],
        ];
    }

    // public function getRentalJobTypeList()
    // {
    //     return collect([
    //         (object)[
    //             'id' => LongTermRentalJobType::AUCTION,
    //             'value' => LongTermRentalJobType::AUCTION,
    //             'name' => __('long_term_rentals.job_type_' . LongTermRentalJobType::AUCTION),
    //         ],
    //         (object)[
    //             'id' => LongTermRentalJobType::QUOTATION,
    //             'value' => LongTermRentalJobType::QUOTATION,
    //             'name' => __('long_term_rentals.job_type_' . LongTermRentalJobType::QUOTATION),
    //         ],
    //         (object)[
    //             'id' => LongTermRentalJobType::EBIDDING,
    //             'value' => LongTermRentalJobType::EBIDDING,
    //             'name' => __('long_term_rentals.job_type_' . LongTermRentalJobType::EBIDDING),
    //         ],
    //         (object)[
    //             'id' => LongTermRentalJobType::BUDGET,
    //             'value' => LongTermRentalJobType::BUDGET,
    //             'name' => __('long_term_rentals.job_type_' . LongTermRentalJobType::BUDGET),
    //         ],
    //     ]);
    // }

    static function getRentalApprovalList()
    {
        return collect([
            (object)[
                'id' => LongTermRentalApprovalTypeEnum::AFFILIATED,
                'value' => LongTermRentalApprovalTypeEnum::AFFILIATED,
                'name' => __('long_term_rentals.type_' . LongTermRentalApprovalTypeEnum::AFFILIATED),
            ],
            (object)[
                'id' => LongTermRentalApprovalTypeEnum::UNAFFILIATED,
                'value' => LongTermRentalApprovalTypeEnum::UNAFFILIATED,
                'name' => __('long_term_rentals.type_' . LongTermRentalApprovalTypeEnum::UNAFFILIATED),
            ]
        ]);
    }

    static function getLongTermRentalApproveStatusList()
    {
        return collect([
            (object)[
                'id' => LongTermRentalStatusEnum::COMPLETE,
                'value' => LongTermRentalStatusEnum::COMPLETE,
                'name' => __('long_term_rentals.approve_status_' . LongTermRentalStatusEnum::COMPLETE),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::CANCEL,
                'value' => LongTermRentalStatusEnum::CANCEL,
                'name' => __('long_term_rentals.approve_status_' . LongTermRentalStatusEnum::CANCEL),
            ]
        ]);
    }

    public function getTorLine($lt_rental_tor_id)
    {
        $tor_line = LongTermRentalTorLine::where('lt_rental_tor_id', $lt_rental_tor_id)->get();
        $tor_line->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
        });
        return $tor_line;
    }

    public function getAccessoriesByTorLineId($lt_rental_tor_line_id)
    {
        return LongTermRentalTorLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'lt_rental_tor_line_accessories.accessory_id')
            ->where('lt_rental_tor_line_accessories.lt_rental_tor_line_id', $lt_rental_tor_line_id)
            ->select(
                'lt_rental_tor_line_accessories.*',
                'lt_rental_tor_line_accessories.amount as amount_accessory',
                'lt_rental_tor_line_accessories.amount_per_car as amount_per_car_accessory',
                'accessories.name as accessory_text'
            )
            ->get();
    }

    public function getRentalTorLinesFromRentalId($long_term_rental_id)
    {
        return LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tor_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
                'lt_rentals.actual_delivery_date',
            )
            ->where('lt_rentals.id', $long_term_rental_id)
            ->orderBy('tor_id')
            ->get();
    }

    public function getRentalTorFromRentalId($long_term_rental_id)
    {
        $data = LongTermRentalTor::leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
                'lt_rental_tors.lt_rental_id',
                'lt_rentals.actual_delivery_date',
            )
            ->where('lt_rental_tors.lt_rental_id', $long_term_rental_id)
            ->orderBy('tor_id')->get();
        return $data;
    }

    public function getTorList($long_term_rental_id)
    {
        return LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
            ->select(
                'lt_rental_tor_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
                'lt_rentals.actual_delivery_date',
            )
            ->where('lt_rental_tors.lt_rental_id', $long_term_rental_id)
            ->orderBy('tor_id')
            ->get();
    }

    public function getTorLineCar($lt_rental_id)
    {
        $data = CarBrand::leftjoin('car_types', 'car_types.car_brand_id', '=', 'car_brands.id')
            ->leftjoin('car_classes', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('lt_rental_tor_lines', 'lt_rental_tor_lines.car_class_id', '=', 'car_classes.id')
            ->leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->where('lt_rental_tors.lt_rental_id', $lt_rental_id)
            ->select(
                'car_brands.id',
                'car_brands.name',
            )
            ->groupBy(
                'car_brands.id',
                'car_brands.name',
            )
            ->get();

        return $data;
    }


    static function getRentalLinesFromRentalId($long_term_rental_id)
    {
        return LongTermRentalLine::where('lt_rental_id', $long_term_rental_id)->get();
    }

    static function saveRentalLineMonths($lt_rental_line_id, $lt_rental_month_id)
    {
        $lt_line_month = new LongTermRentalLineMonth();
        $lt_line_month->lt_rental_line_id = $lt_rental_line_id;
        $lt_line_month->lt_rental_month_id = $lt_rental_month_id;
        $lt_line_month->save();
    }

    static function getRentalJobTypeList($with_trash = false)
    {
        $rental_type = LongTermRentalType::select('id', 'name')
            ->when($with_trash, function ($query) {
                $query->withTrashed();
            })
            ->get();
        return $rental_type;
    }

    static function getCarClassRentalLinesFromRentalId($long_term_rental_id)
    {
        $rental_lines = static::getRentalLinesFromRentalId($long_term_rental_id);
        $rental_lines->map(function ($item) {
            $item->name = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
        });
        return $rental_lines;
    }

    static function getCheckDeliveryList()
    {
        return collect([
            (object)[
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => 'พร้อมส่งมอบ',
            ],
            (object)[
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => 'ไม่พร้อมส่งมอบ',
            ],
            (object)[
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => 'เปลี่ยนรถ',
            ],
        ]);
    }
}
