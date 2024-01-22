<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchesSeeder extends Seeder
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
                'name' => 'Bangkok Branch',
                'code' => '0500',
                'cost_center' => '05ZR000100',
                'is_main' => true,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '1279',
            ],
            [
                'name' => 'Prapadaeng Branch',
                'code' => '0500',
                'cost_center' => '05A00AD340',
                'is_main' => false,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '02-117-3716-7',
            ],
            [
                'name' => 'Pattaya Branch',
                'code' => '0500',
                'cost_center' => '05A00AD350',
                'is_main' => false,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '064-586-7405',
            ],
            [
                'name' => 'Chiang Mai Branch',
                'code' => '0504',
                'cost_center' => '05A00AD330',
                'is_main' => false,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '052-064-595',
            ],
            [
                'name' => 'Chiang Rai Branch',
                'code' => '0502',
                'cost_center' => '05A00AD310',
                'is_main' => false,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '063-842-5506',
            ],
            [
                'name' => 'Phuket Branch',
                'code' => '0503',
                'cost_center' => '05A00AD320',
                'is_main' => false,
                'open_time' => '08:00:00',
                'close_time' => '20:00:00',
                'tel' => '076-646-565',
            ],
        ];

        foreach ($datas as $data) {
            $exists = Branch::where('name', $data['name'])->exists();
            if (!$exists) {
                $d = new Branch();
                $d->name = $data['name'];
                $d->code = $data['code'];
                $d->cost_center = $data['cost_center'];
                $d->is_main = $data['is_main'];
                $d->open_time = $data['open_time'];
                $d->close_time = $data['close_time'];
                $d->tel = $data['tel'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            } else {
                $d = Branch::where('name', $data['name'])->first();
                $d->code = $data['code'];
                $d->cost_center = $data['cost_center'];
                $d->save();
            }
        }
    }
}
