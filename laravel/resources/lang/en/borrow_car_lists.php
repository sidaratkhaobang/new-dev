<?php

use App\Enums\BorrowCarEnum;
use App\Enums\CarEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\StorageEnum;
use App\Enums\StorageLocationEnum;

return [
    'page_title' => 'รถยืมทั้งหมด',
    'title' => 'รถยนต์',
    'add_car' => 'เพิ่มรถยนต์',
    'license_plate' => 'ทะเบียนรถ',
    'total_items' => 'ข้อมูลรถทั้งหมดในคลัง',
    'status_' . BorrowCarEnum::PENDING_DELIVERY => 'รอรับรถยืม',
    'status_' . CarEnum::READY_TO_USE => 'รถพร้อมใช้',
    'status_' . BorrowCarEnum::IN_PROCESS => 'ระหว่างยืม',
    'status_' . BorrowCarEnum::SUCCESS => 'จบงานการยืมรถ',
    'class_' . CarEnum::READY_TO_USE => 'success',
    'class_' . BorrowCarEnum::PENDING_DELIVERY => 'warning',
    'class_' . BorrowCarEnum::IN_PROCESS => 'warning',
    'class_' . BorrowCarEnum::SUCCESS => 'success',
    'rental_type' => 'ประเภทรถ',
    'register_date' => 'วันที่จดทะเบียน',
    'start_system_date' => 'วันที่เริ่มใช้งานในระบบ',
    'car_storage_age' => 'อายุรถในคลัง',
    'car_age' => 'อายุรถ',
    'slot' => 'ช่องจอด',
    'car_detail' => 'ข้อมูลรถยนต์พื้นฐาน',
    'borrow_car_detail' => 'รายละเอียดรถยืม',
    'car_class' => 'รุ่น',
    'car_brand' => 'ยี่ห้อ',

    'car_storage_' . StorageLocationEnum::TRUE_LEASING => 'ทรูลีซซิ่ง',
    'car_park_' . StorageEnum::IN_GARAGE => 'จัดเก็บในคลัง',
    'car_park_' . StorageEnum::OUT_GARAGE => 'จัดเก็บนอกคลัง',

    'rental_type_' . RentalTypeEnum::SHORT => 'รถเช่าสั้น',
    'rental_type_' . RentalTypeEnum::LONG => 'รถเช่ายาว',
    'rental_type_' . RentalTypeEnum::REPLACEMENT => 'รถทดแทน',
    'rental_type_' . RentalTypeEnum::TRANSPORT => 'รถขนส่ง',
    'rental_type_' . RentalTypeEnum::OTHER => 'อื่นๆ',
    'rental_type_' . RentalTypeEnum::SPARE => 'รถสำรอง',



    'status_' . BorrowCarEnum::PENDING_DELIVERY . '_class' => 'warning',
    'status_' . BorrowCarEnum::PENDING_DELIVERY . '_text' => 'รอรับรถยืม',

    'status_' . BorrowCarEnum::IN_PROCESS . '_class' => 'warning',
    'status_' . BorrowCarEnum::IN_PROCESS . '_text' => 'ระหว่างยืม',

    'status_' . CarEnum::READY_TO_USE . '_class' => 'primary',
    'status_' . CarEnum::READY_TO_USE . '_text' => 'รถพร้อมใช้',

    'status_' . BorrowCarEnum::SUCCESS . '_class' => 'success',
    'status_' . BorrowCarEnum::SUCCESS . '_text' => 'จบงานการยืมรถ',
];
