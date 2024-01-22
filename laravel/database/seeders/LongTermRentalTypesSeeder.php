<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LongTermRentalType;

class LongTermRentalTypesSeeder extends Seeder
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
                'name' => 'เสนอราคาทั่วไป',
                'type' => 'NO_AUCTION',
                'job_type' => 'QUOTATION'
            ],
            [
                'name' => 'ขอราคาตั้งงบ',
                'type' => 'NO_AUCTION',
                'job_type' => 'BUDGET'
            ],
            [
                'name' => 'ยื่นซอง',
                'type' => 'AUCTION',
                'job_type' => 'AUCTION'
            ],
            [
                'name' => 'EBidding',
                'type' => 'AUCTION',
                'job_type' => 'EBIDDING'
            ],

        ];

        foreach ($datas as $data) {
            $exists = LongTermRentalType::where('name', $data['name'])->exists();
            if (!$exists) {
                $d = new LongTermRentalType();
                $d->name = $data['name'];
                $d->type = $data['type'];
                $d->job_type = $data['job_type'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            } else {
                $d = LongTermRentalType::where('name', $data['name'])->first();
                $d->name = $data['name'];
                $d->type = $data['type'];
                $d->job_type = $data['job_type'];
                $d->save();
            }
        }
    }
}
