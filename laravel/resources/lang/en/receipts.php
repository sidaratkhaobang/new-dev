<?php

use App\Enums\ReceiptTypeEnum;
use App\Enums\ReceiptStatusEnum;

return [
    'page_title' => 'ใบเสร็จรับเงิน / ใบกำกับภาษี',
    'worksheet_no' => 'เลขที่ใบเสร็จรับเงิน',
    'receipt_type' => 'ประเภทใบเสร็จรับเงิน',
    'customer_table' => 'ข้อมูลลูกค้า',
    'customer_name' => 'ลูกค้า',
    'customer_code' => 'รหัสลูกค้า',
    'customer_tax' => 'หมายเลขประจำตัวผู้เสียภาษี',
    'customer_address' => 'ที่อยู่',
    'list_table' => 'ข้อมูลรายการ',
    'reference_no' => 'อ้างอิงเอกสารเลขที่',
    'date' => 'วันที่',
    'list' => 'รายการ',
    'amount' => 'จำนวนเงิน',
    'edit_address' => 'แก้ไขที่อยู่',
    'print_receipt' => 'พิมพ์ใบเสร็จรับเงิน',
    'total_items' => 'ข้อมูลทั้งหมด',

    'receipt_type_' . ReceiptTypeEnum::CAR_RENTAL => 'ค่าเช่ารถยนต์', //บิลหลัก
    'receipt_type_' . ReceiptTypeEnum::VOUCHER_OF_CASH => 'ค่าบัตรกำนัลแทนเงินสด', //คูปอง
    'receipt_type_' . ReceiptTypeEnum::OTHER => 'ค่าอื่น ๆ', //บิลเสริม

    'status_' . ReceiptStatusEnum::ACTIVE => 'ใช้งาน',
    'status_' . ReceiptStatusEnum::INACTIVE => 'ยกเลิก',
];
