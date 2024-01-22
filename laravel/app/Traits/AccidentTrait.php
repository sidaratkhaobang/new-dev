<?php

namespace App\Traits;

use App\Enums\AccidentRepairFollowUpStatusEnum;
use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentSlideEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\AccidentTypeEnum;
use App\Enums\CaseAccidentEnum;
use App\Enums\ClaimantAccidentEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\RepairClaimEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\ResponsibleEnum;
use App\Enums\RightsEnum;
use App\Enums\WoundType;
use App\Enums\ZoneEnum;
use App\Models\Car;
use App\Models\Province;
use Illuminate\Support\Facades\DB;

trait AccidentTrait
{
    public static function getAccidentTypeList()
    {
        return collect([
            (object) [
                'id' => AccidentTypeEnum::SOFT_CAR,
                'name' => __('accident_informs.accident_type_' . AccidentTypeEnum::SOFT_CAR),
                'value' => AccidentTypeEnum::SOFT_CAR,
            ],
            (object) [
                'id' => AccidentTypeEnum::HARD_CAR,
                'name' => __('accident_informs.accident_type_' . AccidentTypeEnum::HARD_CAR),
                'value' => AccidentTypeEnum::HARD_CAR,
            ],
        ]);
    }

    public static function getAccidentTypeIndexList()
    {
        return collect([
            (object) [
                'id' => AccidentTypeEnum::SOFT_CAR,
                'name' => __('accident_informs.accident_type_index_' . AccidentTypeEnum::SOFT_CAR),
                'value' => AccidentTypeEnum::SOFT_CAR,
            ],
            (object) [
                'id' => AccidentTypeEnum::HARD_CAR,
                'name' => __('accident_informs.accident_type_index_' . AccidentTypeEnum::HARD_CAR),
                'value' => AccidentTypeEnum::HARD_CAR,
            ],
        ]);
    }

    public static function getCliamTypeList()
    {
        return collect([
            (object) [
                'id' => ClaimTypeEnum::FRESH_CLAIM,
                'name' => __('accident_informs.claim_type_' . ClaimTypeEnum::FRESH_CLAIM),
                'value' => ClaimTypeEnum::FRESH_CLAIM,
            ],
            (object) [
                'id' => ClaimTypeEnum::DRY_CLAIM,
                'name' => __('accident_informs.claim_type_' . ClaimTypeEnum::DRY_CLAIM),
                'value' => ClaimTypeEnum::DRY_CLAIM,
            ],
            (object) [
                'id' => ClaimTypeEnum::ACCLIMATE,
                'name' => __('accident_informs.claim_type_' . ClaimTypeEnum::ACCLIMATE),
                'value' => ClaimTypeEnum::ACCLIMATE,
            ],
        ]);
    }

    public static function getCliamantList()
    {
        return collect([
            (object) [
                'id' => ClaimantAccidentEnum::TENANT,
                'name' => __('accident_informs.claimant_' . ClaimantAccidentEnum::TENANT),
                'value' => ClaimantAccidentEnum::TENANT,
            ],
            (object) [
                'id' => ClaimantAccidentEnum::GARAGE,
                'name' => __('accident_informs.claimant_' . ClaimantAccidentEnum::GARAGE),
                'value' => ClaimantAccidentEnum::GARAGE,
            ],
            (object) [
                'id' => ClaimantAccidentEnum::TLS,
                'name' => __('accident_informs.claimant_' . ClaimantAccidentEnum::TLS),
                'value' => ClaimantAccidentEnum::TLS,
            ],
        ]);
    }

    public static function getMistakeTypeList()
    {
        return collect([
            (object) [
                'id' => MistakeTypeEnum::FALSE,
                'name' => __('accident_informs.mistake_' . MistakeTypeEnum::FALSE),
                'value' => MistakeTypeEnum::FALSE,
            ],
            (object) [
                'id' => MistakeTypeEnum::TRUE,
                'name' => __('accident_informs.mistake_' . MistakeTypeEnum::TRUE),
                'value' => MistakeTypeEnum::TRUE,
            ],
            (object) [
                'id' => MistakeTypeEnum::BOTH,
                'name' => __('accident_informs.mistake_' . MistakeTypeEnum::BOTH),
                'value' => MistakeTypeEnum::BOTH,
            ],
        ]);
    }

    public static function getZoneType()
    {
        $zone_type = collect([
            (object) [
                'id' => ZoneEnum::NORTH,
                'name' => __('garages.zone_type_' . ZoneEnum::NORTH),
                'value' => ZoneEnum::NORTH,
            ],
            (object) [
                'id' => ZoneEnum::NORTHEAST,
                'name' => __('garages.zone_type_' . ZoneEnum::NORTHEAST),
                'value' => ZoneEnum::NORTHEAST,
            ],
            (object) [
                'id' => ZoneEnum::WESTERN,
                'name' => __('garages.zone_type_' . ZoneEnum::WESTERN),
                'value' => ZoneEnum::WESTERN,
            ],
            (object) [
                'id' => ZoneEnum::CENTRAL,
                'name' => __('garages.zone_type_' . ZoneEnum::CENTRAL),
                'value' => ZoneEnum::CENTRAL,
            ],
            (object) [
                'id' => ZoneEnum::SOUTH,
                'name' => __('garages.zone_type_' . ZoneEnum::SOUTH),
                'value' => ZoneEnum::SOUTH,
            ],
        ]);
        return $zone_type;
    }

    public static function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public static function getNeedList()
    {
        return collect([
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('accident_informs.need_' . BOOL_TRUE),
            ],
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('accident_informs.need_' . BOOL_FALSE),
            ],
        ]);
    }

    public static function getReplacementList()
    {
        $replace_type = collect([
            (object) [
                'id' => ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN,
                'name' => __('accident_informs.replace_type_' . ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN),
                'value' => ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN,
            ],
        ]);
        return $replace_type;
    }

    public static function getProvinceList()
    {
        $list = Province::select('id', 'name_th as name')
            ->get();
        return $list;
    }

    public static function getCaseList()
    {
        return collect([
            (object) [
                'id' => CaseAccidentEnum::BUMP_FALL,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::BUMP_FALL),
                'value' => CaseAccidentEnum::BUMP_FALL,
            ],
            (object) [
                'id' => CaseAccidentEnum::MALICIOUS,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::MALICIOUS),
                'value' => CaseAccidentEnum::MALICIOUS,
            ],
            (object) [
                'id' => CaseAccidentEnum::OVERTURNING,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::OVERTURNING),
                'value' => CaseAccidentEnum::OVERTURNING,
            ],
            (object) [
                'id' => CaseAccidentEnum::CRASH,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::CRASH),
                'value' => CaseAccidentEnum::CRASH,
            ],
            (object) [
                'id' => CaseAccidentEnum::LOSS,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::LOSS),
                'value' => CaseAccidentEnum::LOSS,
            ],
            (object) [
                'id' => CaseAccidentEnum::FALL_WAYSIDE,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::FALL_WAYSIDE),
                'value' => CaseAccidentEnum::FALL_WAYSIDE,
            ],
            (object) [
                'id' => CaseAccidentEnum::UNKNOWN_CRASH,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::UNKNOWN_CRASH),
                'value' => CaseAccidentEnum::UNKNOWN_CRASH,
            ],
            (object) [
                'id' => CaseAccidentEnum::STONE_THROWN,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::STONE_THROWN),
                'value' => CaseAccidentEnum::STONE_THROWN,
            ],
            (object) [
                'id' => CaseAccidentEnum::STONE_THROWN_CAR,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::STONE_THROWN_CAR),
                'value' => CaseAccidentEnum::STONE_THROWN_CAR,
            ],
            (object) [
                'id' => CaseAccidentEnum::STONE_THROWN_TAIL_LAMP,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::STONE_THROWN_TAIL_LAMP),
                'value' => CaseAccidentEnum::STONE_THROWN_TAIL_LAMP,
            ],
            (object) [
                'id' => CaseAccidentEnum::OTHER,
                'name' => __('accident_informs.case_' . CaseAccidentEnum::OTHER),
                'value' => CaseAccidentEnum::OTHER,
            ],
        ]);
    }

    public static function getStatusJobList()
    {
        return collect([
            (object) [
                'id' => AccidentStatusEnum::WAITING_CLAIM,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::WAITING_CLAIM),
                'value' => AccidentStatusEnum::WAITING_CLAIM,
            ],
            (object) [
                'id' => AccidentStatusEnum::WAITING_REPAIR,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::WAITING_REPAIR),
                'value' => AccidentStatusEnum::WAITING_REPAIR,
            ],
            (object) [
                'id' => AccidentStatusEnum::IN_PROGRESS,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::IN_PROGRESS),
                'value' => AccidentStatusEnum::IN_PROGRESS,
            ],
            (object) [
                'id' => AccidentStatusEnum::SUCCESS,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::SUCCESS),
                'value' => AccidentStatusEnum::SUCCESS,
            ],
            (object) [
                'id' => AccidentStatusEnum::NOT_REPAIR,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::NOT_REPAIR),
                'value' => AccidentStatusEnum::NOT_REPAIR,
            ],
            (object) [
                'id' => AccidentStatusEnum::TTL,
                'name' => __('accident_informs.status_job_' . AccidentStatusEnum::TTL),
                'value' => AccidentStatusEnum::TTL,
            ],
        ]);
    }

    public static function getWoundList()
    {
        return collect([
            (object)[
                'id' => WoundType::A,
                'value' => WoundType::A,
                'name' => WoundType::A,
            ],
            (object)[
                'id' => WoundType::B,
                'value' => WoundType::B,
                'name' => WoundType::B,
            ],
            (object)[
                'id' => WoundType::C,
                'value' => WoundType::C,
                'name' => WoundType::C,
            ],
            (object)[
                'id' => WoundType::D,
                'value' => WoundType::D,
                'name' => WoundType::D,
            ],
            (object)[
                'id' => WoundType::OLD_SPARE_PART,
                'value' => WoundType::OLD_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::OLD_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::REPAIR_SPARE_PART,
                'value' => WoundType::REPAIR_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::REPAIR_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::NO_CHANGE_SPARE_PART,
                'value' => WoundType::NO_CHANGE_SPARE_PART,
                'name' => __('accident_informs.wound_type_' . WoundType::NO_CHANGE_SPARE_PART),
            ],

        ]);
    }

    public static function getRepairList()
    {
        return collect([
            (object)[
                'id' => RepairClaimEnum::HARD_BUMP,
                'value' => RepairClaimEnum::HARD_BUMP,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::HARD_BUMP),
            ],
            (object)[
                'id' => RepairClaimEnum::SOFT_BUMP,
                'value' => RepairClaimEnum::SOFT_BUMP,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::SOFT_BUMP),
            ],
            (object)[
                'id' => RepairClaimEnum::TTL,
                'value' => RepairClaimEnum::TTL,
                'name' => __('accident_informs.repair_claim_' . RepairClaimEnum::TTL),
            ],

        ]);
    }

    public static function getResponsibleList()
    {
        return collect([
            (object)[
                'id' => ResponsibleEnum::INSURANCE_ACCEPT,
                'value' => ResponsibleEnum::INSURANCE_ACCEPT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::INSURANCE_ACCEPT),
            ],
            (object)[
                'id' => ResponsibleEnum::INSURANCE_REJECT,
                'value' => ResponsibleEnum::INSURANCE_REJECT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::INSURANCE_REJECT),
            ],
            (object)[
                'id' => ResponsibleEnum::TLS_ACCEPT,
                'value' => ResponsibleEnum::TLS_ACCEPT,
                'name' => __('accident_informs.responsible_' . ResponsibleEnum::TLS_ACCEPT),
            ],

        ]);
    }

    public static function getRightsList()
    {
        return collect([
            (object)[
                'id' => RightsEnum::USE_RIGHTS,
                'value' => RightsEnum::USE_RIGHTS,
                'name' => __('accident_informs.rights_' . RightsEnum::USE_RIGHTS),
            ],
            (object)[
                'id' => RightsEnum::NOT_USE_RIGHTS,
                'value' => RightsEnum::NOT_USE_RIGHTS,
                'name' => __('accident_informs.rights_' . RightsEnum::NOT_USE_RIGHTS),
            ],

        ]);
    }


    public static function getAccidentRepairStatus()
    {
        return collect([
            (object) [
                'id' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST),
                'value' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION),
                'value' => AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR),
                'value' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL),
                'value' => AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::PROCESS_REPAIR,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::PROCESS_REPAIR),
                'value' => AccidentRepairStatusEnum::PROCESS_REPAIR,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::SUCCESS_REPAIR,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::SUCCESS_REPAIR),
                'value' => AccidentRepairStatusEnum::SUCCESS_REPAIR,
            ],
            (object) [
                'id' => AccidentRepairStatusEnum::TTL,
                'name' => __('accident_orders.status_job_' . AccidentRepairStatusEnum::TTL),
                'value' => AccidentRepairStatusEnum::TTL,
            ],
        ]);
    }

    public static function getRepairStatusList()
    {
        return collect([
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::WAITING_SEND_REPAIR,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_SEND_REPAIR),
                'value' => AccidentRepairFollowUpStatusEnum::WAITING_SEND_REPAIR,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::PREPARE_BID,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::PREPARE_BID),
                'value' => AccidentRepairFollowUpStatusEnum::PREPARE_BID,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::WAITING_INSURANCE_APPROVE,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_INSURANCE_APPROVE),
                'value' => AccidentRepairFollowUpStatusEnum::WAITING_INSURANCE_APPROVE,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::WAITING_SPARE_PART,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_SPARE_PART),
                'value' => AccidentRepairFollowUpStatusEnum::WAITING_SPARE_PART,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::PART_BO,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::PART_BO),
                'value' => AccidentRepairFollowUpStatusEnum::PART_BO,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_KNOCK,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_KNOCK),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_KNOCK,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_KNOCK,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_KNOCK),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_KNOCK,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_PUTTY,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_PUTTY),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_PUTTY,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PUTTY,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PUTTY),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PUTTY,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_GROUND_COLOR,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_GROUND_COLOR),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_GROUND_COLOR,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_GROUND_COLOR,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_GROUND_COLOR),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_GROUND_COLOR,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_TRUE_COLOR,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_TRUE_COLOR),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_TRUE_COLOR,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_TRUE_COLOR,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_TRUE_COLOR),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_TRUE_COLOR,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_ATTRITION,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_ATTRITION),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_ATTRITION,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ATTRITION,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ATTRITION),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ATTRITION,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ASSEMBLE,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ASSEMBLE),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ASSEMBLE,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_CHECK_QUALITY,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_CHECK_QUALITY),
                'value' => AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_CHECK_QUALITY,
            ],
            (object) [
                'id' => AccidentRepairFollowUpStatusEnum::SUCCESS,
                'name' => __('accident_follow_up_repairs.repair_status_' . AccidentRepairFollowUpStatusEnum::SUCCESS),
                'value' => AccidentRepairFollowUpStatusEnum::SUCCESS,
            ],

        ]);
    }

    public static function getAccidentSlideList()
    {
        return collect([
            (object) [
                'id' => AccidentSlideEnum::TLS_SLIDE,
                'name' => __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE),
                'value' => AccidentSlideEnum::TLS_SLIDE,
            ],
            (object) [
                'id' => AccidentSlideEnum::THIRD_PARTY_SLIDE,
                'name' => __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE),
                'value' => AccidentSlideEnum::THIRD_PARTY_SLIDE,
            ],
        ]);
    }
}
