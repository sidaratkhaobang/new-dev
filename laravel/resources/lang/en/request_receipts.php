<?php

use App\Enums\RequestReceiptStatusEnum;
use App\Enums\RequestReceiptTypeEnum;

return [

    'page_title' => 'แจ้งขอใบเสร็จรับเงิน',
    'add_new' => 'เพิ่มขอใบเสร็จรับเงิน',
    'receipt_detail' => 'ข้อมูลขอใบเสร็จรับเงิน',
    'type' => 'ประเภทใบเสร็จรับเงิน',
    'customer_detail' => 'ข้อมูลลูกค้า',
    'list_name' => 'รายการ',
    'fee_deducted' => 'หักค่าธรรมเนียม',
    'total' => 'จำนวนเงินสุทธิ',
    'amount' => 'จำนวนเงิน',
    'detail' => 'รายละเอียด',
    'title' => 'หัวข้อ',
    'customer' => 'รหัส/ชื่อลูกค้า',
    'customer_tax_no' => 'หมายเลขประจำตัวผู้เสียภาษี',
    'customer_address' => 'ที่อยู่',
    'list_detail' => 'ข้อมูลรายการ',
    'customer_name' => 'ชื่อลูกค้า',
    'is_select_db_customer' => 'เลือกจากฐานข้อมูลลูกค้า',
    'validate_list' => 'กรุณาเพิ่มข้อมูลรายการอย่างน้อย 1 รายการ',
    'save_draft' => 'บันทึกร่าง',
    'save' => 'บันทึกขอใบเสร็จรับเงิน',
    'receipt_no_ref' => "อ้างอิงเลขที่ใบเสร็จ",
    'status' => 'สถานะ',
    'inform_date' => 'วันที่แจ้ง',
    'worksheet_no' => 'เลขที่ใบแจ้งขอเสร็จรับเงิน',

    'class_' . RequestReceiptStatusEnum::DRAFT => 'primary',
    'text_' . RequestReceiptStatusEnum::DRAFT => 'ร่าง',

    'class_' . RequestReceiptStatusEnum::WAITING_RECEIPT => 'warning',
    'text_' . RequestReceiptStatusEnum::WAITING_RECEIPT => 'รอออกใบเสร็จ',

    'class_' . RequestReceiptStatusEnum::SUCCESS_RECEIPT => 'success',
    'text_' . RequestReceiptStatusEnum::SUCCESS_RECEIPT => 'ออกใบเสร็จแล้ว',

    'type_' . RequestReceiptTypeEnum::RECEIPT => 'ใบเสร็จรับเงิน',
    'type_' . RequestReceiptTypeEnum::RECEIPT_TAX => 'ใบเสร็จรับเงิน/ใบกำกับภาษี',
];
