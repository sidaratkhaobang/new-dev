<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBPosition.csv'), "r");

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

            $exists = Position::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new Position();
                $d->name = $name;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
