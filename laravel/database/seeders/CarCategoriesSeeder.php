<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarCategory;
use App\Models\CarCategoryType;

class CarCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarCategory.csv'), "r");

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
            $code = trim($col[1]);
            $name = trim($col[2]);
            $status = trim($col[4]);
            $type_name = trim($col[5]);

            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = CarCategory::where('ref_id', $id)->exists();
            if (!$exists) {
                $c = CarCategoryType::where('name', $type_name)->first();
                $d = new CarCategory();
                $d->code = $code;
                $d->name = $name;
                $d->car_category_type_id = $c ? $c->id : null;
                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
