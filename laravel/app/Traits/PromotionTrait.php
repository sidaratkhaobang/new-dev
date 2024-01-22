<?php

namespace App\Traits;

use App\Classes\PromotionManagement;
use App\Models\PromotionCode;

trait PromotionTrait
{
    use DayTrait;

    static function getAvailablePromotionsByRental($rental, $s = null)
    {
        $pm = new PromotionManagement($rental);
        $promotions = $pm->getAvailablePromotions([
            's' => $s
        ]);
        return $promotions;
    }

    static function findAvailablePromotionsByRental($promotion_id, $rental, $s = null)
    {
        $promotions = self::getAvailablePromotionsByRental($rental, $s);
        $promotion = $promotions->filter(function ($_promotion) use ($promotion_id) {
            return (strcmp($_promotion->id, $promotion_id) == 0);
        })->first();
        return $promotion;
    }

    static function getAvailablePromotionsWithCouponsByRental($promotion_code, $rental, $s = null)
    {
        $pm = new PromotionManagement($rental);
        $promotions = $pm->getAvailablePromotionsWithCoupons($promotion_code, [
            's' => $s
        ]);
        return $promotions;
    }

    static function validatePromotionCouponCode($promotion_code, $customer_id)
    {
        $exists = PromotionCode::where('code', $promotion_code)
            ->where('buyer_id', $customer_id)
            ->where('is_sold', '1')
            ->where('is_used', '0')
            ->where('is_expired', '0')
            ->exists();
        return $exists;
    }

    static function validatePromotionVoucherCode($promotion_code, $customer_id)
    {
        $exists = PromotionCode::where('code', $promotion_code)
            ->where('buyer_id', $customer_id)
            ->where('is_sold', '1')
            ->where('is_used', '0')
            ->where('is_expired', '0')
            ->exists();
        return $exists;
    }
}
