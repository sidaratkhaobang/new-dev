<?php

use App\Enums\BorrowCarEnum;
use App\Enums\BorrowTypeEnum;

return [

    'page_title' => 'งานยืมรถ',
    'borrow_request' => 'แจ้งขอยืมรถ',
    'total_items' => 'รายการทั้งหมด',
    'search' => 'ค้นหา',
    'add_new' => 'เพิ่มข้อมูล',
    'add_sheet' => 'เพิ่มใบงานยืมรถ',
    'view_sheet' => 'ดูใบงานยืมรถ',
    'edit_sheet' => 'แก้ไขใบงานยืมรถ',
    'approve_sheet' => 'อนุมัติงานขอยืมรถ',
    'borrow_sheet' => 'งานขอยืมรถ',
    'view_borrow_sheet' => 'ดูงานขอยืมรถ',
    'edit_borrow_sheet' => 'แก้ไขงานขอยืมรถ',
    'username' => 'ชื่อผู้ใช้งาน',
    'name' => 'ชื่อ',
    'email' => 'อีเมล',
    'department' => 'แผนก',
    'role' => 'ตำแหน่ง',
    'branch' => 'สาขา',
    'worksheet' => 'เลขที่ใบงานยืมรถ',
    'creator' => 'ชื่อผู้จัดทำ',
    'created_date' => 'วันที่จัดทำ',
    'borrow_type' => 'ประเภทการยืม',
    'borrow_reason' => 'วัตถุประสงค์ในการยืม',
    'remark' => 'หมายเหตุ',
    'borrow_detail' => 'ข้อมูลใบงานยืมรถ',
    'borrower_detail' => 'ข้อมูลผู้ยืมใช้รถ',
    'borrow_car_detail' => 'ข้อมูลรถที่ต้องการยืม',
    'borrow_place_detail' => 'ข้อมูลการรับ-ส่งรถยืม',
    'place' => 'สถานที่',
    'contact' => 'ผู้ติดต่อ',
    'tel' => 'โทร',
    'fullname' => 'ชื่อ-นามสกุล',
    'start_date' => 'วันที่ เวลาที่เริ่มการยืม',
    'end_date' => 'วันที่ เวลาสิ้นสุดการยืม',
    'start_borrow_date' => 'วันที่ที่เริ่มการยืม',
    'end_borrow_date' => 'วันที่สิ้นสุดการยืม',
    'delivery_date' => 'วันที่ส่งมอบ',
    'license_plate' => 'ทะเบียนรถ',
    'borrower' => 'ผู้ยืม',
    'status' => 'สถานะ',
    'pickup_place' => 'สถานที่ส่งรถ',
    'return_place' => 'สถานที่รับรถ',
    'driving_job' => 'ใบงานผู้ขับรถ',
    'worksheet_print' => 'พิมพ์ใบยืมรถ',
    'type_' . BorrowTypeEnum::BORROW_EMPLOYEE => 'พนักงานยืม',
    'type_' . BorrowTypeEnum::BORROW_OTHER => 'ยืมให้ผู้อื่น',


    'status_' . BorrowCarEnum::PENDING_REVIEW . '_class' => 'warning',
    'status_' . BorrowCarEnum::PENDING_REVIEW . '_text' => 'รออนุมัติการยืมรถ',

    'status_' . BorrowCarEnum::CONFIRM . '_class' => 'success',
    'status_' . BorrowCarEnum::CONFIRM . '_text' => 'อนุมัติการยืมรถ',

    'status_' . BorrowCarEnum::REJECT. '_class' => 'danger',
    'status_' . BorrowCarEnum::REJECT . '_text' => 'ไม่อนุมัติการยืมรถ',

    'status_' . BorrowCarEnum::PENDING_DELIVERY . '_class' => 'primary',
    'status_' . BorrowCarEnum::PENDING_DELIVERY . '_text' => 'รอส่งมอบ',

    'status_' . BorrowCarEnum::IN_PROCESS . '_class' => 'warning',
    'status_' . BorrowCarEnum::IN_PROCESS . '_text' => 'อยู่ระหว่างการยืมรถ',

    'status_' . BorrowCarEnum::SUCCESS . '_class' => 'primary',
    'status_' . BorrowCarEnum::SUCCESS . '_text' => 'จบงานการยืมรถ',
];
