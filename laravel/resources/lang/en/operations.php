<?php

use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\OperationKeyAddressEnum;
use App\Enums\OperationKeyEnum;
use App\Enums\OperationTransferTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\SelfDriveTypeEnum;

return [
    'page_title' => 'Operation งานเช่าสั้น',
    'rental_no' => 'เลขที่ใบเช่า',
    'branch' => 'สาขา',
    'customer' => 'ลูกค้า',
    'rental_type' => 'ประเภทงาน',
    'rantal_date' => 'วันที่เช่า',
    'status' => 'สถานะ',
    'contract_no' => 'เลขที่ใบสัญญา',
    'receipt_no' => 'เลขที่ใบเสร็จ',
    'contract_file' => 'ไฟล์เลขที่ใบสัญญา',
    'receipt_file' => 'ไฟล์เลขที่ใบเสร็จ',
    'driver_info' => 'ข้อมูลพนักงานขับรถ',
    'driver_name' => 'ชื่อคนขับ',
    'driver_sheet' => 'ใบงาน พขร.',
    'atk' => 'ผลตรวจ ATK',
    'alcohol' => 'ผลตรวจแอลกอฮอล์',
    'alcohol_val' => 'ค่าแอลกอฮอล์',
    'key' => 'กุญแจ',
    'key_address' => 'ที่อยู่กุญแจ',
    'car_transfer_sheet' => 'ใบนำรถเข้าออก',
    'inspection_sd' => 'เอกสารตรวจ SD',
    'inspection_sheet' => 'ใบตรวจรถ',
    'use_car_sheet' => 'ใบรายงานการใช้รถ',
    'pickup_time' => 'เวลารับกุญแจ',
    'return_time' => 'เวลาคืนกุญแจ',
    'accessory' => 'ข้อมูลอุปกรณ์เพิ่มเติม',
    'time_info' => 'ข้อมูลเวลา',
    'estimate' => 'เวลาประมาณการ (Estimate)',
    'actual' => 'เวลาทำงาน (Actual)',
    'time_info' => 'ข้อมูลเวลา',
    'prepare_date' => 'เตรียมงาน',
    'start_date' => 'เริ่มงาน',
    'to_customer_date' => 'เวลาที่ถึงลูกค้า',
    'delivery_date' => 'ส่งรถให้ลูกค้า',
    'pickup_customer_date' => 'รับรถจากลูกค้า',
    'return_from_customer' => 'กลับจากจากลูกค้า',
    'return_tls_time' => 'เวลาที่กลับถึงทรูลิซซิ่ง',
    'return_key_date' => 'คืนกุญแจ',
    'pickup_key_date' => 'เวลารับกุญแจ',
    'driving_job_type' => 'ประเภทงานคนขับ',
    'remark' => 'หมายเหตุ',
    'status_' . RentalStatusEnum::DRAFT => 'ร่าง',
    'status_' . RentalStatusEnum::PENDING => 'รอชำระเงิน',
    'status_' . RentalStatusEnum::PAID => 'ชำระเงินแล้ว',
    'status_' . RentalStatusEnum::CANCEL => 'ยกเลิก',
    'status_' . RentalStatusEnum::TEMPORARY => 'ชั่วคราว',
    'status_' . RentalStatusEnum::REMARK => 'หมายเหตุ',
    'status_' . RentalStatusEnum::SUCCESS => 'เสร็จสมบูรณ์',
    'class_' . RentalStatusEnum::DRAFT => 'dark-blue',
    'class_' . RentalStatusEnum::PENDING => 'warning',
    'class_' . RentalStatusEnum::PAID => 'success',
    'class_' . RentalStatusEnum::CANCEL => 'gray',
    'class_' . RentalStatusEnum::TEMPORARY => 'purple',
    'class_' . RentalStatusEnum::REMARK => 'dark-orange',
    'class_' . RentalStatusEnum::SUCCESS => 'primary',

    'pickup_key_' . OperationKeyEnum::PICKUP => 'เบิกกุญแจ',
    'return_key_' . OperationKeyEnum::RETURN => 'คืนกุญแจ',

    'operation' . OperationKeyAddressEnum::OPERATION => 'Operation',
    'driver' . OperationKeyAddressEnum::DRIVER => 'พนักงานขับรถ',

    'drivig_type_' . DrivingJobTypeStatusEnum::MAIN_JOB => 'งานหลัก',
    'drivig_type_' . DrivingJobTypeStatusEnum::SIDE_JOB => 'งานเสริม',

    'sd_' . SelfDriveTypeEnum::SEND => 'ขาไป',
    'sd_' . SelfDriveTypeEnum::PICKUP => 'ขากลับ',


    'transfer_type_' . OperationTransferTypeEnum::SEND => 'ส่งมอบลูกค้า',
    'transfer_type_' . OperationTransferTypeEnum::PICKUP => 'รับเข้าคลัง',
];
