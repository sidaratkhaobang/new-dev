<?php

use App\Enums\CheckCreditStatusEnum;
use App\Enums\ContractEnum;
use App\Enums\ContractSignerSideEnum;
use App\Models\Contracts;

return [
    'index' => [
        'title' => [
            'new_customer' => 'แจ้งตรวจเครดิตลูกค้าใหม่',
            'approve' => 'อนุมัติเครดิต',
        ],
        'page_title' => 'แจ้งตรวจเครดิตลูกค้าใหม่',
        'total_items' => 'รายการทั้งหมด',
        'btn-create-page' => 'เพิ่ม',
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
        'btn-save' => 'บันทึก',
        'section_table' => [
            'title' => 'เอกสารสำหรับตรวจสอบเครดิต',
            'btn-add-file' => 'เพิ่ม',
            'file_name' => 'ชื่อเอกสาร',
            'extension_name' => 'ไฟล์',
        ],
        'result_check_credit' => 'ผลการตรวจสอบเครดิต',
        'approved_amount' => 'วงเงินที่อนุมัติ (บาท)',
        'approved_days' => 'วันที่อนุมัติเครดิต (วัน)',
        'reason' => 'เหตุผลที่ไม่อนุมัติ',
    ],

    'status_text_' . ContractEnum::REQUEST_CONTRACT => 'ขอจัดทำสัญญา',
    'status_text_' . ContractEnum::ACTIVE_CONTRACT => 'จัดทำสัญญา',
    'status_text_' . ContractEnum::SEND_OFFER_SIGN => 'ส่งเสนอลงนาม',
    'status_text_' . ContractEnum::SEND_CUSTOMER_SIGN => 'ส่งให้ลูกค้าเซ็น',
    'status_text_' . ContractEnum::ACTIVE_BETWEEN_CONTRACT => 'อยู่ระหว่างสัญญา',

    'status_text_' . ContractEnum::REQUEST_CHANGE_ADDRESS => 'ขอเปลี่ยนแปลงที่อยู่',
    'status_text_' . ContractEnum::REQUEST_CHANGE_USER_CAR => 'ขอเปลี่ยนแปลงชื่อผู้ใช้รถ',
    'status_text_' . ContractEnum::REQUEST_TRANSFER_CONTRACT => 'ขอโอนย้ายบริษัทผู้เช่า',

    'status_text_' . ContractEnum::REJECT_REQUEST => 'ไม่ยืนยันการเปลี่ยนแปลง',

    'status_text_' . ContractEnum::CANCEL_CONTRACT => 'ยกเลิกสัญญา',
    'status_text_' . ContractEnum::CLOSE_CONTRACT => 'ปิดสัญญา',

    'status_text_' . ContractEnum::REJECT => 'ผ่าน',
    'status_text_' . ContractEnum::CONFIRM => 'ไม่ผ่าน',

    'status_text_' . ContractEnum::START_RENT_PICKUP_DATE => 'ตั้งแต่วันที่เริ่มเช่า',
    'status_text_' . ContractEnum::START_RENT_RETURN_DATE => 'ตั้งแต่วันที่รับรถ',

    'status_text_' . ContractEnum::END_RENT_EXPIRE_DATE => 'ตั้งแต่วันที่สิ้นสุดการเช่า',
    'status_text_' . ContractEnum::END_RENT_RETURN_DATE => 'ตั้งแต่วันที่คืนรถ',

    'status_class_' . ContractEnum::REQUEST_CONTRACT => 'primary',
    'status_class_' . ContractEnum::ACTIVE_CONTRACT => 'warning',
    'status_class_' . ContractEnum::SEND_OFFER_SIGN => 'warning',
    'status_class_' . ContractEnum::SEND_CUSTOMER_SIGN => 'warning',
    'status_class_' . ContractEnum::ACTIVE_BETWEEN_CONTRACT => 'success',

    'status_class_' . ContractEnum::REQUEST_CHANGE_ADDRESS => 'primary',
    'status_class_' . ContractEnum::REQUEST_CHANGE_USER_CAR => 'primary',
    'status_class_' . ContractEnum::REQUEST_TRANSFER_CONTRACT => 'primary',

    'status_class_' . ContractEnum::REJECT_REQUEST => 'danger',

    'status_class_' . ContractEnum::CANCEL_CONTRACT => 'danger',
    'status_class_' . ContractEnum::CLOSE_CONTRACT => 'danger',

    'status_class_' . ContractEnum::REJECT => 'danger',
    'status_class_' . ContractEnum::CONFIRM => 'success',

    'contract_side' => 'ฝ่ายสัญญา',
    'singer_' . ContractSignerSideEnum::HOST => 'ผู้ให้เช่า',
    'singer_' . ContractSignerSideEnum::RENTER => 'ผู้เช่า',
    'is_attorney' => 'เป็นผู้ได้รับมอบอำนาจ',
    'contract' => 'สัญญา',
    'have_fine' => 'มีค่าปรับ',
    'car_user' =>  'ผู้ใช้รถ',
    'customer_mobile_number' => 'เบอร์โทร',
];
