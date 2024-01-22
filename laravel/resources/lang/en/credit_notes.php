<?php
use App\Enums\CreditNoteStatusEnum;

return [
    'page_title' => 'ใบลดหนี้',
    'credit_note_no' => 'เลขที่ใบลดหนี้',
    'invoice_no' => 'เลขที่ใบแจ้งหนี้',
    'customer_code_name' => 'รหัส/ชื่อผู้ซื้อ',

    'status_' . CreditNoteStatusEnum::IN_PROCESS => 'อยู่ระหว่างดำเนินการ',
    'class_' . CreditNoteStatusEnum::IN_PROCESS => 'warning',
    'status_' . CreditNoteStatusEnum::COMPLETE => 'ดำเนินการเสร็จสิ้น',
    'class_' . CreditNoteStatusEnum::COMPLETE => 'success',
];