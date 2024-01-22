<?php

use App\Enums\CheckCreditStatusEnum;

return [
    'index' => [
        'page_title' => 'ประเภทและเงื่อนไขสัญญา',
        'total_items' => 'รายการทั้งหมด',
        'search' => [
            'label' => 'รายการค้นหา',
            'placeholder' => 'กรุณาใส่ข้อมูล',
            'btn-create-page' => 'เพิ่ม',
        ],
        'table' => [
            'contract-category' => 'ประเภทสัญญา',
        ]
    ],
    'form' => [
        'page_title' => 'เพิ่มประเภทสัญญา',
        'condition_name' => 'ชื่อประเภทสัญญา',
        'table' => [
            'seq' => 'ลำดับหัวข้อ',
            'name' => 'หัวข้อสัญญา',
            'sub' => [
                'seq' => 'ลำดับหัวข้อ',
                'name' => 'หัวข้อย่อย',
            ]
        ],
        'validate' => [
            'seq' => 'ลำดับหัวข้อย่อย ของหัวข้อ :name ต้องไม่ซ้ำกัน'
        ],
        'btn-save' => 'บันทึก',
    ]
//
//    'status_class_' . CheckCreditStatusEnum::DRAFT => 'info',
//    'status_class_' . CheckCreditStatusEnum::PENDING_REVIEW => 'warning',
//    'status_class_' . CheckCreditStatusEnum::CONFIRM => 'success',
//    'status_class_' . CheckCreditStatusEnum::REJECT => 'danger',
//
//    'status_text_' . CheckCreditStatusEnum::DRAFT => 'ร่าง',
//    'status_text_' . CheckCreditStatusEnum::PENDING_REVIEW => 'รอตรวจสอบเครดิต',
//    'status_text_' . CheckCreditStatusEnum::CONFIRM => 'อนุมัติ',
//    'status_text_' . CheckCreditStatusEnum::REJECT => 'ไม่อนุมัติ',
];
