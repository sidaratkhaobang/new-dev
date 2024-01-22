<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarGroup;

class CarGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            '0' => [
                'name' => 'ไม่ระบุ'
            ],
            '2' => [
                'name' => 'เก๋ง'
            ],
            '3' => [
                'name' => 'กระบะ'
            ],
            '4' => [
                'name' => 'ตู้'
            ]
        ];
        foreach ($list as $id => $data) {
            $exists = CarGroup::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new CarGroup();
                $d->name = $data['name'];
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
