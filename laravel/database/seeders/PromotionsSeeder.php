<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Enums\PromotionTypeEnum;
use App\Enums\DiscountTypeEnum;

class PromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/promotions.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 16) {
                continue;
            }
            $name = trim($col[0]);
            $code = trim($col[1]);
            $branch_name = trim($col[2]);
            $promotion_type = trim($col[3]);
            $_discount_type = trim($col[4]);
            $discount_amount = trim($col[6]);
            $start_travel_date = trim($col[13]);
            $end_travel_date = trim($col[14]);
            $start_date = trim($col[15]);
            $end_date = trim($col[16]);

            if (empty($name)) {
                continue;
            }

            if (strpos($code, '(Random') !== false) {
                $code = str_replace('(Random 7)', '', $code);
                $code = str_replace('(Random 8)', '', $code);
                $code = str_replace('(Random 5)', '', $code);
            }

            $discount_type = DiscountTypeEnum::PERCENT;
            if (strcmp($_discount_type, 'FREECARCLASS') == 0) {
                $discount_type = DiscountTypeEnum::FREE_CAR_CLASS;
            } else if (strcmp($_discount_type, 'AMOUNT') == 0) {
                $discount_type = DiscountTypeEnum::AMOUNT;
            }

            $start_travel_date = date_create_from_format('d/m/Y', $start_travel_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $start_travel_date)->getTimestamp()) : null;
            $end_travel_date = date_create_from_format('d/m/Y', $end_travel_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $end_travel_date)->getTimestamp()) : null;

            $start_date = date_create_from_format('d/m/Y', $start_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $start_date)->getTimestamp()) : null;
            $end_date = date_create_from_format('d/m/Y', $end_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $end_date)->getTimestamp()) : null;

            $exists = Promotion::where('name', $name)->exists();
            if (!$exists) {
                $d = new Promotion();
                $d->name = $name;
                $d->code = $code;
                $d->promotion_type = $promotion_type;
                $d->discount_type = $discount_type;
                $d->discount_amount = intval($discount_amount);
                $d->start_travel_date = $start_travel_date;
                $d->end_travel_date = $end_travel_date;
                $d->start_date = $start_date;
                $d->end_date = $end_date;
                $d->save();
            }
        }
    }
}
