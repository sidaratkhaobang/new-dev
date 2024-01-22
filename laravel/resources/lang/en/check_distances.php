<?php

use App\Enums\CheckDistanceTypeEnum;

return [
    'page_title' => 'รายการเช็กระยะ',
    'distance_table' => 'ตารางระยะทาง/ระยะเวลา',
    'distance' => 'ระยะทาง(กม.)',
    'month' => 'ระยะเวลา(เดือน)',
    'amount' => 'จำนวนรายการ',
    'check_distance_table' => 'รายการตรวจเช็กตามระยะทาง',
    'code_name' => 'รหัส/รายการซ่อม',
    'is_check' => 'วิธีการตรวจเช็ก',
    'price' => 'ราคามาตรฐาน',
    'remark' => 'หมายเหตุ',
    'copy_btn' => 'คัดลอกข้อมูล',
    'copy' => 'คัดลอก',
    'car_class_model' => 'ยี่ห้อ / รุ่นรถยนต์ต้นแบบ',


    'type_text_' . CheckDistanceTypeEnum::REPAIR => 'ซ่อม',
    'type_text_' . CheckDistanceTypeEnum::CHANGE => 'เปลี่ยน',
    'type_text_' . CheckDistanceTypeEnum::SERVICE_CHARGE => 'ค่าบริการ',
    'type_text_' . CheckDistanceTypeEnum::CHECK => 'ตรวจเช็ก',
    'type_text_' . CheckDistanceTypeEnum::ADJUST => 'ปรับตั้ง',
    'type_text_' . CheckDistanceTypeEnum::CLEAN => 'ทำความสะอาด',
    'type_text_' . CheckDistanceTypeEnum::MODIFY => 'แก้ไข',
    'type_text_' . CheckDistanceTypeEnum::PUTTER_OUT => 'ดับไฟ',
    'type_text_' . CheckDistanceTypeEnum::RECAP => 'ปะยาง',
    'type_text_' . CheckDistanceTypeEnum::FREE_SERVICE => 'บริการฟรี',
    'type_text_' . CheckDistanceTypeEnum::FREE_WAGE => 'ฟรีค่าแรง',
];
