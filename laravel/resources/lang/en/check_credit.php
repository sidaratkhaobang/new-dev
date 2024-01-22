<?php

use App\Enums\CheckCreditStatusEnum;

return [
    'index' => [
        'title' => [
            'new_customer' => 'แจ้งตรวจสอบเครดิตลูกค้าใหม่',
            'approve' => 'งานตรวจสอบเครดิตลูกค้าใหม่',
        ],
        'page_title' => 'แจ้งตรวจสอบเครดิตลูกค้าใหม่',
        'total_items' => 'รายการทั้งหมด',
        'btn-create-page' => 'เพิ่มข้อมูล',
        'search' => [
            'customer_type' => 'ประเภทลูกค้า',
            'customer_name' => 'ลูกค้า',
            'branche_id' => 'สาขา',
            'status' => 'สถานะ',
        ],
        'table' => [
            'worksheet_no' => 'เลขที่งานตรวจสอบเครดิต',
            'customer_type' => 'ประเภทลูกค้า',
            'customer_name' => 'ลูกค้า',
            'branch_name' => 'สาขา',
            'status' => 'สถานะ',
        ]
    ],
    'form' => [
        'page_title' => 'เพิ่มตรวจสอบเครดิต',
        'section_info' => 'ข้อมูลงานตรวจสอบเครดิต',
        'worksheet_no' => 'เลขที่งานตรวจสอบเครดิต',
        'author_name' => 'ผู้จัดทำ',
        'create_date' => 'วันที่จัดทำ',
        'section_customer' => 'ข้อมูลลูกค้า',
        'customer_code' => 'รหัสลูกค้า',
        'branch_id' => 'สาขา',
        'customer_type' => 'ประเภทลูกค้า',
        'customer_grade' => 'เกรดลูกค้า',
        'customer_name' => 'ชื่อลูกค้า',
        'customer_group_id' => 'กลุ่มลูกค้า',
        'customer_tax_number' => 'เลขที่เสียภาษี',
        'customer_prefix_name_th' => 'คำนำหน้าชื่อ (ภาษาไทย)',
        'customer_full_name_th' => 'ชื่อเต็มลูกค้า (ภาษาไทย)',
        'customer_prefix_name_en' => 'คำนำหน้าชื่อ (ภาษาอังกฤษ)',
        'customer_full_name_en' => 'ชื่อเต็มลูกค้า (ภาษาอังกฤษ)',
        'customer_email' => 'Email',
        'customer_fax' => 'แฟกซ์',
        'customer_mobile_number' => 'เบอร์โทร',
        'customer_phone_number' => 'มือถือ',
        'customer_address' => 'ที่อยู่',
        'btn-save-pending-approve' => 'บันทึกส่งตรวจเครดิต',
        'btn-save-create-customer' => 'สร้างข้อมูลลูกค้าใหม่',
        'btn-save' => 'บันทึก',
        'section_table' => [
            'title' => 'เอกสารสำหรับตรวจสอบเครดิต',
            'btn-add-file' => 'เพิ่ม',
            'file_name' => 'ชื่อเอกสาร',
            'extension_name' => 'ไฟล์',
        ],
        'document_file' => 'เอกสารสำหรับตรวจสอบเครดิต',
        'result_check_credit' => 'ผลการตรวจสอบเครดิต',
        'approved_amount' => 'วงเงินที่อนุมัติ (บาท)',
        'approved_days' => 'วันที่อนุมัติเครดิต (วัน)',
        'reason' => 'เหตุผลที่ไม่อนุมัติ',
    ],

    'status_class_' . CheckCreditStatusEnum::DRAFT => 'info',
    'status_class_' . CheckCreditStatusEnum::PENDING_REVIEW => 'warning',
    'status_class_' . CheckCreditStatusEnum::CONFIRM => 'success',
    'status_class_' . CheckCreditStatusEnum::REJECT => 'danger',

    'status_text_' . CheckCreditStatusEnum::DRAFT => 'ร่าง',
    'status_text_' . CheckCreditStatusEnum::PENDING_REVIEW => 'รอตรวจสอบเครดิต',
    'status_text_' . CheckCreditStatusEnum::CONFIRM => 'อนุมัติ',
    'status_text_' . CheckCreditStatusEnum::REJECT => 'ไม่อนุมัติ',
];
