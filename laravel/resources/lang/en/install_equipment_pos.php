<?php

use App\Enums\ApproveStepEnum;
use App\Enums\InstallEquipmentPOStatusEnum;

return [
    'page_title' => 'ใบสั่งซื้ออุปกรณ์',
    'worksheet_no' => 'เลขที่ใบสั่งซื้อ',
    'ie_worksheet_no' => 'เลขที่ใบขอติดตั้งอุปกรณ์',

    'status_' . InstallEquipmentPOStatusEnum::COMPLETE => 'เสร็จสิ้น',

    'status_' . InstallEquipmentPOStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'class_' . InstallEquipmentPOStatusEnum::PENDING_REVIEW => 'warning',
    'status_' . InstallEquipmentPOStatusEnum::CONFIRM => 'อนุมัติ',
    'class_' . InstallEquipmentPOStatusEnum::CONFIRM => 'success',
    'status_' . InstallEquipmentPOStatusEnum::REJECT => 'ไม่อนุมัติ',
    'class_' . InstallEquipmentPOStatusEnum::REJECT => 'danger',
    'status_' . InstallEquipmentPOStatusEnum::CANCEL => 'ยกเลิก',
    'class_' . InstallEquipmentPOStatusEnum::CANCEL => 'secondary',

    'status_approve_' . ApproveStepEnum::CONFIRM => 'Confirm',
    'class_approve_' . ApproveStepEnum::CONFIRM => 'check',
    'status_approve_' .  ApproveStepEnum::PENDING => 'Waiting for review ',
    'class_approve_' . ApproveStepEnum::PENDING => 'pending',
    'status_approve_' . ApproveStepEnum::PENDING_PREVIOUS => 'Wait from previous users',
    'class_approve_' . ApproveStepEnum::PENDING_PREVIOUS => 'pending-previous',
    'status_approve_' . ApproveStepEnum::REJECT => 'Reject',
    'class_approve_' . ApproveStepEnum::REJECT => 'danger',

    'info' =>  'ข้อมูลใบสั่งซื้อ',
    'time_of_delivery' =>  'กำหนดการส่งสินค้า',
    'payment_term' =>  'เงื่อนไขการชำระเงิน',
    'contact' =>  'ติดต่อ',
    'car_user' =>  'ผู้ใช้รถ',
    'quotation' =>  'ใบเสนอราคา ลว.',
    'accessory_info' =>  'ข้อมูลอุปกรณ์ที่สั่งซื้อ',
    'total_net_price' =>  'ราคารวมสุทธิ',
    'vat_7' =>  'VAT 7%',
    'exclude_vat_price' =>  'ราคาไม่รวม VAT',
    'disapprove_confirm' => 'ยืนยันไม่อนุมัติ ใบสั่งซื้ออุปกรณ์',
    'approve_confirm' => 'ยืนยันอนุมัติ ใบสั่งซื้ออุปกรณ์',
    'print' =>  'พิมพ์ใบสั่งซื้อ',
    'step_approve' => 'สถานะการขออนุมัติ (การตรวจสอบตามลำดับ)',
    'history_approve' => 'ประวัติการอนุมัติ'
];
