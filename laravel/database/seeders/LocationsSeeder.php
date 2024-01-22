<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Amphure;
use App\Models\LocationGroup;
use App\Models\Location;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->importFromProvince();
        $this->importFromFile();
    }

    private function importFromProvince()
    {
        $provinces = Province::all();
        foreach ($provinces as $province) {
            $exists = LocationGroup::where('name', $province->name_th)->exists();
            if (!$exists) {
                $d = new LocationGroup();
                $d->name = $province->name_th;
                $d->save();

                $exists2 = Location::where('name', $province->name_th)->exists();
                if (!$exists2) {
                    $d2 = new Location();
                    $d2->name = $province->name_th;
                    $d2->location_group_id = $d->id;
                    $d2->province_id = $province->id;
                    $d2->save();
                }
            }
        }

        $amphures = Amphure::select('amphures.id', 'amphures.name_th', 'provinces.id as province_id', 'provinces.name_th as province_name')
            ->leftJoin('provinces', 'provinces.id', '=', 'amphures.province_id')
            ->get();
        foreach ($amphures as $amphure) {
            $exists2 = Location::where('name', $amphure->name_th)->exists();
            if (!$exists2) {
                $group = LocationGroup::where('name', $amphure->province_name)->first();
                $d2 = new Location();
                $d2->name = $amphure->name_th;
                $d2->location_group_id = $group ? $group->id : null;
                $d2->province_id = $amphure->province_id;
                $d2->save();
            }
        }
    }

    private function importFromFile()
    {
        $handle = fopen(storage_path('init/database/locations.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 8) {
                continue;
            }
            $name = trim($col[0]);
            $group_name = trim($col[2]);
            $province_name = trim($col[3]);
            $can_transportation_car = trim($col[6]);
            $can_transportation_boat = trim($col[7]);

            if (empty($name)) {
                continue;
            }

            $can_transportation_car = boolval($can_transportation_car);
            $can_transportation_boat = boolval($can_transportation_boat);

            $group_id = null;
            $group = LocationGroup::where('name', $group_name)->first();
            if (!$group) {
                $d = new LocationGroup();
                $d->name = $group_name;
                $d->save();

                $group_id = $d->id;
            } else {
                $group_id = $group->id;
            }

            $exists = Location::where('name', $name)->exists();
            if (!$exists) {
                $province = Province::where('name_th', $province_name)->first();
                $d = new Location();
                $d->name = $name;
                $d->location_group_id = $group_id;
                $d->province_id = $province ? $province->id : null;
                $d->save();
            }
        }
    }
}
