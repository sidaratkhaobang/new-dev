<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarCategoryTransport;
use App\Models\CarCharacteristicTransport;
use Illuminate\Database\Seeder;
use App\Models\CarTire;

class CarCharacteristicTransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarCharacteristicTransport.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 3) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);
            $asset_no = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $exists = CarCharacteristicTransport::where('name', $name)->exists();
            if (!$exists) {
                $d = new CarCharacteristicTransport();
                $d->name = $name;
                $d->asset_no = $asset_no;
                $d->save();
            }
        }
    }
}
