<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accessories;
use App\Models\Creditor;

class AccessoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarOption.csv'), "r");

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
            $code = trim($col[1]);
            $name = trim($col[2]);
            $version = trim($col[3]);
            $price = trim($col[4]);
            $creditor_id = trim($col[5]);
            $status = trim($col[10]);
            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = Accessories::where('ref_id', $id)->exists();
            if (!$exists) {
                $creditor = Creditor::where('ref_id', $creditor_id)->first();
                $d = new Accessories();
                $d->name = $name;
                $d->code = $code;
                $d->version = $version;
                $d->price = floatval($price);
                $d->creditor_id = ($creditor ? $creditor->id : null);
                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
