<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpressWay;

class ExpressWaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBExpressWay.csv'), "r");

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
            $type = trim($col[1]);
            $name = trim($col[2]);
            $is_expressway = trim($col[3]);

            if (empty($name)) {
                continue;
            }

            $exists = ExpressWay::where('name', $name)->exists();
            if (!$exists) {
                $d = new ExpressWay();
                $d->type = $type;
                $d->name = $name;
                $d->is_expressway = boolval($is_expressway);
                $d->save();
            }
        }
    }
}
