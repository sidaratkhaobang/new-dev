<?php

namespace Database\Seeders;

use App\Enums\CalculateTypeEnum;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ServiceType;
use App\Models\Branch;
use App\Models\GLAccount;
use App\Models\ProductGLAccount;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/products_4.csv'), "r");

        $count = 0;
        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 26) {
                continue;
            }
            $name = trim($col[1]);
            //$sku = trim($col[1]);
            $service_type_name = trim($col[2]);
            $_calculate_type = trim($col[3]);
            $standard_price = trim($col[4]);
            $standard_price = str_replace(',', '', $standard_price);
            $branch_name = trim($col[5]);

            $booking_day_mon = trim($col[6]);
            $booking_day_tue = trim($col[7]);
            $booking_day_wed = trim($col[8]);
            $booking_day_thu = trim($col[9]);
            $booking_day_fri = trim($col[10]);
            $booking_day_sat = trim($col[11]);
            $booking_day_sun = trim($col[12]);

            $start_booking_time = trim($col[13]);
            $end_booking_time = trim($col[14]);
            $reserve_booking_duration = trim($col[15]);
            $start_date = trim($col[16]);
            $end_date = trim($col[17]);
            $is_used_application = trim($col[18]);
            $gl_account_name = trim($col[28]);

            if (empty($name)) {
                $this->command->info('name not found : ' . $count);
                continue;
            }

            $name = trim($name);

            if (!in_array($_calculate_type, [CalculateTypeEnum::HOURLY, CalculateTypeEnum::DAILY, CalculateTypeEnum::FIXED, CalculateTypeEnum::MONTHLY])) {
                $this->command->info('calculate_type not found : ' . $count);
                continue;
            }
            $calculate_type = $_calculate_type;

            /* $start_booking_time = empty($start_booking_time) ? date('H:i', strtotime($start_booking_time)) : null;
            $end_booking_time = empty($end_booking_time) ? date('H:i', strtotime($end_booking_time)) : null; */

            $start_date = date_create_from_format('d/m/Y', $start_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $start_date)->getTimestamp()) : null;
            $end_date = date_create_from_format('d/m/Y', $end_date) ? date('Y-m-d', date_create_from_format('d/m/Y', $end_date)->getTimestamp()) : null;

            $service_type = ServiceType::where('name', $service_type_name)->first();
            $branch = Branch::where('name', $branch_name)->first();

            if (empty($service_type)) {
                $this->command->info('service_type not found : ' . $service_type_name);
                continue;
            }

            if (empty($branch)) {
                $this->command->info('branch not found : ' . $branch_name);
                continue;
            }

            $is_used_application = boolval($is_used_application);

            $gl_account = GLAccount::where('name', $gl_account_name)->first();

            if (empty($gl_account)) {
                $this->command->info('gl_account not found : ' . $gl_account_name);
                continue;
            }

            $exists = Product::where('name', $name)->where('branch_id', $branch->id)->exists();
            if (!$exists) {
                $d = new Product();
                $d->name = $name;
                $d->sku = $name;
                $d->service_type_id = $service_type->id;
                $d->calculate_type = $calculate_type;
                $d->standard_price = floatval($standard_price);
                $d->branch_id = $branch->id;
                $d->booking_day_mon = true;
                $d->booking_day_tue = true;
                $d->booking_day_wed = true;
                $d->booking_day_thu = true;
                $d->booking_day_fri = true;
                $d->booking_day_sat = true;
                $d->booking_day_sun = true;
                $d->start_booking_time = $start_booking_time;
                $d->end_booking_time = $end_booking_time;
                $d->reserve_booking_duration = intval($reserve_booking_duration);
                $d->start_date = $start_date;
                $d->end_date = $end_date;
                $d->is_used_application = $is_used_application;
                $d->save();

                if ($gl_account) {
                    $exists2 = ProductGLAccount::where('product_id', $d->id)->where('gl_account_id', $gl_account->id)->exists();
                    if (!$exists2) {
                        ProductGLAccount::insert([
                            'product_id' => $d->id,
                            'gl_account_id' => $gl_account->id
                        ]);
                    }
                }
            }

            $count++;
        }
    }
}
