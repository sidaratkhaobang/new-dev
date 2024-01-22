<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarCategoryTransport;
use App\Models\CarCharacteristic;
use App\Models\CarCharacteristicTransport;
use Illuminate\Database\Seeder;
use App\Models\CarTire;

class CarCharacteristicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarCharacteristic.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);

            if (empty($name)) {
                continue;
            }

            $exists = CarCharacteristic::where('name', $name)->exists();
            if (!$exists) {
                $d = new CarCharacteristic();
                $d->name = $name;
                $d->save();
            }
        }
    }
}
