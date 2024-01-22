<?php

use App\Enums\RecordPettyCashStatusEnum;
use App\Enums\CheckPettyCashStatusEnum;

return [
    'page_title' => 'บันทึกเบิกเงินสดย่อย',
    'record_no' => 'เลขที่ใบคุมงาน',
    'branch' => 'สาขา',
    'cost_center' => 'costcenter/ผู้ถือเงินสดย่อย',
    'total' => 'จำนวนเงินทั้งหมด',
    'add_create' => 'เพิ่มเบิกเงินสดย่อย',

    'status_' . RecordPettyCashStatusEnum::DRAFT => 'ร่าง',
    'status_' . RecordPettyCashStatusEnum::PENDING => 'รอบัญชีตรวจสอบ',
    'status_' . RecordPettyCashStatusEnum::COMPLETE => 'บันทึกเบิกเงินสดย่อยเสร็จสิ้น',

    'status_' . RecordPettyCashStatusEnum::DRAFT . '_class' => 'primary',
    'status_' . RecordPettyCashStatusEnum::PENDING  . '_class' => 'warning',
    'status_' . RecordPettyCashStatusEnum::COMPLETE . '_class' => 'success',

    //check petty cash
    'page_title_check' => 'ตรวจสอบเบิกเงินสดย่อย',

    'status_' . CheckPettyCashStatusEnum::PENDING => 'รอบัญชีตรวจสอบ',
    'status_' . CheckPettyCashStatusEnum::COMPLETE => 'ตรวจสอบเสร็จสิ้น',

    'status_' . CheckPettyCashStatusEnum::PENDING  . '_class' => 'warning',
    'status_' . CheckPettyCashStatusEnum::COMPLETE . '_class' => 'success',
];
