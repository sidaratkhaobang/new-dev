<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Branch;
use App\Models\BranchLocation;

class BranchLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/branch_locations.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $branch_name = trim($col[0]);
            $location_name = trim($col[1]);

            if (empty($branch_name) || empty($location_name)) {
                continue;
            }

            $branch = Branch::where('name', $branch_name)->first();
            $location = Location::where('name', $location_name)->first();

            if (empty($branch) || empty($location)) {
                continue;
            }

            $exists = BranchLocation::where('branch_id', $branch->id)->where('location_id', $location->id)->exists();
            if (!$exists) {
                $d = new BranchLocation();
                $d->branch_id = $branch->id;
                $d->location_id = $location->id;
                $d->can_origin = true;
                $d->can_stopover = true;
                $d->can_destination = true;
                $d->save();
            }
        }
    }
}
