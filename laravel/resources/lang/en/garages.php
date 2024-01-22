<?php

use App\Enums\CarTypeAccidentEnum;
use App\Enums\GarageTypeEnum;
use App\Enums\ZoneEnum;
use App\Enums\ZoneTypeEnum;

return [

    'page_title' => 'อู่ทั้งหมด',
    'add_new' => 'เพิ่มข้อมูล',
    'name' => 'ชื่ออู่',
    'total_items' => 'รายการทั้งหมด',
    'images' => 'รูปภาพประกอบ (ขนาดไฟล์ไม่เกิน 10 MB)',
    'garage_detail' => 'ข้อมูลอู่',
    'garage_type' => 'ประเภทอู่',
    'email' => 'อีเมล',
    'tel' => 'เบอร์โทร',
    'car_type' => 'ประเภทรถ',
    'EMCS' => 'EMCS',
    'onsite_service' => 'บริการนอกสถานที่',
    'insurance' => 'รับงานบริษัทประกัน',
    'address' => 'ที่อยู่',
    'sector' => 'ภาค',
    'province' => 'จังหวัด',
    'amphure' => 'อำเภอ/เขต',
    'district' => 'ตำบล/แขวง',
    'zip_code' => 'รหัสไปรษณีย์',
    'coordinator_name' => 'ชื่อผู้ประสานงานอู่',
    'status' => 'สถานะ',
    'garage' => 'อู่',
    'province_district' => 'จังหวัด/อำเภอ',
    'onsite_install_service' => 'บริการติดตั้งนอกสถานที่',
    'status_' . STATUS_ACTIVE => 'ใช่',
    'status_' . STATUS_DEFAULT => 'ไม่ใช่',

    'status_job_' . STATUS_ACTIVE => 'รับงาน',
    'status_job_' . STATUS_DEFAULT => 'ไม่รับงาน',
    'class_job_' . STATUS_ACTIVE => 'success',
    'class_job_' . STATUS_DEFAULT => 'warning',

    'garage_type_' . GarageTypeEnum::GENERAL_GARAGE => 'อู่ทั่วไป',
    'garage_type_' . GarageTypeEnum::GLASS_GARAGE => 'ร้านกระจก',


    'car_type_' . CarTypeAccidentEnum::SEDAN_CAR => 'รถเก๋ง',
    'car_type_' . CarTypeAccidentEnum::PICKUP_CAR => 'รถกระบะ',
    'car_type_' . CarTypeAccidentEnum::VAN => 'รถตู้',
    'car_type_' . CarTypeAccidentEnum::BUS => 'รถบัส',
    'car_type_' . CarTypeAccidentEnum::ALL_TYPE => 'รถยนต์ทุกชนิด',
    'car_type_' . CarTypeAccidentEnum::TRUCK => 'รถบรรทุก',
    'car_type_' . CarTypeAccidentEnum::GENERAL => 'งานทั่วไป',


    'zone_type_' . ZoneEnum::NORTH => 'ภาคเหนือ',
    'zone_type_' . ZoneEnum::NORTHEAST => 'ภาคตะวันออกเฉียงเหนือ',
    'zone_type_' . ZoneEnum::WESTERN => 'ภาคตะวันตก',
    'zone_type_' . ZoneEnum::CENTRAL => 'ภาคกลาง',
    'zone_type_' . ZoneEnum::SOUTH => 'ภาคใต้',
];