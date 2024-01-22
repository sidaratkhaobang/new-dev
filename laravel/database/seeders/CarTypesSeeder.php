<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarType;
use App\Models\CarBrand;
use App\Models\CarCategory;
use App\Models\CarGroup;

class CarTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarType.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 7) {
                continue;
            }
            $id = trim($col[0]);
            $brand_id = trim($col[1]);
            $code = trim($col[2]);
            $name = trim($col[3]);
            $car_category_id = trim($col[4]);
            $car_group_id = trim($col[5]);
            $status = trim($col[6]);
            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = CarType::where('ref_id', $id)->exists();
            if (!$exists) {
                $c1 = CarBrand::where('ref_id', $brand_id)->first();
                $c2 = CarCategory::where('ref_id', $car_category_id)->first();
                $c3 = CarGroup::where('ref_id', $car_group_id)->first();

                $d = new CarType();
                $d->name = $name;
                $d->code = $code;
                $d->car_brand_id = $c1 ? $c1->id : null;
                $d->car_category_id = $c2 ? $c2->id : null;
                $d->car_group_id = $c3 ? $c3->id : null;
                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
