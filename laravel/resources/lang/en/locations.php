<?php
use App\Enums\StatusEnum;
return [

    'page_title' => 'สถานที่',
    'name' => 'ชื่อสถานที่',
    'location_group' => 'กลุ่มสถานที่',
    'status' => 'สถานะ',
    'province' => 'จังหวัด',
    'add_new' => 'เพิ่มสถานที่',
    'total_items' => 'รายการทั้งหมด',
    'car' => 'รถ',
    'boat' => 'เรือ',
    'lat' => 'ละติจูด',
    'lng' => 'ลองติจูด',
    'transportation_type' => 'ประเภทยานพาหนะ',

    'status_yes_no_' .StatusEnum::ACTIVE => 'ใช่',
    'status_yes_no_' .StatusEnum::INACTIVE => 'ไม่ใช่',
    'car' => 'รถ',
    'boat' => 'เรือ',
    'status_' .StatusEnum::ACTIVE => 'Active',
    'status_' .StatusEnum::INACTIVE => 'Inactive',
    'status_' .StatusEnum::INACTIVE => 'Inactive',
    'class_' .StatusEnum::ACTIVE => 'success',
    'class_' .StatusEnum::INACTIVE => 'secondary',
    'class_' .StatusEnum::INACTIVE  => 'secondary',
];
