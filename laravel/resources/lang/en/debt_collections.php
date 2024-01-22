<?php

use App\Enums\DebtCollectionStatusEnum;
use App\Enums\DebtCollectionSubStatusEnum;
use App\Enums\DebtCollectionChannelTypeEnum;

return [
    'page_title' => 'การติดตามหนี้',
    'invoice_no' => 'เลขที่ใบแจ้งหนี้',
    'customer_code' => 'รหัสลูกค้า',
    'customer_name' => 'ชื่อลูกค้า',
    'customer_group' => 'กลุ่มลูกค้า',
    'latest_due_date' => 'วันที่ค้างชำระล่าสุด',
    'overdue' => 'ยอดค้างชำระ',
    'pdf_invoice' => 'พิมพ์ใบแจ้งหนี้',
    'download_excel_debt' => 'ดาวน์โหลดรายงานติดตามหนี้',
    'table_rental' => 'ข้อมูลงานเช่า',
    'lt_rental_no' => 'เลขที่ใบขอเช่า',
    'contract_no' => 'เลขที่สัญญา',
    'contract_start_date' => 'วันที่เริ่มสัญญา',
    'contract_end_date' => 'วันที่สิ้นสุดสัญญา',
    'table_customer' => 'ข้อมูลลูกค้า',
    'customer_tax' => 'เลขประจำตัวเสียภาษี',
    'customer_tel' => 'เบอร์สำนักงาน',
    'customer_phone' => 'เบอร์โทร',
    'customer_address' => 'ที่อยู่ลูกค้า',
    'table_overdue' => 'ข้อมูลค้างชำระ',
    'doc_date' => 'Doc. Date',
    'range_date' => 'ช่วงวันที่',
    'assignment' => 'Assignment',
    'doc_number' => 'Document Number',
    'license_plate' => 'เลขทะเบียน',
    'type' => 'Type',
    'amount' => 'ยอดเงิน',
    'table_billing' => 'ข้อมูลการวางบิล',
    'send_date' => 'วันที่ส่ง',
    'bill_date' => 'วันที่วางบิล',
    'detail' => 'รายละเอียด',
    'table_channel' => 'ข้อมูลช่องทางติดตามหนี้',
    'notification_date' => 'วันที่แจ้งติดตาม',
    'channel' => 'ช่องทาง',

    'status_' . DebtCollectionStatusEnum::PENDING => 'รอดำเนินการ',
    'status_' . DebtCollectionStatusEnum::WAITING => 'อยู่ระหว่างรอชำระ',
    'status_' . DebtCollectionStatusEnum::COMPLETE => 'ดำเนินการเสร็จสิ้น',
    'status_' . DebtCollectionStatusEnum::OVERDUE => 'ค้างชำระ',

    'status_' . DebtCollectionStatusEnum::PENDING . '_class' => 'primary',
    'status_' . DebtCollectionStatusEnum::WAITING . '_class' => 'warning',
    'status_' . DebtCollectionStatusEnum::COMPLETE . '_class' => 'success',
    'status_' . DebtCollectionStatusEnum::OVERDUE . '_class' => 'danger',

    'sub_status_' . DebtCollectionSubStatusEnum::DONE => 'แจ้งเรียบร้อย',
    'sub_status_' . DebtCollectionSubStatusEnum::LITIGATION => 'งานคดีความ',
    'sub_status_' . DebtCollectionSubStatusEnum::NOT_CONTACT => 'ติดต่อไม่ได้',

    'channel_' . DebtCollectionChannelTypeEnum::EMAIL => 'อีเมล',
    'channel_' . DebtCollectionChannelTypeEnum::PHONE => 'เบอร์โทร',
];
