<?php

namespace App\Traits;

use App\Enums\LitigationCaseStatusEnum;
use App\Enums\TrafficTicketDocTypeEnum;
use App\Enums\TrafficTicketInboundOutboundEnum;
use App\Enums\TrafficTicketNoticeChannelEnum;
use App\Enums\TrafficTicketReportStatusEnum;
use App\Enums\TrafficTicketSendPOStatusEnum;
use App\Enums\TrafficTicketStatusEnum;

trait TrafficTicketTrait
{
    public static function getTrafficTicketStatusList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketStatusEnum::GUITY_PENDING,
                'name' => __('traffic_tickets.status_' . TrafficTicketStatusEnum::GUITY_PENDING),
                'value' => TrafficTicketStatusEnum::GUITY_PENDING,
            ],
            (object) [
                'id' => TrafficTicketStatusEnum::SEND_POLICE_PENDING,
                'name' => __('traffic_tickets.status_' . TrafficTicketStatusEnum::SEND_POLICE_PENDING),
                'value' => TrafficTicketStatusEnum::SEND_POLICE_PENDING,
            ],
            (object) [
                'id' => TrafficTicketStatusEnum::PAYMENT_PENDING,
                'name' => __('traffic_tickets.status_' . TrafficTicketStatusEnum::PAYMENT_PENDING),
                'value' => TrafficTicketStatusEnum::PAYMENT_PENDING,
            ],
            (object) [
                'id' => TrafficTicketStatusEnum::COMPLETE,
                'name' => __('traffic_tickets.status_' . TrafficTicketStatusEnum::COMPLETE),
                'value' => TrafficTicketStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getDocumentTypeList()
    {
        $statuses = [
            TrafficTicketDocTypeEnum::TRAFFIC_TICKET,
            TrafficTicketDocTypeEnum::WARNING,
            TrafficTicketDocTypeEnum::VIOLATION_TRAFFIC_SIGN,
            TrafficTicketDocTypeEnum::TACHOMETER,
            TrafficTicketDocTypeEnum::LASER,
            TrafficTicketDocTypeEnum::EXPRESSWAY,
        ];

        return collect(array_map(function ($status) {
            return (object) [
                'id' => $status,
                'name' => __('traffic_tickets.doc_type_' . $status),
                'text' => __('traffic_tickets.doc_type_' . $status),
                'value' => $status,
            ];
        }, $statuses));
    }

    public static function getList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('traffic_tickets.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('traffic_tickets.status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public static function checkStateStatus($status, $init)
    {
        $status_collect = collect([
            TrafficTicketStatusEnum::DRAFT,
            TrafficTicketStatusEnum::GUITY_PENDING,
            TrafficTicketStatusEnum::SEND_POLICE_PENDING,
            TrafficTicketStatusEnum::PAYMENT_PENDING,
            TrafficTicketStatusEnum::COMPLETE,
        ]);
        $key = $status_collect->search($status);
        $key_init = $status_collect->search($init);
        return $key >= $key_init;
    }

    public static function getNoticeChannelList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketNoticeChannelEnum::EMAIL,
                'name' => __('traffic_tickets.notice_channel_' . TrafficTicketNoticeChannelEnum::EMAIL),
                'value' => TrafficTicketNoticeChannelEnum::EMAIL,
            ],
            (object) [
                'id' => TrafficTicketNoticeChannelEnum::PHONE,
                'name' => __('traffic_tickets.notice_channel_' . TrafficTicketNoticeChannelEnum::PHONE),
                'value' => TrafficTicketNoticeChannelEnum::PHONE,
            ],
        ]);
    }

    public static function getSendPOStatusList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketSendPOStatusEnum::PENDING,
                'name' => __('traffic_tickets.send_po_' . TrafficTicketSendPOStatusEnum::PENDING),
                'value' => TrafficTicketSendPOStatusEnum::PENDING,
            ],
            (object) [
                'id' => TrafficTicketSendPOStatusEnum::COMPLETE,
                'name' => __('traffic_tickets.send_po_' . TrafficTicketSendPOStatusEnum::COMPLETE),
                'value' => TrafficTicketSendPOStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getPaymentStatusList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketSendPOStatusEnum::PENDING,
                'name' => __('traffic_tickets.payment_status_' . TrafficTicketSendPOStatusEnum::PENDING),
                'value' => TrafficTicketSendPOStatusEnum::PENDING,
            ],
            (object) [
                'id' => TrafficTicketSendPOStatusEnum::COMPLETE,
                'name' => __('traffic_tickets.payment_status_' . TrafficTicketSendPOStatusEnum::COMPLETE),
                'value' => TrafficTicketSendPOStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getReportStatusList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketReportStatusEnum::PENDING,
                'name' => __('traffic_tickets.report_status_' . TrafficTicketReportStatusEnum::PENDING),
                'value' => TrafficTicketReportStatusEnum::PENDING,
            ],
            (object) [
                'id' => TrafficTicketReportStatusEnum::COMPLETE,
                'name' => __('traffic_tickets.report_status_' . TrafficTicketReportStatusEnum::COMPLETE),
                'value' => TrafficTicketReportStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getInboundOutboundList()
    {
        return collect([
            (object) [
                'id' => TrafficTicketInboundOutboundEnum::INBOUND,
                'name' => __('traffic_tickets.inbound_outbound_' . TrafficTicketInboundOutboundEnum::INBOUND),
                'value' => TrafficTicketInboundOutboundEnum::INBOUND,
            ],
            (object) [
                'id' => TrafficTicketInboundOutboundEnum::OUTBOUND,
                'name' => __('traffic_tickets.inbound_outbound_' . TrafficTicketInboundOutboundEnum::OUTBOUND),
                'value' => TrafficTicketInboundOutboundEnum::OUTBOUND,
            ],
        ]);
    }

    public static function getSlotList()
    {
        return collect(array_map(function ($val) {
            return (object) [
                'id' => $val,
                'name' => $val,
                'text' => $val,
                'value' => $val,
            ];
        }, range(1, 30)));
    }
}