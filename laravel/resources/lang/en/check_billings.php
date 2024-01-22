<?php

use App\Enums\DebtCollectionStatusEnum;
use App\Enums\CheckBillingStatusEnum;

return [
    'page_title' => 'ตรวจสอบวันวางบิล',
    'all_no' => 'เลขที่ใบแจ้งหนี้/เลขที่ใบแจ้งลด',
    'invoice_no' => 'เลขที่ใบแจ้งหนี้',
    'credit_note_no' => 'เลขที่ใบแจ้งลด',
    'license_plate' => 'ทะเบียน',
    'customer_name' => 'ชื่อลูกค้า',
    'schedule_billing' => 'กำหนดวางบิล',
    'check_billing_date' => 'วันที่วางบิล',
    'period_no' => 'งวดที่',
    'amount' => 'จำนวนเงิน',
    'download_excel_bill' => 'Excel ใบรับวางบิล',
    'upload_file' => 'อัปโหลดไฟล์ข้อมูล',
    'table_car' => 'ข้อมูลรถ',
    'car_status' => 'สถานะรถ',
    'table_invoice' => 'ข้อมูลใบแจ้งหนี้',
    'start_bill_date' => 'เริ่มต้นวันที่/วันที่วางบิล',
    'type_bill' => 'วิธีการวางบิล',
    'invoice_date' => 'วันที่ออกใบแจ้งหนี้',
    'doc_bill' => 'เอกสารประกอบ',
    'table_status' => 'ข้อมูลสถานะ',
    'sending_billing_date' => 'วันที่ส่ง',
    'detail' => 'รายละเอียด',
    'remark' => 'หมายเหตุ',

    //status/class check billing
    'status_' . DebtCollectionStatusEnum::PENDING => 'รอดำเนินการ',
    'status_' . DebtCollectionStatusEnum::COMPLETE => 'ดำเนินการเสร็จสิ้น',
    'status_' . DebtCollectionStatusEnum::OVERDUE => 'ค้างชำระ',

    'status_' . DebtCollectionStatusEnum::PENDING . '_class' => 'primary',
    'status_' . DebtCollectionStatusEnum::COMPLETE . '_class' => 'success',
    'status_' . DebtCollectionStatusEnum::OVERDUE . '_class' => 'danger',

    //status check billing status
    'sub_status_' . CheckBillingStatusEnum::SUCCESS => 'สำเร็จ',
    'sub_status_' . CheckBillingStatusEnum::UNSUCCESS => 'ไม่สำเร็จ',

    // //type day lang
    // 'schedule_' . CheckBillingTypeEnum::ALL_DAY => 'ทุกวัน',
    // 'schedule_' . CheckBillingTypeEnum::ALL_MON_DAY => 'ทุกวันจันทร์',
    // 'schedule_' . CheckBillingTypeEnum::ALL_TUE_DAY => 'ทุกวันอังคาร',
    // 'schedule_' . CheckBillingTypeEnum::ALL_WED_DAY => 'ทุกวันพุธ',
    // 'schedule_' . CheckBillingTypeEnum::ALL_THU_DAY => 'ทุกวันพฤหัสบดี',
    // 'schedule_' . CheckBillingTypeEnum::ALL_FRI_DAY => 'ทุกวันศุกร์',
    // 'schedule_' . CheckBillingTypeEnum::SET_DATE => 'กำหนดวัน',
];
