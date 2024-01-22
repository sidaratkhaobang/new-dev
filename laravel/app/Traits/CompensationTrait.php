<?php

namespace App\Traits;
use App\Enums\CompensationStatusEnum;
use App\Enums\ComplaintTypeEnum;
use App\Enums\NegotiationResultEnum;
use App\Enums\NegotiationTypeEnum;
use App\Enums\SendingChannelEnum;
use App\Enums\TrafficTicketDocTypeEnum;
use App\Enums\TrafficTicketStatusEnum;
use App\Models\Compensation;

trait CompensationTrait
{
    public static function getStatusList()
    {
        return collect([
            (object) [
                'id' => CompensationStatusEnum::PENDING,
                'name' => __('compensations.status_' . CompensationStatusEnum::PENDING),
                'value' => CompensationStatusEnum::PENDING,
            ],
            (object) [
                'id' => CompensationStatusEnum::DATA_COLLECTION,
                'name' => __('compensations.status_' . CompensationStatusEnum::DATA_COLLECTION),
                'value' => CompensationStatusEnum::DATA_COLLECTION,
            ],
            (object) [
                'id' => CompensationStatusEnum::UNDER_NEGOTIATION,
                'name' => __('compensations.status_' . CompensationStatusEnum::UNDER_NEGOTIATION),
                'value' => CompensationStatusEnum::UNDER_NEGOTIATION,
            ],
            (object) [
                'id' => CompensationStatusEnum::PENDING_REVIEW,
                'name' => __('compensations.status_' . CompensationStatusEnum::PENDING_REVIEW),
                'value' => CompensationStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => CompensationStatusEnum::COMPLETE,
                'name' => __('compensations.status_' . CompensationStatusEnum::COMPLETE),
                'value' => CompensationStatusEnum::COMPLETE,
            ],
            (object) [
                'id' => CompensationStatusEnum::CANCEL_CLAIM,
                'name' => __('compensations.status_' . CompensationStatusEnum::CANCEL_CLAIM),
                'value' => CompensationStatusEnum::CANCEL_CLAIM,
            ],
            (object) [
                'id' => CompensationStatusEnum::REJECT,
                'name' => __('compensations.status_' . CompensationStatusEnum::REJECT),
                'value' => CompensationStatusEnum::REJECT,
            ],
        ]);
    }

    public static function getApproveStatusList()
    {
        return collect([
            (object) [
                'id' => CompensationStatusEnum::PENDING_REVIEW,
                'name' => __('compensations.status_' . CompensationStatusEnum::PENDING_REVIEW),
                'value' => CompensationStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => CompensationStatusEnum::COMPLETE,
                'name' => __('compensations.status_' . CompensationStatusEnum::COMPLETE),
                'value' => CompensationStatusEnum::COMPLETE,
            ],
            (object) [
                'id' => CompensationStatusEnum::REJECT,
                'name' => __('compensations.status_' . CompensationStatusEnum::REJECT),
                'value' => CompensationStatusEnum::REJECT,
            ],
        ]);
    }

    public static function getComplaintTypeList()
    {
        return collect([
            (object) [
                'id' =>  ComplaintTypeEnum::PARTY_NO_INSURANCE,
                'name' => __('compensations.complaint_type_' . ComplaintTypeEnum::PARTY_NO_INSURANCE),
                'value' => ComplaintTypeEnum::PARTY_NO_INSURANCE,
            ],
            (object) [
                'id' =>  ComplaintTypeEnum::PARTY_WITH_INSURANCE,
                'name' => __('compensations.complaint_type_' . ComplaintTypeEnum::PARTY_WITH_INSURANCE),
                'value' => ComplaintTypeEnum::PARTY_WITH_INSURANCE,
            ],
            (object) [
                'id' =>  ComplaintTypeEnum::OTHER,
                'name' => __('compensations.complaint_type_' . ComplaintTypeEnum::OTHER),
                'value' => ComplaintTypeEnum::OTHER,
            ],
        ]);
    }

    public static function getNegotiationTypeList()
    {
        return collect([
            (object) [
                'id' =>  NegotiationTypeEnum::INSURANCE,
                'name' => __('compensations.negotiation_type_' . NegotiationTypeEnum::INSURANCE),
                'value' => NegotiationTypeEnum::INSURANCE,
            ],
            (object) [
                'id' =>  NegotiationTypeEnum::OIC,
                'name' => __('compensations.negotiation_type_' . NegotiationTypeEnum::OIC),
                'value' => NegotiationTypeEnum::OIC,
            ],
        ]);
    }

    public static function getNegotiationResultList()
    {
        return collect([
            (object) [
                'id' =>  NegotiationResultEnum::END,
                'name' => __('compensations.negotiation_result_' . NegotiationResultEnum::END),
                'value' => NegotiationResultEnum::END,
            ],
            (object) [
                'id' =>  NegotiationResultEnum::NOT_END,
                'name' => __('compensations.negotiation_result_' . NegotiationResultEnum::NOT_END),
                'value' => NegotiationResultEnum::NOT_END,
            ],
            (object) [
                'id' =>  NegotiationResultEnum::SUE,
                'name' => __('compensations.negotiation_result_' . NegotiationResultEnum::SUE),
                'value' => NegotiationResultEnum::SUE,
            ],
        ]);
    }

    public static function getSendingChannelList()
    {
        return collect([
            (object) [
                'id' =>  SendingChannelEnum::BYSELF,
                'name' => __('compensations.sending_channel_' . SendingChannelEnum::BYSELF),
                'value' => SendingChannelEnum::BYSELF,
            ],
            (object) [
                'id' =>  SendingChannelEnum::EMAIL,
                'name' => __('compensations.sending_channel_' . SendingChannelEnum::EMAIL),
                'value' => SendingChannelEnum::EMAIL,
            ],
            (object) [
                'id' =>  SendingChannelEnum::POST,
                'name' => __('compensations.sending_channel_' . SendingChannelEnum::POST),
                'value' => SendingChannelEnum::POST,
            ],
            (object) [
                'id' =>  SendingChannelEnum::OTHER,
                'name' => __('compensations.sending_channel_' . SendingChannelEnum::OTHER),
                'value' => SendingChannelEnum::OTHER,
            ],
        ]);
    }

    public static function createCompensation($accident_id) 
    {
        $compensation = new Compensation();
        $compensation->accident_id = $accident_id;
        $compensation->worksheet_no = generate_worksheet_no(Compensation::class, false);
        $compensation->status = CompensationStatusEnum::DATA_COLLECTION;
        $compensation->save();
        return true;
    }
}
