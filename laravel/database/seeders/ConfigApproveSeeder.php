<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfigApprove;
use App\Enums\ConfigApproveTypeEnum;

class ConfigApproveSeeder extends Seeder
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
                'type' => ConfigApproveTypeEnum::PURCHASE_REQUISITION,
            ],
            [
                'type' => ConfigApproveTypeEnum::PURCHASE_ORDER,
            ],
            [
                'type' => ConfigApproveTypeEnum::LT_SPEC_ACCESSORY,
            ],
            [
                'type' => ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED,
            ],
            [
                'type' => ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED,
            ],
            [
                'type' => ConfigApproveTypeEnum::EQUIPMENT_ORDER,
            ],
            [
                'type' => ConfigApproveTypeEnum::REPLACEMENT_CAR,
            ],
            [
                'type' => ConfigApproveTypeEnum::BORROW_CAR,
            ],
            [
                'type' => ConfigApproveTypeEnum::REPAIR_ORDER,
            ],
            [
                'type' => ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER,
            ],
            [
                'type' => ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET,
            ],
            [
                'type' => ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL,
            ],
            [
                'type' => ConfigApproveTypeEnum::SELLING_PRICE,
            ],
            [
                'type' => ConfigApproveTypeEnum::FINANCE_REQUEST,
            ],
            [
                'type' => ConfigApproveTypeEnum::LITIGATION,
            ],
            [
                'type' => ConfigApproveTypeEnum::COMPENSATION,
            ],
        ];

        foreach ($datas as $data) {
            $exists = ConfigApprove::where('type', $data['type'])->exists();
            if (!$exists) {
                $d = new ConfigApprove();
                $d->type = $data['type'];
                $d->save();
            }
        }
    }
}
