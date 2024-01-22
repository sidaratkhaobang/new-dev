<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralLedger;

class GeneralLedgersSeeder extends Seeder
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
                'name' => 'Spare parts (GL)',
                'account' => '130000110',
                'description' => 'บัญชีสินค้าคงเหลือ - อะไหล่ (Spare parts (GL))',
            ],
            [
                'name' => 'Input vat invoice pending',
                'account' => '149010020',
                'description' => 'ภาษีซื้อรอใบกำกับภาษี (Input vat invoice pending)',
            ],
            [
                'name' => 'Deposit - others',
                'account' => '149030900',
                'description' => 'เงินมัดจำระยะสั้น - อื่น (Deposit - others)',
            ],
        ];

        foreach ($datas as $data) {
            $exists = GeneralLedger::where('account', $data['account'])->exists();
            if (!$exists) {
                $d = new GeneralLedger();
                $d->name = $data['name'];
                $d->account = $data['account'];
                $d->description = $data['description'];
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
