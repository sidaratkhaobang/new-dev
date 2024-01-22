<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarPart;
use App\Models\CarPartType;

class CarPartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarPart.csv'), "r");

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
            $type = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $exists = CarPart::where('ref_id', $id)->exists();
            if (!$exists) {
                $c = CarPartType::where('ref_id', $type)->first();

                $d = new CarPart();
                $d->name = $name;
                $d->car_part_type_id = $c ? $c->id : null;
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
