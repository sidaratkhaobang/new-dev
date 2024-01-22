<?php
use App\Enums\InvoiceStatusEnum;

return [
    'lt_rental_page_title' => 'ใบแจ้งหนี้เช่ายาว',
    'st_rental_page_title' => 'ใบแจ้งหนี้เช่าสั้น',
    'other_page_title' => 'ใบแจ้งหนี้อื่นๆ',
    'invoice_no' => 'เลขที่ใบแจ้งหนี้',
    'customer_code_name' => 'รหัส/ชื่อลูกค้า',
    'contract_start_date' => 'วันที่เริ่มสัญญา',
    'contract_end_date' => 'วันที่สิ้นสุดสัญญา',
    'license_plate' => 'ทะเบียนรถ',
    'instalment' => 'จำนวนงวดทั้งหมด',

    'status_' . InvoiceStatusEnum::IN_PROCESS => 'อยู่ระหว่างดำเนินการ',
    'class_' . InvoiceStatusEnum::IN_PROCESS => 'warning',
    'status_' . InvoiceStatusEnum::COMPLETE => 'เสร็จสิ้น',
    'class_' . InvoiceStatusEnum::COMPLETE => 'success',

    'branch' => 'สาขา',
    'invoice_type' => 'ประเภทใบแจ้งหนี้',
    'buyer' => 'รหัส/ชื่อผู้ซื้อ'
];