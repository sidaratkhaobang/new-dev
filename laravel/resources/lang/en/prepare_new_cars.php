<?php

use App\Enums\ImportCarLineStatusEnum;

return [

    'page_title' => 'รายการส่งมอบรถ',
    'status_' . ImportCarLineStatusEnum::PENDING => 'รถใหม่',
    'status_' . ImportCarLineStatusEnum::PENDING_REVIEW => 'รอตรวจสอบ',
    'status_' . ImportCarLineStatusEnum::CONFIRM_DATA => 'ยืนยันข้อมูล',
    'status_' . ImportCarLineStatusEnum::REJECT_DATA => 'แก้ไขข้อมูล',
    'status_' . ImportCarLineStatusEnum::PENDING_DELIVERY => 'รอส่งมอบ',
    'status_' . ImportCarLineStatusEnum::SUCCESS_DELIVERY => 'ส่งมอบแล้ว',
    'class_' . ImportCarLineStatusEnum::SUCCESS_DELIVERY => 'success',
    'class_' . ImportCarLineStatusEnum::PENDING => 'warning',
    'class_' . ImportCarLineStatusEnum::PENDING_DELIVERY => 'primary',


    'status_lot_PENDING' => 'รอจัดทำ',
    'class_lot_PENDING' => 'primary',
    'status_lot_SUCCESS' => 'จัดทำแล้ว',
    'class_lot_SUCCESS' => 'success',


];
