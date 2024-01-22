<?php

use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\WageCalType;
use App\Enums\WageCalDay;
use App\Enums\WageCalTime;
return [

    'page_title' => 'รายจ่ายพนักงานขับรถ',
    'name' => 'ชื่อรายจ่าย',
    'status' => 'สถานะ',
    'service_category' => 'ประเภทงาน',
    'add_new' => 'เพิ่มรายจ่าย',
    'total_items' => 'รายการทั้งหมด',
    'type' => 'ประเภท',
    'type_name' => 'ประเภท',
    'service_type' => 'ประเภทบริการ',
    'wage_type' => 'ประเภทรายจ่าย',
    'standard' => 'มาตรฐาน',
    'other' => 'รายจ่ายอื่นๆ',
    'special_wage' => 'รายจ่ายพิเศษ',
    'special_wage_cal' => 'รายจ่ายที่นำมาคำนวณพิเศษ',
    'seq' => 'ลำดับแสดงผล',
    'wage_type_cal' => 'รูปแบบการคิดรายจ่าย',
    'wage_day_cal' => 'วันที่คิดค่าใช้จ่าย',
    'wage_time_cal' => 'เวลาที่คิดค่าใช้จ่าย',
    'status_' . STATUS_ACTIVE => 'มาตรฐาน',
    'status_no_' . STATUS_DEFAULT => 'ไม่ใช่',
    'status_yes_' . STATUS_ACTIVE => 'ใช่',
    'status_' . STATUS_DEFAULT => 'รายจ่ายอื่นๆ',
    'class_' . STATUS_ACTIVE  => 'success',
    'class_' . STATUS_DEFAULT => 'secondary',
    'class_' . STATUS_DEFAULT  => 'secondary',
    'status_' . WageCalType::PER_MONTH => 'ต่อเดือน',
    'status_' . WageCalType::PER_DAY => 'ต่อวัน',
    'status_' . WageCalType::PER_HOUR => 'ต่อชั่วโมง',
    'status_' . WageCalType::PER_TRIP => 'ต่อเที่ยว',
    'status_' . WageCalDay::ALL => 'ทั้งหมด',
    'status_' . WageCalDay::WORK_DAY => 'เฉพาะวันทำงาน',
    'status_' . WageCalDay::HOLIDAY => 'เฉพาะวันหยุด',
    'status_' . WageCalTime::ALL => 'ทั้งหมด',
    'status_' . WageCalTime::WORK_TIME => 'เฉพาะในเวลาทำงาน',
    'status_' . WageCalTime::OUT_OF_WORK_TIME => 'เฉพาะนอกเวลางาน',
];
