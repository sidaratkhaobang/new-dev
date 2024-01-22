<?php

namespace App\Traits;

use App\Enums\InvoiceStatusEnum;
use App\Models\Branch;

trait InvoiceTrait
{
    public static function getStatusList()
    {
        return collect([
            (object) [
                'id' => InvoiceStatusEnum::IN_PROCESS,
                'name' => __('invoices.status_' . InvoiceStatusEnum::IN_PROCESS),
                'value' => InvoiceStatusEnum::IN_PROCESS,
            ],
            (object) [
                'id' => InvoiceStatusEnum::COMPLETE,
                'name' => __('invoices.status_' . InvoiceStatusEnum::COMPLETE),
                'value' => InvoiceStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getBranchList()
    {
        $list = Branch::select('id', 'name')->get();
        return $list;
    }
}