<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarWiper;

class CarWipersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarRain.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);
            $version = trim($col[2]);
            $detail = trim($col[3]);
            $price = trim($col[4]);

            if (empty($name)) {
                continue;
            }

            $exists = CarWiper::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarWiper();
                $d->name = $name;
                $d->version = $version;
                $d->detail = $detail;
                $d->price = floatval($price);
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
