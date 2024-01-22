<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarBrand;

class CarBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarBrand.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 4) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);
            $code = trim($col[2]);
            $status = trim($col[3]);
            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = CarBrand::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarBrand();
                $d->name = $name;
                $d->code = $code;
                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
