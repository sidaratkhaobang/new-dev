<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClaimList;

class ClaimListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBClaimList.csv'), "r");


        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if (sizeof($col) < 2) {
                continue;
            }
            $name = trim($col[0]);
            $code = trim($col[1]);

            if (empty($code) || empty($name)) {
                continue;
            }

            $exists = ClaimList::where('name', $name)->exists();
            if (!$exists) {
                $d = new ClaimList();
                $d->code = $code;
                $d->name = $name;
                $d->save();
            }
        }
    }
}
