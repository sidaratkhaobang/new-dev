<?php

namespace App\Traits;
use App\Enums\LitigationCaseEnum;
use App\Enums\LitigationCaseStatusEnum;
use App\Enums\LitigationCaseTypeEnum;
use App\Enums\LitigationLocationEnum;
use App\Enums\LitigationPaymentChannel;
use App\Enums\LitigationStatusEnum;
use App\Enums\LitigationTLSTypeEnum;
use App\Models\Bank;


trait LitigationTrait
{
    public static function getTLSTypeList()
    {
        return collect([
            (object) [
                'id' => LitigationTLSTypeEnum::PLAINTIFF,
                'name' => __('litigations.tls_type_' . LitigationTLSTypeEnum::PLAINTIFF),
                'value' => LitigationTLSTypeEnum::PLAINTIFF,
            ],
            (object) [
                'id' => LitigationTLSTypeEnum::DEFENDANT,
                'name' => __('litigations.tls_type_' . LitigationTLSTypeEnum::DEFENDANT),
                'value' => LitigationTLSTypeEnum::DEFENDANT,
            ],
        ]);
    }


    public static function getCaseTypeList()
    {
        return collect([
            (object) [
                'id' => LitigationCaseTypeEnum::CIVIL_CASE,
                'name' => __('litigations.case_type_' . LitigationCaseTypeEnum::CIVIL_CASE),
                'value' => LitigationCaseTypeEnum::CIVIL_CASE,
            ],
            (object) [
                'id' => LitigationCaseTypeEnum::CRIMINAL_CASE,
                'name' => __('litigations.case_type_' . LitigationCaseTypeEnum::CRIMINAL_CASE),
                'value' => LitigationCaseTypeEnum::CRIMINAL_CASE,
            ],
        ]);
    }

    public static function getLitigationStatusList()
    {
        return collect([
            (object) [
                'id' => LitigationStatusEnum::PENDING,
                'name' => __('litigations.status_' . LitigationStatusEnum::PENDING),
                'value' => LitigationStatusEnum::PENDING,
            ],
            (object) [
                'id' => LitigationStatusEnum::IN_PROCESS,
                'name' => __('litigations.status_' . LitigationStatusEnum::IN_PROCESS),
                'value' => LitigationStatusEnum::IN_PROCESS,
            ],
            (object) [
                'id' => LitigationStatusEnum::FOLLOW,
                'name' => __('litigations.status_' . LitigationStatusEnum::FOLLOW),
                'value' => LitigationStatusEnum::FOLLOW,
            ],
            (object) [
                'id' => LitigationStatusEnum::COMPLETE,
                'name' => __('litigations.status_' . LitigationStatusEnum::COMPLETE),
                'value' => LitigationStatusEnum::COMPLETE,
            ],
        ]);
    }

    public static function getLitigationApproveStatusList()
    {
        return collect([
            (object) [
                'id' => LitigationStatusEnum::PENDING_REVIEW,
                'name' => __('litigations.status_' . LitigationStatusEnum::PENDING_REVIEW),
                'value' => LitigationStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => LitigationStatusEnum::CONFIRM,
                'name' => __('litigations.status_' . LitigationStatusEnum::CONFIRM),
                'value' => LitigationStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => LitigationStatusEnum::REJECT,
                'name' => __('litigations.status_' . LitigationStatusEnum::REJECT),
                'value' => LitigationStatusEnum::REJECT,
            ],
        ]);
    }

    public static function getInitCourtFollowStatusList()
    {
        return collect([
            (object) [
                'id' => LitigationCaseStatusEnum::PREPARE,
                'name' => __('litigations.case_status_' . LitigationCaseStatusEnum::PREPARE),
                'text' => __('litigations.case_status_' . LitigationCaseStatusEnum::PREPARE),
                'value' => LitigationCaseStatusEnum::PREPARE,
            ],
            (object) [
                'id' => LitigationCaseStatusEnum::MEDIATION,
                'name' => __('litigations.case_status_' . LitigationCaseStatusEnum::MEDIATION),
                'text' => __('litigations.case_status_' . LitigationCaseStatusEnum::MEDIATION),
                'value' => LitigationCaseStatusEnum::MEDIATION,
            ],
        ]);
    }
    
    public static function getCourtFollowStatusList()
    {
        $statuses = [
            LitigationCaseStatusEnum::PREPARE,
            LitigationCaseStatusEnum::MEDIATION,
            LitigationCaseStatusEnum::INVESTIGATE,
            LitigationCaseStatusEnum::VERDICT_HEARING,
            LitigationCaseStatusEnum::BETWEEN_APPEALING,
            LitigationCaseStatusEnum::BETWEEN_SUPREME_COURT,
            LitigationCaseStatusEnum::WEALTH_INVESTIGATE,
            LitigationCaseStatusEnum::CASE_FINAL,
        ];
    
        return collect(array_map(function ($status) {
            return (object) [
                'id' => $status,
                'name' => __('litigations.case_status_' . $status),
                'text' => __('litigations.case_status_' . $status),
                'value' => $status,
            ];
        }, $statuses));
    }

    public static function getInitPoliceFollowStatusList()
    {
        return collect([
            (object) [
                'id' => LitigationCaseStatusEnum::DAILY_RECORD,
                'name' => __('litigations.case_status_' . LitigationCaseStatusEnum::DAILY_RECORD),
                'text' => __('litigations.case_status_' . LitigationCaseStatusEnum::DAILY_RECORD),
                'value' => LitigationCaseStatusEnum::DAILY_RECORD,
            ],
            (object) [
                'id' => LitigationCaseStatusEnum::REPORT,
                'name' => __('litigations.case_status_' . LitigationCaseStatusEnum::REPORT),
                'text' => __('litigations.case_status_' . LitigationCaseStatusEnum::REPORT),
                'value' => LitigationCaseStatusEnum::REPORT,
            ],
        ]);
    }

    public static function getPoliceFollowStatusList()
    {
        $statuses = [
            LitigationCaseStatusEnum::DAILY_RECORD,
            LitigationCaseStatusEnum::REPORT,
            LitigationCaseStatusEnum::SUMMON,
            LitigationCaseStatusEnum::ARREST_WARRANT,
            LitigationCaseStatusEnum::SUE,
        ];
    
        return collect(array_map(function ($status) {
            return (object) [
                'id' => $status,
                'name' => __('litigations.case_status_' . $status),
                'text' => __('litigations.case_status_' . $status),
                'value' => $status,
            ];
        }, $statuses));
    }

    public static function getCaseNameList()
    {
        $statuses = [
            LitigationCaseEnum::DEBTOR_CASE,
            LitigationCaseEnum::LOST_CAR_CASE,
            LitigationCaseEnum::PARTNER_CASE,
            LitigationCaseEnum::CHECK_CASE,
            LitigationCaseEnum::OTHER,
        ];
    
        return collect(array_map(function ($status) {
            return (object) [
                'id' => $status,
                'name' => __('litigations.case_name_' . $status),
                'text' => __('litigations.case_name_' . $status),
                'value' => $status,
            ];
        }, $statuses));
    }

    public static function getLocationCaseList()
    {
        $statuses = [
            LitigationLocationEnum::POLICE_STATION,
            LitigationLocationEnum::COURT,
        ];
    
        return collect(array_map(function ($status) {
            return (object) [
                'id' => $status,
                'name' => __('litigations.location_name_' . $status),
                'text' => __('litigations.location_name_' . $status),
                'value' => $status,
            ];
        }, $statuses));
    }

    public static function getPaymentChannelList()
    {
        return collect([
            (object) [
                'id' => LitigationPaymentChannel::TRANSFER,
                'name' => __('litigations.payment_channel_' . LitigationPaymentChannel::TRANSFER),
                'value' => LitigationPaymentChannel::TRANSFER,
            ],
            (object) [
                'id' => LitigationPaymentChannel::CHECK,
                'name' => __('litigations.payment_channel_' . LitigationPaymentChannel::CHECK),
                'value' => LitigationPaymentChannel::CHECK,
            ],
        ]);
    }

    public static function getBankList()
    {
        $bank_list = Bank::where('status', STATUS_ACTIVE)->get()
        ->map(function($item) {
            $item->name = $item->key;
            return $item;
        });
        return $bank_list;
    }
}
