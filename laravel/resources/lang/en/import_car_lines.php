<?php
use App\Enums\ImportCarLineStatusEnum;

return [
    'status_'.ImportCarLineStatusEnum::PENDING => 'รอกรอกข้อมูล',
    'status_'.ImportCarLineStatusEnum::PENDING_REVIEW => 'รอตรวจสอบ',
    'status_'.ImportCarLineStatusEnum::CONFIRM_DATA => 'ยืนยันข้อมูล',
    'status_'.ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA => 'คู่ค้ายืนยัน',
    'status_'.ImportCarLineStatusEnum::REJECT_DATA => 'แก้ไขข้อมูล',
    'status_' .ImportCarLineStatusEnum::PENDING_DELIVERY => 'รอส่งมอบ',
    'status_' .ImportCarLineStatusEnum::SUCCESS_DELIVERY => 'ส่งมอบสำเร็จ',
    'status_' .ImportCarLineStatusEnum::SUCCESS_DELIVERY => 'ส่งมอบแล้ว',
    'class_' . ImportCarLineStatusEnum::SUCCESS_DELIVERY => 'success',
    'class_' .ImportCarLineStatusEnum::PENDING_DELIVERY => 'primary',
    'class_' .ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA => 'vendor-color',
    'class_' .ImportCarLineStatusEnum::CONFIRM_DATA => 'tls-color',
];
