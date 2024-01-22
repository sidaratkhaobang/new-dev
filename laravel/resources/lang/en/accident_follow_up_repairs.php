<?php

use App\Enums\AccidentRepairFollowUpStatusEnum;
use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\AccidentTypeEnum;
use App\Enums\CarTypeAccidentEnum;
use App\Enums\CaseAccidentEnum;
use App\Enums\ClaimantAccidentEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\GarageTypeEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\OfferGMStatusEnum;
use App\Enums\RepairClaimEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\ResponsibleEnum;
use App\Enums\RightsEnum;
use App\Enums\WoundType;
use App\Enums\ZoneEnum;
use App\Enums\ZoneTypeEnum;

return [

    'page_title' => 'รายการติดตามงานซ่อม',
    'page_title_call_center' => 'ติดตามงานซ่อม',
    'add_new' => 'เพิ่มข้อมูล',
    'name' => 'ชื่ออู่',
    'total_items' => 'รายการทั้งหมด',
    'car_info' => 'ข้อมูลรถ',
    'follow_repair' => 'ติดตามงานซ่อม',
    'recieve_date' => 'วันที่ได้รับข้อมูลจากอู่',
    'repair_status' => 'สถานะของงานซ่อม',
    'detail' => 'รายละเอียด/ปัญหา',
    'solution' => 'แนวทางการแก้ไข',
    'garage' => 'อู่ที่ซ่อม',
    'accident_worksheet_no_ref' => 'อ้างอิงเลขที่ใบแจ้งอุบัติเหตุ',

    'repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_SEND_REPAIR => 'รอส่งซ่อม',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::PREPARE_BID => 'เตรียมเสนอราคา',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_INSURANCE_APPROVE => 'รอประกันอนุมัติ',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::WAITING_SPARE_PART => 'รออะไหล่',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::PART_BO => 'อะไหล่ติด B/O',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_KNOCK => 'ระหว่างซ่อม: เตรียมเคาะ',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_KNOCK => 'ระหว่างซ่อม: เคาะ',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_PUTTY => 'ระหว่างซ่อม: เตรียมโป๊วสี',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PUTTY => 'ระหว่างซ่อม: โป๊วสี',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_GROUND_COLOR => 'ระหว่างซ่อม: เตรียมพ่นสีพื้น',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_GROUND_COLOR => 'ระหว่างซ่อม: พ่นสีพื้น',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_TRUE_COLOR => 'ระหว่างซ่อม: เตรียมพ่นสีจริง',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_TRUE_COLOR => 'ระหว่างซ่อม: พ่นสีจริง',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_PREPARE_ATTRITION => 'ระหว่างซ่อม: เตรียมขัดสี',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ATTRITION => 'ระหว่างซ่อม: ขัดสี',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_ASSEMBLE => 'ระหว่างซ่อม: ประกอบ',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::UNDER_REPAIR_CHECK_QUALITY => 'ระหว่างซ่อม: อู่ตรวจสอบคุณภาพงานซ่อม',
    'repair_status_' . AccidentRepairFollowUpStatusEnum::SUCCESS => 'ซ่อมเสร็จ',
 ];