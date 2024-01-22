<?php

namespace App\Console\Commands;

use App\Jobs\CheckExpiredCoupon;
use DateTime;
use App\Models\Promotion;
use Illuminate\Console\Command;
use App\Models\PromotionCode;
use App\Enums\RentalStatusEnum;

class ExpiredCoupon extends Command
{
    protected $signature = 'command:expired_coupon';
    protected $description = 'Expired Coupon';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $prev_day = date('Y-m-d', strtotime("-1 days"));
        $today = date('Y-m-d');

        $promotion_id_arr = Promotion::where('end_date', '<=', $prev_day)->pluck('id')->toArray();

        $promotion_code_id_arr = PromotionCode::whereIn('promotion_id', $promotion_id_arr)->where('is_sold', BOOL_TRUE)->where('is_used', BOOL_FALSE)->where('is_expired', BOOL_FALSE)->pluck('id')->toArray();

        $promotion_code_id_bill_arr = PromotionCode::leftJoin('rental_bills_promotion_codes', 'rental_bills_promotion_codes.promotion_code_id', '=', 'promotion_codes.id')
            ->leftJoin('rental_bills', 'rental_bills.id', '=', 'rental_bills_promotion_codes.rental_bill_id')
            ->where('rental_bills.status', RentalStatusEnum::PENDING)
            ->whereIn('promotion_codes.promotion_id', $promotion_id_arr)
            ->where('promotion_codes.is_sold', BOOL_TRUE)
            ->where('promotion_codes.is_used', BOOL_TRUE)
            ->where('promotion_codes.is_expired', BOOL_FALSE)
            ->pluck('promotion_codes.id')->toArray();

        $promotion_code_arr = array_merge($promotion_code_id_arr, $promotion_code_id_bill_arr);

        if ($promotion_code_arr) {
            PromotionCode::whereIn('id', $promotion_code_arr)->update([
                'is_expired' => '1',
            ]);

            CheckExpiredCoupon::dispatch($promotion_code_arr);
        }
    }
}
