<?php

namespace App\Classes;

use App\Enums\PromotionTypeEnum;
use App\Models\Car;
use App\Models\Customer;
use App\Models\CustomerGroupRelation;
use App\Models\Promotion;
use App\Models\PromotionCarClass;
use App\Models\PromotionCode;
use App\Models\PromotionCodeUsage;
use App\Models\PromotionCustomerGroup;
use App\Models\PromotionProduct;
use App\Models\PromotionSale;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use DateTime;
use App\Traits\DayTrait;

class PromotionManagement
{
    use DayTrait;

    public $rental;
    public $rental_id;
    public $branch_id;
    public $pickup_date;
    public $return_date;
    public $subtotal;
    public $distance;
    public $error_msg;

    use RentalTrait;

    public function __construct($rental)
    {
        $this->rental = $rental;
        $this->rental_id = $rental->id;
        $this->branch_id = $rental->branch_id;
        $this->pickup_date = $rental->pickup_date;
        $this->return_date = $rental->return_date;
        $this->subtotal = floatval($rental->subtotal);
        $this->distance = floatval($rental->distance);
        $this->error_msg = null;
    }

    function getAvailablePromotions(array $optionals = [])
    {
        // extract optionals
        $s = isset($optionals['s']) ? trim($optionals['s']) : null;

        $query = $this->getRentalQuery();
        $promotions = $query->where('promotions.promotion_type', PromotionTypeEnum::PROMOTION)
            ->leftJoin('promotion_codes', 'promotion_codes.promotion_id', '=', 'promotions.id')
            ->where(function ($query) {
                $query->where('promotions.promotion_type', PromotionTypeEnum::PROMOTION);
                $query->whereNull('promotion_codes.code');
                $query->whereNull('promotion_codes.promotion_id');
            })
            ->where(function ($query) use ($s) {
                if (!empty($s)) {
                    $query->where('promotions.name', 'like', '%' . $s . '%');
                }
            })
            ->orderBy('promotions.name')
            ->get();
        return $promotions;
    }

    function find($promotion_id)
    {
        $query = $this->getRentalQuery();
        $promotion = $query->where('promotions.id', $promotion_id)->first();
        return $promotion;
    }

    function getAvailablePromotionsWithCoupons($promotion_code = null, array $optionals = [])
    {
        // extract optionals
        $s = isset($optionals['s']) ? trim($optionals['s']) : null;

        $query = $this->getRentalQuery();
        $promotions = $query->addSelect('promotion_codes.code as promotion_code')
            ->leftJoin('promotion_codes', 'promotion_codes.promotion_id', '=', 'promotions.id')
            ->where(function ($query) use ($promotion_code) {
                $query->where(function ($query2) {
                    $query2->where('promotions.promotion_type', PromotionTypeEnum::PROMOTION);
                    $query2->whereNull('promotion_codes.code');
                    $query2->whereNull('promotion_codes.promotion_id');
                    if (!empty($s)) {
                        $query2->where('promotions.name', 'like', '%' . $s . '%');
                    }
                });
                $query->orWhere(function ($query2) use ($promotion_code) {
                    $query2->where('promotion_codes.code', $promotion_code);
                    $query2->whereIn('promotions.promotion_type', [PromotionTypeEnum::PROMOTION, PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER]);
                    $query2->whereNotNull('promotion_codes.promotion_id');
                });
            })
            ->orderByDesc('promotion_codes.code')
            ->orderBy('promotions.name')
            ->get();
        return $promotions;
    }

    function getPromotionCodeList($code, $promotion_code_id = null, $promotion_ids = [], $s = null)
    {
        $promotion_code_list = PromotionCode::leftjoin('promotion_codes.promotion_id', '=', 'promotion_codes.promotion_id', '=', 'promotions.id')
            ->select(
                'promotion_codes.id as id',
                'promotion_codes.code as promotion_code_code',
                'promotion_codes.quota as promotion_code_quota',
                'promotion_codes.promotion_id as promotion_id',
            )
            ->where('promotion_codes.quota', '>', 0)
            ->where('promotion_codes.is_expired', false)
            ->where(function ($query) use ($s, $code, $promotion_code_id, $promotion_ids) {
                if (!empty($s)) {
                    $query->where('promotion_codes.code', 'like', '%' . $s . '%');
                }
                if (!empty($code)) {
                    $query->where('promotion_codes.code', $code);
                }
                if (!empty($promotion_code_id)) {
                    $query->where('promotion_codes.id', $promotion_code_id);
                }
                if (sizeof($promotion_ids) > 0) {
                    $query->whereIn('promotion_id', $promotion_ids);
                }
            })
            ->get();
        return $promotion_code_list;
    }

    private function getRentalQuery()
    {
        // prepare data
        $hours_diff = $this->getHoursDiff($this->pickup_date, $this->return_date);
        $days_diff = $this->getDaysDiff($this->pickup_date, $this->return_date);

        // start query
        $query = $this->getMainQuery();
        $query = $this->filterQueryByRentalData($query, $this->pickup_date, $this->subtotal, $hours_diff, $days_diff, $this->distance);
        return $query;
    }

    private function getMainQuery()
    {
        $queryBuilder = Promotion::select(
            'promotions.name',
            'promotions.id',
            'promotions.promotion_type',
            'promotions.discount_type',
            'promotions.start_date',
            'promotions.end_date',
            'promotions.discount_amount',
            'promotions.condition',
        )
            ->where('promotions.status', STATUS_ACTIVE)
            ->where(function ($query) {
                $query->where('promotions.branch_id', $this->branch_id);
                $query->orWhereNull('promotions.branch_id');
            });
        return $queryBuilder;
    }

    private function filterQueryByRentalData($query, $pickup_date, float $subtotal, int $hours_diff, int $days_diff, float $distance)
    {
        $query->where(function ($query) use ($pickup_date) {
            $query->whereNull('promotions.start_date');
            $query->orWhereDate('promotions.start_date', '<=', $pickup_date);
        })
            ->where(function ($query) use ($pickup_date) {
                $query->whereNull('promotions.end_date');
                $query->orWhereDate('promotions.end_date', '>=', $pickup_date);
            })
            ->where(function ($query) use ($subtotal) {
                $query->where('promotions.is_check_min_total', '0');
                $query->orWhere(function ($query2) use ($subtotal) {
                    $query2->where('promotions.is_check_min_total', '1');
                    $query2->where('promotions.min_total', '<=', $subtotal);
                });
            })
            ->where(function ($query) use ($hours_diff) {
                $query->where('promotions.is_check_min_hours', '0');
                $query->orWhere(function ($query2) use ($hours_diff) {
                    $query2->where('promotions.is_check_min_hours', '1');
                    $query2->where('promotions.min_hours', '<=', $hours_diff);
                });
            })
            ->where(function ($query) use ($days_diff) {
                $query->where('promotions.is_check_min_days', '0');
                $query->orWhere(function ($query2) use ($days_diff) {
                    $query2->where('promotions.is_check_min_days', '1');
                    $query2->where('promotions.min_days', '<=', $days_diff);
                });
            })
            ->where(function ($query) use ($distance) {
                $query->where('promotions.is_check_min_distance', '0');
                if ($distance > 0) {
                    $query->orWhere(function ($query2) use ($distance) {
                        $query2->where('promotions.is_check_min_distance', '1');
                        $query2->where('promotions.min_distance', '<=', $distance);
                    });
                }
            });
        return $query;
    }

    function getErrorMsg()
    {
        return $this->error_msg;
    }

    /* public function validateQuota($promotion_code_id)
    {
        $count_used_code = RentalTrait::getCountPromotionCodeUsed($promotion_code_id);
        $promotion_code = PromotionCode::find($promotion_code_id);
        if (intval($promotion_code->quota) < 0) {
            $this->error_msg = 'ส่วนลดนี้ไม่ได้จำกัดจำนวนไว้';
            return false;
        }
        if (intval($promotion_code->quota) <= $count_used_code) {
            $this->error_msg = 'ส่วนลดนี้ถูกใช้ไปครบจำกัดแล้ว';
            return false;
        }
        return true;
    } */
}
