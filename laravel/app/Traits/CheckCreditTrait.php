<?php

namespace App\Traits;

use App\Enums\CheckCreditStatusEnum;
use App\Enums\CustomerTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Models\CheckCredits;

trait CheckCreditTrait
{
    public static function getWorkSheetNumber()
    {
        $no_count = CheckCredits::count() + 1;
        return generateRecordNumber('CR', $no_count);
    }

    public static function getStatusList()
    {
        return collect([
            (object) [
                'id' => CheckCreditStatusEnum::DRAFT,
                'name' => __('contract.status_text_' . CheckCreditStatusEnum::DRAFT),
                'value' => CheckCreditStatusEnum::DRAFT,
            ],
            (object) [
                'id' => CheckCreditStatusEnum::PENDING_REVIEW,
                'name' => __('contract.status_text_' . CheckCreditStatusEnum::PENDING_REVIEW),
                'value' => CheckCreditStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => CheckCreditStatusEnum::CONFIRM,
                'name' => __('contract.status_text_' . CheckCreditStatusEnum::CONFIRM),
                'value' => CheckCreditStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => CheckCreditStatusEnum::REJECT,
                'name' => __('contract.status_text_' . CheckCreditStatusEnum::REJECT),
                'value' => CheckCreditStatusEnum::REJECT,
            ],
        ]);
    }

    public static function getListStatusRadio()
    {
        return collect([
            [
                'value' => CheckCreditStatusEnum::CONFIRM,
                'name' => __('lang.approve'),
            ],
            [
                'value' => CheckCreditStatusEnum::REJECT,
                'name' => __('lang.disapprove'),
            ],
        ]);
    }
}
