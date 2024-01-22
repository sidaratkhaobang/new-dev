<?php

namespace App\Traits;

use App\Enums\TransferTypeEnum;

trait CarParkTrait 
{
    public static function getTransferType()
    {
        return collect([
            (object)[
                'id' => TransferTypeEnum::IN,
                'value' => TransferTypeEnum::IN,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::IN),
            ],
            (object)[
                'id' => TransferTypeEnum::OUT,
                'value' => TransferTypeEnum::OUT,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::OUT),
            ],
        ]);
    }
}