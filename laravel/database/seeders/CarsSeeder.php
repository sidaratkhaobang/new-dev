<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Enums\RentalTypeEnum;
use App\Enums\CarEnum;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\Branch;

class CarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/cars.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 9) {
                continue;
            }
            $code = trim($col[0]);
            $license_plate = trim($col[1]);
            $engine_no = trim($col[2]);
            $chassis_no = trim($col[3]);
            /* $car_class_id = trim($col[4]);
            $car_color_id = trim($col[5]); */
            $car_class_name = trim($col[4]);
            $car_color_name = trim($col[5]);
            $rental_type = trim($col[6]);
            /* $branch_id = trim($col[7]); */
            $branch_name = trim($col[7]);
            $status = trim($col[8]);

            if (empty($code)) {
                continue;
            }

            $exists = Car::where('license_plate', $license_plate)->exists();
            if (!$exists) {
                $car_class = CarClass::where('name', $car_class_name)->first();
                $car_color = CarColor::where('name', $car_color_name)->first();
                $branch = Branch::where('name', $branch_name)->first();

                $d = new Car();
                $d->code = $code;
                $d->license_plate = $license_plate;
                $d->engine_no = $engine_no;
                $d->chassis_no = $chassis_no;
                $d->car_class_id = $car_class ? $car_class->id : null;
                $d->car_color_id = $car_color ? $car_color->id : null;
                $d->rental_type = RentalTypeEnum::SHORT;
                $d->branch_id = $branch ? $branch->id : null;
                $d->start_date = date('Y-m-d');
                $d->status = CarEnum::READY_TO_USE;
                $d->save();
            }
        }
    }
}
