<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarClass;
use App\Models\CarPart;
use App\Models\CarType;
use App\Models\CarBattery;
use App\Models\CarTire;
use App\Models\CarWiper;

class CarClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBCarClass.csv'), "r");

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
            $name = trim($col[3]);
            $full_name = trim($col[4]);
            $description = trim($col[5]);

            $car_type_id = trim($col[6]);
            $engine_size = trim($col[7]);
            $manufacturing_year = trim($col[8]);

            $gear_id = trim($col[9]);
            $drive_system_id = trim($col[10]);
            $car_seat_id = trim($col[11]);
            $side_mirror_id = trim($col[12]);
            $air_bag_id = trim($col[13]);
            $central_lock_id = trim($col[14]);
            $front_brake_id = trim($col[15]);
            $rear_brake_id = trim($col[16]);
            $abs_id = trim($col[17]);
            $anti_thift_system_id = trim($col[18]);

            $oil_tank_capacity = trim($col[19]);
            $oil_type = trim($col[20]);

            $car_battery_id = trim($col[21]);
            $car_tire_id = trim($col[22]);
            $car_wiper_id = trim($col[23]);

            $status = trim($col[62]);
            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = CarClass::where('ref_id', $id)->exists();
            if (!$exists) {
                $a1 = CarType::where('ref_id', $car_type_id)->first();

                $c1 = CarPart::where('ref_id', $gear_id)->first();
                $c2 = CarPart::where('ref_id', $drive_system_id)->first();
                $c3 = CarPart::where('ref_id', $car_seat_id)->first();
                $c4 = CarPart::where('ref_id', $side_mirror_id)->first();
                $c5 = CarPart::where('ref_id', $air_bag_id)->first();
                $c6 = CarPart::where('ref_id', $central_lock_id)->first();
                $c7 = CarPart::where('ref_id', $front_brake_id)->first();
                $c8 = CarPart::where('ref_id', $rear_brake_id)->first();
                $c9 = CarPart::where('ref_id', $abs_id)->first();
                $c10 = CarPart::where('ref_id', $anti_thift_system_id)->first();

                $b1 = CarBattery::where('ref_id', $car_battery_id)->first();
                $b2 = CarTire::where('ref_id', $car_tire_id)->first();
                $b3 = CarWiper::where('ref_id', $car_wiper_id)->first();

                $d = new CarClass();
                $d->name = $name;
                $d->full_name = $full_name;
                $d->description = $description;
                $d->car_type_id = $a1 ? $a1->id : null;
                $d->engine_size = $engine_size;
                $d->manufacturing_year = $manufacturing_year;

                $d->gear_id = $c1 ? $c1->id : null;
                $d->drive_system_id = $c2 ? $c2->id : null;
                $d->car_seat_id = $c3 ? $c3->id : null;
                $d->side_mirror_id = $c4 ? $c4->id : null;
                $d->air_bag_id = $c5 ? $c5->id : null;
                $d->central_lock_id = $c6 ? $c6->id : null;
                $d->front_brake_id = $c7 ? $c7->id : null;
                $d->rear_brake_id = $c8 ? $c8->id : null;
                $d->abs_id = $c9 ? $c9->id : null;
                $d->anti_thift_system_id = $c10 ? $c10->id : null;

                $d->oil_tank_capacity = intval($oil_tank_capacity);
                $d->oil_type = $oil_type;

                $d->car_battery_id = $b1 ? $b1->id : null;
                $d->car_tire_id = $b2 ? $b2->id : null;
                $d->car_wiper_id = $b3 ? $b3->id : null;

                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
