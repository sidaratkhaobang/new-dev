<?php

use App\Enums\DiscountModeEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\PatternCodeEnum;
use App\Enums\PromotionTypeEnum;

return [
    'page_title' => 'ส่วนลดเช่าระยะสั้น',
    'name' => 'ชื่อส่วนลด',
    'code' => 'รหัสส่วนลด',
    'branch' => 'สาขา',
    'status' => 'สถานะ',
    'discount_type' => 'ประเภทการลดราคา',
    'discount_mode' => 'รูปแบบการลดราคา',
    'discount_amount' => 'จำนวนที่ลด',
    'discount_day' => 'จำนวนวัน/ชั่วโมงที่ลด',
    'discount_day_helper' => '(หากไม่ระบุจะลดตลอดการเช่า)',
    'priority' => 'ลำดับการแสดงผล',
    'free_product' => 'สินค้าที่แถม',
    'free_car_class' => 'รุ่นรถที่แถม',
    'free_product_additional' => 'ออฟชั่นเสริมที่แถม',
    'promotion_effective' => 'สินค้าที่ลด',
    'condition_table' => 'เงื่อนไข',
    'min_total' => 'ราคาขั้นต่ำ',
    'min_hours' => 'จำนวนชั่วโมงขั้นต่ำ',
    'min_day' => 'จำนวนวันขั้นต่ำ',
    'min_distance' => 'ระยะทางขั้นต่ำ (กิโลเมตร)',
    'car_class' => 'รุ่นรถ',
    'customer_group' => 'กลุ่มลูกค้า',
    'product' => 'แพ็กเกจ',
    'sale' => 'พนักงานขาย',
    'start_date' => 'วันที่เริ่มต้นการใช้งาน',
    'end_date' => 'วันที่สิ้นสุดการใช้งาน',
    'add_new' => 'เพิ่มส่วนลด',
    'save_code_coupon' => 'บันทึก และ สร้าง คูปอง',
    'save_code_voucher' => 'บันทึก และ สร้าง Voucher',
    'promotion_coupon' => 'โปรโมชั่น/คูปอง',
    'promotion' => 'โปรโมชั่น',
    'voucher' => 'Voucher',
    'name_coupon' => 'ชื่อ โปรโมชั่น',
    'name_voucher' => 'ชื่อ Voucher',
    'code_coupon' => 'รหัส โปรโมชั่น',
    'code_voucher' => 'รหัส Voucher',
    'view_promotion_code' => 'ดูคูปอง',
    'total_items' => 'รายการทั้งหมด',
    'promotion_type' => 'ประเภทส่วนลด',
    'coupon_type' => 'ประเภทคูปอง',
    'voucher_type' => 'ประเภท Voucher',
    'voucher_type_' . BOOL_FALSE => 'Voucher ธรรมดา',
    'voucher_type_' . BOOL_TRUE => 'Voucher เเพ็กเกจ',

    'check' => 'ตรวจสอบ',
    'no_check' => 'ไม่ตรวจสอบ',

    'discount_type_' . DiscountTypeEnum::PERCENT => 'เปอร์เซ็นต์',
    'discount_type_' . DiscountTypeEnum::AMOUNT => 'ลดเป็นจำนวนเงิน',
    'discount_type_' . DiscountTypeEnum::FIXED_PRICE => 'Fix ราคา',
    'discount_type_' . DiscountTypeEnum::FREE_PRODUCT => 'แถมสินค้า',
    'discount_type_' . DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT => 'ฟรีออฟชั่นเสริม',
    'discount_type_' . DiscountTypeEnum::FREE_CAR_CLASS => 'ฟรีรุ่นรถ',

    'discount_type_unit_' . DiscountTypeEnum::PERCENT => '%',
    'discount_type_unit_' . DiscountTypeEnum::AMOUNT => 'บาท',
    'discount_type_unit_' . DiscountTypeEnum::FIXED_PRICE => 'บาท',
    'discount_type_unit_' . DiscountTypeEnum::FREE_PRODUCT => '',
    'discount_type_unit_' . DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT => '',
    'discount_type_unit_' . DiscountTypeEnum::FREE_CAR_CLASS => 'วัน/ชั่วโมง',

    'discount_mode_' . DiscountModeEnum::ALL => 'ลดทั้งหมดทุกสินค้า',
    'discount_mode_' . DiscountModeEnum::TRANSACTION => 'ลดรายสินค้า',

    'status_' . STATUS_ACTIVE => 'ใช้งาน',
    'status_' . STATUS_INACTIVE => 'ระงับ',
    'status_class_' . STATUS_ACTIVE => 'success',
    'status_class_' . STATUS_INACTIVE => 'danger',

    // Promotion Code
    'page_title_code' => 'คูปอง',
    'coupon' => 'คูปอง',
    'coupon_code' => 'รหัสคูปอง',
    'promotion_code' => 'Code',
    'pattern_code' => 'รูปแบบ Code',
    'pattern' => 'Pattern',
    'random' => 'สุ่ม',
    'prefix_code' => 'Prefix Code',
    'amount_code' => 'จำนวน Code',
    'amount_code_pack' => 'จำนวน Code/แพ็ก',
    'use_active' => 'การใช้งาน',
    'reuse_code' => 'การใช้ซ้ำ',
    'yes' => 'ได้',
    'no' => 'ไม่ได้',
    'selling_price' => 'ราคาขาย',
    'code_digit' => 'จำนวนหลัก/ตัวอักษร',
    'padding' => 'จำนวนหลัก running number',
    'quota' => 'จำนวน/ครั้ง',
    'build' => 'สร้างเอง',
    'code_files' => 'เลือกไฟล์ Code (ขนาดไฟล์ไม่เกิน 10 MB)',
    // 'package_amount' => 'จำนวนแพ็กเกจ',
    'package_amount' => 'จำนวน Voucher ภายในแพ็กเกจ',

    'section_title_main' => 'ข้อมูลสำคัญ',
    'section_title_pattern' => 'กรณีรูปแบบคูปอง Pattern',
    'section_title_random' => 'กรณีรูปแบบคูปองสุ่ม',

    'promotion_type' => 'ประเภทโปรโมชัน',
    'promotion_type_' . PromotionTypeEnum::PROMOTION => 'โปรโมชัน',
    'promotion_type_' . PromotionTypeEnum::COUPON => 'คูปอง',
    'promotion_type_' . PromotionTypeEnum::VOUCHER => 'บัตรกำนัลแทนเงินสด',
    'promotion_type_' . PromotionTypeEnum::PARTNER => 'Partner',

    'pattern_' . PatternCodeEnum::PATTERN => 'Pattern',
    'pattern_' . PatternCodeEnum::RANDOM => 'สุ่ม',

    'badge_class_' . BOOL_FALSE => 'bg-black-25',
    'badge_class_' . BOOL_TRUE => 'bg-success',

    'can_reuse' => 'การใช้ซ้ำ',
    'can_reuse_' . BOOL_TRUE => 'ใช้ซ้ำได้',
    'can_reuse_' . BOOL_FALSE => 'ไม่ได้',

    'sold_date' => 'วันที่ขาย',
    'is_sold' => 'การขาย',
    'is_sold_' . BOOL_FALSE => 'ยังไม่ขาย',
    'is_sold_' . BOOL_TRUE => 'ขายแล้ว',

    'use_date' => 'วันที่ใช้งาน',
    'is_used' => 'การใช้งาน',
    'is_used_' . BOOL_FALSE => 'ยังไม่ถูกใช้',
    'is_used_' . BOOL_TRUE => 'ถูกใช้งานแล้ว',

    'incompatible_section' => 'ข้อห้ามโปรโมชัน',
    'incompatible_promotions' => 'ห้ามใช้ร่วมกับโปรโมชัน',

    'customer_id' => 'รหัสอ้างอิงลูกค้า',
    'id' => 'รหัสอ้างอิงโปรโมชัน',
    'amount' => 'จำนวนเงิน',
    'start_sale_date' => 'วันที่เริ่มจำหน่าย',
    'end_sale_date' => 'วันที่สิ้นสุดการจำหน่าย',
    'customer_sender_id' => 'รหัสลูกค้า(ผู้ส่ง)',
    'customer_receiver_id' => 'รหัสลูกค้า(ผู้รับ)',

    'branch_expired' => 'สาขาบันทึกรายได้ตอน Voucher หมดอายุ',
    'check_customer_address' => 'อ้างอิงที่อยู่ลูกค้า',
    'customer_billing_address_id' => 'รหัสอ้างอิงที่อยู่ลูกค้า',

];
