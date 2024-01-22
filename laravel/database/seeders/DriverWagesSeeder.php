<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverWageCategory;
use App\Models\DriverWage;
use App\Enums\WageCalDay;
use App\Enums\WageCalTime;
use App\Enums\WageCalType;
use App\Models\ServiceType;

class DriverWagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->driverWageCategories();
        $this->driverWages();
    }

    function driverWageCategories()
    {
        $handle = fopen(storage_path('init/database/driver_wages.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 3) {
                continue;
            }
            $name = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $exists = DriverWageCategory::where('name', $name)->exists();
            if (!$exists) {
                $d = new DriverWageCategory();
                $d->name = $name;
                $d->save();
            }
        }
        fclose($handle);
    }

    function driverWages()
    {
        $handle = fopen(storage_path('init/database/driver_wages.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 9) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);
            $driver_wage_category_name = trim($col[2]);
            $is_standard = trim($col[3]);
            $wage_cal_type = trim($col[4]);
            $wage_cal_day = trim($col[5]);
            $wage_cal_time = trim($col[6]);
            $is_special_wage = trim($col[7]);
            $service_type_name = trim($col[8]);

            if (empty($name)) {
                continue;
            }

            if (!in_array($wage_cal_type, [WageCalType::PER_DAY, WageCalType::PER_HOUR, WageCalType::PER_MONTH, WageCalType::PER_TRIP])) {
                $this->command->warn('wage_cal_type not found');
                $wage_cal_type = WageCalType::PER_TRIP;
            }
            if (!in_array($wage_cal_day, [WageCalDay::ALL, WageCalDay::WORK_DAY, WageCalDay::HOLIDAY])) {
                $this->command->warn('wage_cal_day not found');
                $wage_cal_day = WageCalDay::ALL;
            }
            if (!in_array($wage_cal_time, [WageCalTime::ALL, WageCalTime::WORK_TIME, WageCalTime::OUT_OF_WORK_TIME])) {
                $this->command->warn('wage_cal_time not found');
                $wage_cal_time = WageCalTime::ALL;
            }


            $driver_wage_category = DriverWageCategory::where('name', $driver_wage_category_name)->first();
            if (empty($driver_wage_category)) {
                $this->command->warn('driver_wage_category not found : ' . $driver_wage_category_name);
            }

            $service_type = ServiceType::where('name', $service_type_name)->first();
            if (empty($service_type)) {
                $this->command->warn('driver_wage_category not found : ' . $service_type_name);
            }

            $d = DriverWage::where('name', $name)->first();
            if (!$d) {
                //$this->command->info('new driver_wage : ' . $name);
                $d = new DriverWage();
                $d->name = $name;
            }

            $d->driver_wage_category_id = $driver_wage_category ? $driver_wage_category->id : null;
            $d->is_standard = boolval($is_standard);
            $d->wage_cal_type = $wage_cal_type;
            $d->wage_cal_day = $wage_cal_day;
            $d->wage_cal_time = $wage_cal_time;
            $d->is_special_wage = boolval($is_special_wage);
            $d->service_type_id = $service_type ? $service_type->id : null;
            $d->status = STATUS_ACTIVE;
            $d->save();
        }
        fclose($handle);
    }
}
