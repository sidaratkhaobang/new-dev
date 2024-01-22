<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverWageRelation;
use App\Models\DriverWage;
use App\Models\Driver;
use App\Enums\AmountTypeEnum;

class DriverWageRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/driver_wages_relation.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 6) {
                continue;
            }
            $_driver_name = trim($col[1]);
            $driver_wage_name = trim($col[3]);
            $amount = trim($col[4]);
            $amount_type = trim($col[5]);

            if (empty($_driver_name) || empty($driver_wage_name)) {
                continue;
            }

            $_driver_name = str_replace('  ', ' ', $_driver_name);
            $_driver_name = str_replace('  ', ' ', $_driver_name);
            $driver_name = explode(' ', $_driver_name);
            if (sizeof($driver_name) != 2) {
                $driver_name = explode(' ณ ', $_driver_name);
                if (sizeof($driver_name) != 2) {
                    continue;
                } else {
                    $driver_name[1] = 'ณ ' . $driver_name[1];
                }
            }
            $driver_name = $driver_name[0] . ' ' . $driver_name[1];

            $driver = Driver::where('name', $driver_name)->first();
            if (empty($driver)) {
                $this->command->warn('driver not found : ' . $driver_name);
                continue;
            }

            $driver_wage = DriverWage::where('name', $driver_wage_name)->first();
            if (empty($driver_wage)) {
                $this->command->warn('driver_wage not found : ' . $driver_wage_name);
                continue;
            }

            if (!in_array($amount_type, [AmountTypeEnum::BAHT, AmountTypeEnum::PERCENT])) {
                $this->command->warn('amount_type not found');
                $amount_type = AmountTypeEnum::BAHT;
            }

            $exists = DriverWageRelation::where('driver_id', $driver->id)->where('driver_wage_id', $driver_wage->id)->exists();
            if (!$exists) {
                $d = new DriverWageRelation();
                $d->driver_id = $driver->id;
                $d->driver_wage_id = $driver_wage->id;
                $d->amount = intval($amount);
                $d->amount_type = $amount_type;
                $d->save();
            }
        }
    }
}
