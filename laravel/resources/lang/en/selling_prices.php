<?php

use App\Enums\SellingPriceStatusEnum;

return [
    'page_title' => 'รถรอส่งขายทั้งหมด/ทำราคาขายล่วงหน้า',
    //ทำราคาขายรถล่วงหน้า
    'sale_price' => 'ทำราคาขายรถล่วงหน้า',
    'worksheet_no' => 'เลขที่ใบคำขออนุมัติ',
    'car_color' => 'สีจดทะเบียน',
    'price' => 'ราคาอนุมัติขายไม่รวม VAT',
    'vat' => 'VAT',
    'total' => 'ราคาอนุมัติขายรวม VAT',
    'ownership' => 'ผู้ถือกรรมสิทธิ์',
    'car_table' => 'ข้อมูลรถ',
    'mileage' => 'เลขไมล์ล่าสุด',
    'request_approve' => 'ส่งขออนุมัติ',
    'new_sale_price' => 'ทำราคาขายรถล่วงหน้าใหม่',
    'new_request_approve' => 'ขออนุมัติราคาใหม่',
    //รถรอส่งขาย
    'car_sale' => 'รถรอส่งขาย',
    'noti_finance' => 'แจ้งปิดไฟแนนซ์',
    'noti_finance_date' => 'วันทีแจ้งปิดไฟแนนซ์',
    'expected_off_finance' => 'วันทีคาดว่าจะปิดไฟแนนซ์',
    'expected_transfer_ownership' => 'วันทีคาดว่าจะโอนกรรมสิทธิ์เสร็จ',
    'transfer_ownership' => 'วันทีโอนกรรมสิทธิ์',

    //อนุมัติทำราคาขายรถล่วงหน้า
    'approve_title' => 'อนุมัติทำราคาขายรถล่วงหน้า',
    'amount_car' => 'จำนวนรถในใบงาน',
    'sub_list' => 'รายการหัวข้อย่อย',

    //status ทำราคาขายรถล่วงหน้า
    'status_' . SellingPriceStatusEnum::PRE_SALE_PRICE => 'รอทำราคาขายล่วงหน้า',
    'status_' . SellingPriceStatusEnum::REQUEST_APPROVE => 'รอส่งขออนุมัติ',
    'status_' . SellingPriceStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'status_' . SellingPriceStatusEnum::CONFIRM => 'อนุมัติ',
    'status_' . SellingPriceStatusEnum::REJECT => 'ไม่อนุมัติ',

    'class_' . SellingPriceStatusEnum::PRE_SALE_PRICE => 'primary',
    'class_' . SellingPriceStatusEnum::REQUEST_APPROVE => 'primary',
    'class_' . SellingPriceStatusEnum::PENDING_REVIEW => 'primary',
    'class_' . SellingPriceStatusEnum::CONFIRM => 'success',
    'class_' . SellingPriceStatusEnum::REJECT => 'danger',

    //status รถรอส่งขาย
    'status_' . SellingPriceStatusEnum::PENDING_SALE => 'รถรอส่งขาย',
    'status_' . SellingPriceStatusEnum::PENDING_FINANCE => 'รอปิดไฟแนนซ์',
    'status_' . SellingPriceStatusEnum::PENDING_TRANSFER => 'รอโอนกรรมสิทธ์',

    'class_' . SellingPriceStatusEnum::PENDING_SALE => 'primary',
    'class_' . SellingPriceStatusEnum::PENDING_FINANCE => 'warning',
    'class_' . SellingPriceStatusEnum::PENDING_TRANSFER => 'warning',
];
