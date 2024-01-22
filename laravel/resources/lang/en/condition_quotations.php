<?php

use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\ServiceTypeEnum;

return [
    'page_title' => 'เงื่อนไขใบเสนอราคา',
    'condition' => 'เงื่อนไข',
    'condition_table' => 'ข้อมูลเงื่อนไข',
    'condition_seq' => 'ลำดับหัวข้อ',
    'condition_name' => 'หัวข้อเงื่อนไข',
    'checklist_seq' => 'ลำดับรายการ',
    'checklist_name' => 'รายการ',
    'add_new' => 'เพิ่มเงื่อนไข',

    'type_' . LongTermRentalApprovalTypeEnum::AFFILIATED => 'ในเครือ',
    'type_' . LongTermRentalApprovalTypeEnum::UNAFFILIATED => 'นอกเครือ',
    'type_' . ServiceTypeEnum::SELF_DRIVE => 'เช่าขับเอง',
    'type_' . ServiceTypeEnum::MINI_COACH => 'มินิโค้ช',
    'type_' . ServiceTypeEnum::BUS => 'บัส',
    'type_' . ServiceTypeEnum::BOAT => 'เรือ',
    'type_' . ServiceTypeEnum::LIMOUSINE => 'ลิมูซีน',
    'type_' . ServiceTypeEnum::SLIDE_FORKLIFT => 'รถสไลด์',
    'long_term_page_title' => 'เงื่อนไขใบเสนอราคางานเช่ายาว',
    'short_term_page_title' => 'เงื่อนไขใบเสนอราคางานเช่าสั้น',
    'condition_type' => 'ประเภทเงื่อนไข',

    'repair_page_title' => 'เงื่อนไขส่งซ่อมศูนย์บริการ',
    'checklist_table' => 'รายการหัวข้อย่อย',
];
