<?php

namespace Database\Seeders;

use App\Enums\PettyCashTypeEnum;
use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'ค่าน้ำมัน',
                'type' => PettyCashTypeEnum::DRIVING_JOB,
            ],
            [
                'name' => 'ค่าที่จอดรถ',
                'type' => PettyCashTypeEnum::DRIVING_JOB,
            ],
            [
                'name' => 'ค่าทางด่วน',
                'type' => PettyCashTypeEnum::DRIVING_JOB,
            ],
            [
                'name' => 'ค่าอื่น ๆ',
                'type' => PettyCashTypeEnum::DRIVING_JOB,
            ],
        ];

        foreach ($data as $item) {
            $exists = ExpenseType::where('name', $item['name'])->exists();
            if (!$exists) {
                $d = new ExpenseType();
                $d->petty_cash_type = $item['type'];
                $d->name = $item['name'];
                $d->save();
            }
        }
    }
}