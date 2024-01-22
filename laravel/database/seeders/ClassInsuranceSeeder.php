<?php

namespace Database\Seeders;

use App\Models\CarClass;
use Illuminate\Database\Seeder;

class ClassInsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBClassInsurance.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }

            $no = trim($col[0]);
            $brand = trim($col[1]);
            $class_insurance = trim($col[2]);
            $car_class = trim($col[3]);
            $cc = trim($col[4]);

            if (empty($class_insurance)) {
                continue;
            }

            $full_name = $brand . ' ' . $car_class;
            $car_class = CarClass::where('full_name', $full_name)->first();
            if ($car_class) {
                $car_class->model_insurance = $class_insurance;
                $car_class->save();
            }
        }
    }
}