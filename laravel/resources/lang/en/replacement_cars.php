<?php
use App\Enums\ReplacementCarStatusEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\ReplacementTypeEnum;

return [
    'page_title_inform' => 'แจ้งงานรถทดแทน',
    'page_title_approve' => 'อนุมัติรถทดแทน',
    'page_title' => 'ใบงานรถทดแทน',
    'info' => 'ข้อมูลใบงานรถทดแทน',
    'worksheet_no' => 'เลขที่ใบงานรถทดแทน',
    'replacement_type' => 'ประเภทงานรถทดแทน',
    'job_type' => 'ประเภทงาน',
    'job_id' => 'อ้างอิงงาน',
    'ref_no' => 'เลขที่ใบงานอ้างอิง',
    'contract_no' => 'เลขที่สัญญา',
    'license_plate' => 'ทะเบียนรถ',
    'main_license_plate' => 'ทะเบียนรถหลัก',
    'replace_license_plate' => 'ทะเบียนรถทดแทน',
    'expect_date' => 'วันเวลาที่ต้องการขอรถ / รับรถทดแทน',
    'job_date_time' => 'วันที่ / เวลาของงาน',
    'is_need_driver' => 'ต้องการพนักงานขับรถ',
    'is_need_slide' => 'ต้องการรถยก',
    'place' => 'สถานที่',
    'customer_name' => 'ลูกค้า',
    'tel' => 'โทร',
    'remark' => 'หมายเหตุ',
    'replacement_info' => 'ข้อมูลรถทดแทน',
    'created_by' => 'ผู้จัดทำ',
    'type_' . ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN => 'ส่งรถทดแทนรับรถหลัก',
    'type_' . ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE => 'ส่งรถหลักรับรถทดแทน',
    'type_' . ReplacementTypeEnum::RECEIVE_MAIN => 'รับรถหลัก',
    'type_' . ReplacementTypeEnum::SEND_MAIN => 'ส่งรถหลัก',
    'type_' . ReplacementTypeEnum::SEND_REPLACE => 'ส่งรถทดแทน',
    'type_' . ReplacementTypeEnum::RECEIVE_REPLACE => 'รับรถทดแทน',
    // 'type_' . ReplacementTypeEnum::CHANGE_REPLACE => 'เปลี่ยนรถทดแทน',

    'job_type_' . ReplacementJobTypeEnum::ACCIDENT => 'อุบัติเหตุ',
    'job_type_' . ReplacementJobTypeEnum::REPAIR => 'ซ่อมบำรุง',
    'document' => 'เอกสารเพิ่มเติม (ขนาดไม่เกิน 10 MB)',

    'status_' . ReplacementCarStatusEnum::PENDING_INSPECT => 'รอตรวจสอบ',
    'class_' . ReplacementCarStatusEnum::PENDING_INSPECT => 'primary',

    'status_' . ReplacementCarStatusEnum::PENDING => 'รอดำเนินการ',
    'class_' . ReplacementCarStatusEnum::PENDING => 'primary',

    'status_' . ReplacementCarStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'class_' . ReplacementCarStatusEnum::PENDING_REVIEW => 'warning',

    'status_' . ReplacementCarStatusEnum::IN_PROCESS => 'อยู่ระหว่างดำเนินการ',
    'class_' . ReplacementCarStatusEnum::IN_PROCESS => 'warning',

    'status_' . ReplacementCarStatusEnum::APPROVE => 'อนุมัติ',
    'class_' . ReplacementCarStatusEnum::APPROVE => 'success',

    'status_' . ReplacementCarStatusEnum::REJECT => 'ไม่อนุมัติ',
    'class_' . ReplacementCarStatusEnum::REJECT => 'danger',

    'status_' . ReplacementCarStatusEnum::CANCEL => 'ยกเลิก',
    'class_' . ReplacementCarStatusEnum::CANCEL => 'secondary',

    'status_' . ReplacementCarStatusEnum::COMPLETE => 'ดำเนินการเสร็จสิ้น',
    'class_' . ReplacementCarStatusEnum::COMPLETE => 'primary',

    'accident_history' => 'ประวัติอุบัติเหตุ',
    'accident_date' => 'วันที่เกิดอุบัติเหตุ',
    'accident_detail' => 'รายละเอียดอุบัติเหตุ',

    'repair_history' => 'ประวัติซ่อมบำรุง',
    'repair_date' => 'วันที่ซ่อมบำรุง',
    'repair_detail' => 'รายละเอียดการซ่อมบำรุง',

    'car_detail_main' => 'ข้อมูลรถหลัก',
    'car_detail_replace' => 'ข้อมูลรถแทน',

    'accessory_detail' => 'ข้อมูลอุปกรณ์เสริม',
    'accessory' => 'อุปกรณ์เสริม',
    'amount' => 'จำนวน',
    'install_date' => 'วันที่ติดตั้ง',

    'condition' => 'เงื่อนไขบริการ',
    'main_page_title' => 'งานรถทดแทนทั้งหมด',
    'spec_low_reason' => 'เหตุผลรถทดแทนสเปคต่ำกว่า',
    'spec_lower' => 'สเปครถต่ำกว่า',

    'replacement_place' => 'สถานที่จริงที่ต้องการขอรถ / รับรถทดแทน',
    'replacement_date' => 'วันเวลาจริงที่ต้องการขอรถ / รับรถทดแทน',
    'class' => 'รุ่น',
    'color' => 'สี',
    'no_same_spec' => 'ไม่มีรถทดแทนสเปคเทียบเท่า/สูงกว่า',
    'reject_reason' => 'เหตุผลการไม่อนุมัติ',
    'replacement_history' => 'ประวัติรถทดแทน',
    'car_main_page_title' => 'รถทดแทนทั้งหมด',

    'worksheet_send_type' => 'พิมพ์ใบส่งรถ',
    'worksheet_receive_type' => 'พิมพ์ใบรับรถ',

    'worksheet_name_SEND_' . ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN => 'ใบส่งรถทดแทน',
    'worksheet_name_SEND_' . ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE => 'ใบส่งรถหลัก',
    'worksheet_name_SEND_' . ReplacementTypeEnum::SEND_MAIN => 'ใบส่งรถหลัก',
    'worksheet_name_SEND_' . ReplacementTypeEnum::SEND_REPLACE => 'ใบส่งรถทดแทน',
    'worksheet_name_RECEIVE_' . ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN => 'ใบรับรถหลัก',
    'worksheet_name_RECEIVE_' . ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE => 'ใบรับรถทดแทน',
    'worksheet_name_RECEIVE_' . ReplacementTypeEnum::RECEIVE_MAIN => 'ใบรับรถหลัก',
    'worksheet_name_RECEIVE_' . ReplacementTypeEnum::RECEIVE_REPLACE => 'ใบรับรถทดแทน',
];