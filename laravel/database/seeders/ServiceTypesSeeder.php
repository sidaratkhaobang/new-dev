<?php

namespace Database\Seeders;

use App\Enums\TransportationTypeEnum;
use App\Enums\ServiceTypeEnum;
use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name' => 'เช่าขับเอง',
                'service_type' => ServiceTypeEnum::SELF_DRIVE,
                'transportation_type' => TransportationTypeEnum::CAR,
            ],
            [
                'name' => 'มินิโค้ช',
                'service_type' => ServiceTypeEnum::MINI_COACH,
                'transportation_type' => TransportationTypeEnum::CAR,
            ],
            [
                'name' => 'บัส',
                'service_type' => ServiceTypeEnum::BUS,
                'transportation_type' => TransportationTypeEnum::CAR,
            ],
            [
                'name' => 'เรือ',
                'service_type' => ServiceTypeEnum::BOAT,
                'transportation_type' => TransportationTypeEnum::BOAT,
            ],
            [
                'name' => 'ลิมูซีน',
                'service_type' => ServiceTypeEnum::LIMOUSINE,
                'transportation_type' => TransportationTypeEnum::CAR,
            ],
            [
                'name' => 'รถสไลด์',
                'service_type' => ServiceTypeEnum::SLIDE_FORKLIFT,
                'transportation_type' => TransportationTypeEnum::CAR,
            ],
        ];

        foreach ($datas as $data) {
            $exists = ServiceType::where('name', $data['name'])->exists();
            if (!$exists) {
                $d = new ServiceType();
                $d->name = $data['name'];
                $d->service_type = $data['service_type'];
                $d->transportation_type = $data['transportation_type'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
