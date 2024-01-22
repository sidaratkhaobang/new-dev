<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WithholdingTax;

class WithholdingTaxesSeeder extends Seeder
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
                'name' => 'ไม่มีการหักภาษี ณ ที่จ่าย',
                'code' => '00',
                'amount' => 0,
            ],
            [
                'name' => '40(2) 3% ค่าธรรมเนียม นายหน้า ฯลฯ',
                'code' => '01',
                'amount' => 3,
            ],
            [
                'name' => '40(2)10% ค่าธรรมเนียม นายหน้า  ให้มูลนิธิ ฯ',
                'code' => '02',
                'amount' => 10,
            ],
        ];

        foreach ($datas as $data) {
            $exists = WithholdingTax::where('name', $data['name'])->exists();
            if (!$exists) {
                $d = new WithholdingTax();
                $d->name = $data['name'];
                $d->code = $data['code'];
                $d->amount = $data['amount'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
