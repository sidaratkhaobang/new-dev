<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\Accessories;
use App\Models\CarAccessory;
use App\Enums\LongTermRentalTypeAccessoryEnum;

class CarsAccessoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/cars_accessories.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $chassis_no = trim($col[0]);
            $accessory_name = trim($col[1]);

            if (empty($chassis_no) || empty($accessory_name)) {
                continue;
            }

            $car = Car::where('chassis_no', $chassis_no)->first();
            if ($car) {
                $accessory = Accessories::where('name', $accessory_name)->first();
                if ($accessory) {
                    $exists = CarAccessory::where('car_id', $car->id)->where('accessory_id', $accessory->id)->exists();
                    if (!$exists) {
                        $d = new CarAccessory();
                        $d->car_id = $car->id;
                        $d->accessory_id = $accessory->id;
                        $d->amount = 1;
                        $d->type_accessories = LongTermRentalTypeAccessoryEnum::ATTACHMENT;
                        $d->install_date = date('Y-m-d');
                        $d->save();
                    }
                } else {
                    $this->command->info('accessory not found : ' . $accessory_name);
                }
            } else {
                //$this->command->info('chassis_no not found : ' . $chassis_no);
            }
        }
    }
}
