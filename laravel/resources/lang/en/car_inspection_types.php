<?php

use App\Enums\InspectionTypeEnum;
use App\Enums\TransferReasonEnum;

return [
    'page_title' => 'ตั้งค่าประเภทงานตรวจ',
    'name' => 'ชื่อฟอร์มตรวจรถ',
    'form_name' => 'ฟอร์มตรวจรถ',
    'inspect_type_name' => 'ชื่อประเภทงานตรวจรถ',
    'inspect_type' => 'ประเภทงานตรวจรถ',
    'car_type' => 'ใช้งานกับประเภทรถ',
    'rental_type' => 'ประเภทงานเช่า',
    'seq' => 'ลำดับหัวข้อ',
    'section_topic' => 'หัวข้อรายการตรวจเช็ค',
    'seq_inspection' => 'ลำดับการตรวจ',
    'list_question' => 'รายการคำถาม',
    'list_inspection' => 'รายการตรวจ',
    'confirm' => 'ยืนยันการบันทึกข้อมูล',
    'rent_product' => 'Product งานเช่า',

    'inspection_detail' => 'ข้อมูลงานตรวจรถ',
    'question_detail' => 'ข้อมูลคำถาม',
    'question_additional_detail' => 'ข้อมูลคำถามเพิ่มเติม',

    'add_new' => 'สร้างฟอร์มตรวจรถ',
    'add_topic' => 'เพิ่มหัวข้อรายการตรวจเช็ค',
    'is_main' => 'เป็นสาขาหลัก',
    'is_main_0' => 'สาขาย่อย',
    'is_main_1' => 'สาขาหลัก',

    'class_0' => 'secondary',
    'class_1' => 'success',
    'view' => 'ดูข้อมูล',
    'edit' => 'แก้ไข',
    'delete' => 'ลบ',
    'copy_form' => 'คัดลอกใบงาน',
    'customer_signature_out' => 'ลายเซ็นลูกค้า/อู่ ตอนส่งมอบ',
    'customer_signature_in' => 'ลายเซ็นลูกค้า/อู่ ตอนรับคืน',
    'use_form' => 'ฟอร์มที่ใช้',
    'responsible_department' => 'แผนกที่รับผิดชอบ',
    'responsible_section' => 'ฝ่ายที่รับผิดชอบ',
    'inspector_signature' => 'ลายเซ็นผู้ตรวจ',
    'photo' => 'ถ่ายรูป',
    'condition' => 'เงื่อนไข',
    'inspection_seq_out' => 'ลำดับการตรวจสอบ (ขาออก)',
    'inspection_seq_in' => 'ลำดับการตรวจสอบ (ขาเข้า)',
    'inspection_seq_car' => 'ลำดับการตรวจรถ',
    'inspection_form' => 'ฟอร์มที่ใช้ตรวจ',
    'inspection_team' => 'ทีมที่ตรวจหลัก',
    'take_photo' => 'ทีมที่ตรวจหลัก',
    'car_type_name' => 'ประเภทรถที่ใช้',
    'role' => 'บทบาท',
    'send_mobile' => 'ต้องการส่งไปที่ Moblie',
    'dpf_oil' => 'น้ำยา DPF',

    'status_' . STATUS_ACTIVE => 'ต้องการ',
    'status_' . STATUS_DEFAULT => 'ไม่ต้องการ',

    'status_condition_name_' . TransferReasonEnum::DELIVER_CUSTOMER => 'ส่งมอบลูกค้า / นำรถออกจากคลัง',
    'status_condition_name_' . TransferReasonEnum::RECEIVE_WAREHOUSE => 'รับเข้าคลัง',
    'status_condition_name_' . TransferReasonEnum::DELIVER_GARAGE => 'ส่งอู่',
    'status_condition_name_' . TransferReasonEnum::RECEIVE_GARAGE => 'รับเข้าอู่',
];
