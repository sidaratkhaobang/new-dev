<?php

use App\Enums\CalculateTypeEnum;

return [

    'page_title' => 'แพ็กเกจ',
    'name' => 'ชื่อแพ็กเกจ',
    'price' => 'ราคา',
    'add_new' => 'เพิ่มแพ็กเกจ',
    'sku' => 'SKU',
    'service_type' => 'รูปแบบบริการ',
    'calculate_type' => 'รูปแบบการคิดราคา',
    'hour' => 'ชั่วโมง',
    'day' => 'วัน',
    'lump_sum' => 'เหมาจ่าย',
    'standard_price' => 'ราคามาตรฐานรวม VAT (ต่อหน่วย)',
    'branch' => 'สาขา',
    'reserve_date' => 'วันที่สามารถจองได้',
    'start_booking_time' => 'เวลาที่เริ่มจองได้',
    'end_booking_time' => 'เวลาสิ้นสุดการจอง',
    'reserve_booking_duration' => 'จองล่วงหน้าขั้นต่ำ (ชั่วโมง)',
    'start_date' => 'วันที่เริ่มต้นใช้งาน',
    'end_date' => 'วันที่สิ้นสุดการใช้งาน',
    'show' => 'แสดงผล',
    'free' => 'แถมฟรี',
    'amount' => 'จำนวน',
    'product_detail' => 'ข้อมูลแพ็กเกจ',
    'is_used_application' => 'แสดงผลใน Application',
    'day_mon' => 'จ.',
    'day_tue' => 'อ.',
    'day_wed' => 'พ.',
    'day_thu' => 'พฤ.',
    'day_fri' => 'ศ.',
    'day_sat' => 'ส.',
    'day_sun' => 'อา.',
    'calculate_type_' . CalculateTypeEnum::HOURLY => 'ชั่วโมง',
    'calculate_type_' . CalculateTypeEnum::DAILY => 'วัน',
    'calculate_type_' . CalculateTypeEnum::FIXED => 'เหมาจ่าย',
    'calculate_type_' . CalculateTypeEnum::MONTHLY => 'รายเดือน',
    'save_set_price' => 'บันทึกและตั้งค่าราคา',
    'view_product_price' => 'ดูราคาแพ็กเกจ',
    'gl_account' => 'บัญชีแยกประเภท',
    'fix_return_time' => 'เวลาคืนรถ/เรือ',
    'fix_days' => 'กำหนดระยะเวลา (วัน)',
    'car_class' => 'รุ่นรถ',
    'car_type' => 'แบบรถ',
    'product_additional_existed' => 'ออฟชั่นเสริมนี้มีอยู่แล้ว',
    'status_' . STATUS_ACTIVE => 'ใช้งาน',
    'class_' . STATUS_ACTIVE => 'success',
    'status_' . STATUS_DEFAULT => 'ไม่ใช้งาน',
    'class_' . STATUS_DEFAULT => 'danger',
    'status' => 'สถานะ',
];
