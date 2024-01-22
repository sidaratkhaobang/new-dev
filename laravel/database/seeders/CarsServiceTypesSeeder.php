<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarServiceType;
use App\Models\Car;
use App\Models\ServiceType;

class CarsServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/cars_service_types.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $license_plate = trim($col[0]);
            $service_type_name = trim($col[1]);

            if (empty($license_plate) || empty($service_type_name)) {
                continue;
            }

            $car = Car::where('license_plate', $license_plate)->first();
            if ($car) {
                $service_type = ServiceType::where('name', $service_type_name)->first();
                if ($service_type) {
                    $exists = CarServiceType::where('car_id', $car->id)->where('service_type_id', $service_type->id)->exists();
                    if (!$exists) {
                        $d = new CarServiceType();
                        $d->car_id = $car->id;
                        $d->service_type_id = $service_type->id;
                        $d->save();
                    }
                } else {
                    $this->command->info('service_type not found : ' . $service_type_name);
                }
            } else {
                //$this->command->info('license_plate not found : ' . $license_plate);
            }
        }
    }
}
