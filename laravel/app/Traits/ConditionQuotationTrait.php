<?php

namespace App\Traits;
use App\Enums\ConditionGroupEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Models\ConditionGroup;
use App\Models\ServiceType;

trait ConditionQuotationTrait
{
    static function getConditionGroup($condition_group_enum)
    {
        return ConditionGroup::where('condition_group', $condition_group_enum)->first();
    }


    static function getLongTermRentalApprovalType()
    {
        return collect([
            (object)[
                'id' => 'AFFILIATED',
                'value' => LongTermRentalApprovalTypeEnum::AFFILIATED,
                'name' => __('condition_quotations.type_' . LongTermRentalApprovalTypeEnum::AFFILIATED),
            ],
            (object)[
                'id' => 'UNAFFILIATED',
                'value' => LongTermRentalApprovalTypeEnum::UNAFFILIATED,
                'name' => __('condition_quotations.type_' . LongTermRentalApprovalTypeEnum::UNAFFILIATED),
            ],
        ]);
    } 

    static function getShortTermServiceType()
    {
        return ServiceType::select('id', 'name', 'service_type')
            ->get()->map(function ($item) {
                $item->id = $item->service_type;
                $item->value = $item->service_type;
                return $item;
        });
    }
}