<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarStatus;

class CarStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/CarStatus.csv'), "r");

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
            $code = trim($col[1]);
            $name = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $exists = CarStatus::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarStatus();
                $d->name = $name;
                $d->code = $code;
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
