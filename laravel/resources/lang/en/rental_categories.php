<?php
use App\Enums\StatusEnum;
use App\Enums\RentalTypeEnum;
return [

    'page_title' => 'ประเภทรถงานเช่าสั้น',
    'rental_category' => 'ประเภทรถงานเช่าสั้น',
    'name' => 'ชื่อประเภท',
    'status' => 'สถานะ',
    'service_category' => 'ประเภทบริการ',
    'add_new' => 'เพิ่มประเภท',
    'total_items' => 'รายการทั้งหมด',
    'active' => 'ใช้งาน',
    'inactive' => 'ระงับ',
    'status_' .StatusEnum::ACTIVE => 'Active',
    'status_' .StatusEnum::INACTIVE => 'Inactive',
    'status_' .StatusEnum::INACTIVE => 'Inactive',
    'class_' .StatusEnum::ACTIVE => 'success',
    'class_' .StatusEnum::INACTIVE => 'secondary',
    'class_' .StatusEnum::INACTIVE  => 'secondary',

    
    'rental_type_' . RentalTypeEnum::SHORT => 'รถเช่าสั้น',
    'rental_type_' . RentalTypeEnum::LONG => 'รถเช่ายาว',
    'rental_type_' . RentalTypeEnum::REPLACEMENT => 'รถทดแทน',
    'rental_type_' . RentalTypeEnum::TRANSPORT => 'รถขนส่ง',
    'rental_type_' . RentalTypeEnum::OTHER => 'อื่นๆ',
    'rental_type_' . RentalTypeEnum::SPARE => 'รถสำรอง',
];
