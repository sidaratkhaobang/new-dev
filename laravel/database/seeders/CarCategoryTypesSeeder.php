<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarCategoryType;

class CarCategoryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarCategoryTypeName.csv'), "r");

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

            $exists = CarCategoryType::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarCategoryType();
                $d->name = $name;
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
