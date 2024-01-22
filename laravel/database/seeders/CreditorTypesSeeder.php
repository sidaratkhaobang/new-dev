<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditorType;
use App\Enums\CreditorTypeEnum;

class CreditorTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $enums = [
            CreditorTypeEnum::LEASING => 'เจ้าหนี้เช่าซื้อ',
            CreditorTypeEnum::DEALER => 'เจ้าหนี้ Dealer',
            CreditorTypeEnum::ACCESSORIES => 'เจ้าหนี้อุปกรณ์เสริม',
            CreditorTypeEnum::SERVICE => 'เจ้าหนี้ศูนย์บริการ',
            CreditorTypeEnum::OTHER => 'เจ้าหนี้อื่นๆ',
        ];

        foreach ($enums as $type => $name) {
            $exists = CreditorType::where('type', $type)->exists();
            if (!$exists) {
                $d = new CreditorType();
                $d->name = $name;
                $d->type = $type;
                $d->save();
            }
        }
    }
}
