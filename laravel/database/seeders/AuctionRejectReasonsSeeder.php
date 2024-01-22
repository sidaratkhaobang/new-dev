<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuctionRejectReason;

class AuctionRejectReasonsSeeder extends Seeder
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
                'name' => 'ส่งมอบไม่ทันตามกำหนด',
            ],
            [
                'name' => 'หน่วยงานประกาศยกเลิก',
            ],
            [
                'name' => 'ทำตาม TORไม่ได้',
            ],
            [
                'name' => 'ผู้ผลิตไม่มีรถ',
            ],
            [
                'name' => 'มีการแก้ไขTOR',
            ],
            [
                'name' => 'ไม่ได้เป็นผู้เสนอราคาต่ำสุด',
            ],
            [
                'name' => 'อื่นๆ',
            ],
        ];

        foreach ($datas as $data) {
            $exists = AuctionRejectReason::where('name', $data['name'])->exists();
            if (!$exists) {
                $d = new AuctionRejectReason();
                $d->name = $data['name'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
