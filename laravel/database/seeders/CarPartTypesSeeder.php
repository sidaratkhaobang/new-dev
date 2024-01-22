<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarPartType;
use App\Enums\CarPartTypeEnum;

class CarPartTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBStatus.csv'), "r");

        $mapping = [
            '1201' => CarPartTypeEnum::GEAR,
            '1202' => CarPartTypeEnum::DRIVE_SYSTEM,
            '1203' => CarPartTypeEnum::CAR_SEAT,
            '1204' => CarPartTypeEnum::SIDE_MIRROR,
            '1205' => CarPartTypeEnum::AIR_BAG,
            '1206' => CarPartTypeEnum::CENTRAL_LOCK,
            '1207' => CarPartTypeEnum::FRONT_BRAKE,
            '1208' => CarPartTypeEnum::REAR_BRAKE,
            '1209' => CarPartTypeEnum::ABS,
            '1210' => CarPartTypeEnum::ANTI_THIFT_SYSTEM,
        ];

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
            $name = trim($col[1]);
            $type = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            if (strcmp($type, 'DBCarPart Type') != 0) {
                continue;
            }

            $exists = CarPartType::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarPartType();
                $d->name = $name;
                $d->type = $mapping[$id];
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
