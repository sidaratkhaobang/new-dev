<?php

use App\Enums\DrivingJobStatusEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Models\BorrowCar;
use App\Models\CarAuction;
use App\Models\InstallEquipment;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\ImportCarLine;
use App\Models\ReplacementCar;
use App\Models\TransferCar;
use App\Models\RepairOrder;


return [
    'page_title' => 'งานคนขับ',
    'worksheet_table' => 'ข้อมูลใบงานคนขับ',
    'id' => 'รหัสอ้างอิงใบงาน',
    'worksheet_no' => 'เลขที่ใบงาน',
    'worksheet_type' => 'ประเภทใบงาน',
    'job_type' => 'ประเภทงาน',
    'job_id' => 'อ้างอิงงาน',
    'ref_no' => 'อ้างอิงใบงาน',
    'parent' => 'อ้างอิงใบงานเก่า',
    'working_date' => 'วันที่ทำงาน',
    'start_time' => 'เวลาเริ่มงาน',
    'end_time' => 'เวลาจบงาน',
    'start_date' => 'วันที่เริ่มงาน (มีผลต่อวันที่นำรถเข้าออก)',
    'end_date' => 'วันที่สิ้นสุดงาน (มีผลต่อวันที่นำรถเข้าออก)',
    'income' => 'รายรับของงาน',
    'driver_id' => 'รหัสอ้างอิงคนขับ',
    'driver_name' => 'ชื่อคนขับ',
    'status' => 'สถานะงาน',
    'is_confirm_wage' => 'ยืนยันรายจ่าย',
    'wage_job_table' => 'รายจ่ายคนขับ',
    'driver_wage' => 'ประเภทค่าใช้จ่าย',
    'remark' => 'หมายเหตุ',
    'description' => 'รายละเอียดงาน',
    'amount' => 'รายจ่าย',
    'add_new' => 'เพิ่มงานคนขับ',
    'total_items' => 'รายการทั้งหมด',
    'save_complete' => 'บันทึก + ยืนยันรายจ่าย',
    'est_distance' => 'ระยะทางโดยประมาณ',
    'actual_start_date' => 'วันเวลาที่เริ่มงานจริง',
    'actual_end_date' => 'วันเวลาที่จบงานจริง',
    'actual_end_job_date' => 'วันเวลาที่จบงานจริง',
    'arrived_office' => 'กลับถึง Office',
    'car_transfer_info' => 'ข้อมูลใบนำรถเข้าออก',
    'car_info' => 'ข้อมูลรถ',
    'transfer_worksheet_no' => 'เลขที่ใบนำรถเข้าออก',

    'job_type_' . Rental::class => 'เช่าสั้น',
    'job_type_' . LongTermRental::class => 'เช่ายาว',
    'job_type_' . ImportCarLine::class => 'ซื้อรถใหม่',
    'job_type_' . DrivingJobTypeStatusEnum::OTHER => 'อื่นๆ',
    'job_type_' . InstallEquipment::class => 'รถติดตั้งอุปกรณ์',
    'job_type_' . TransferCar::class => 'รถโอน',
    'job_type_' . ReplacementCar::class => 'รถทดแทน',
    'job_type_' . RepairOrder::class => 'ซ่อมบำรุง',
    'job_type_' . CarAuction::class => 'รถประมูลขาย',
    'job_type_' . BorrowCar::class => 'รถยืม',

    'status_' . DrivingJobStatusEnum::INITIAL . '_class' => 'primary',
    'status_' . DrivingJobStatusEnum::INITIAL . '_text' => 'เปิดงาน',

    'status_' . DrivingJobStatusEnum::COMPLETE . '_class' => 'success',
    'status_' . DrivingJobStatusEnum::COMPLETE . '_text' => 'เสร็จสิ้น',

    'status_' . DrivingJobStatusEnum::PENDING . '_class' => 'info',
    'status_' . DrivingJobStatusEnum::PENDING . '_text' => 'รอดำเนินการ',

    'status_' . DrivingJobStatusEnum::IN_PROCESS . '_class' => 'warning',
    'status_' . DrivingJobStatusEnum::IN_PROCESS . '_text' => 'อยู่ระหว่างดำเนินการ',

    'status_' . DrivingJobStatusEnum::CANCEL . '_class' => 'danger',
    'status_' . DrivingJobStatusEnum::CANCEL . '_text' => 'ยกเลิก',

    'is_confirm_wage_' . BOOL_FALSE . '_class' => 'secondary',
    'is_confirm_wage_' . BOOL_FALSE . '_text' => 'แบบร่าง',

    'is_confirm_wage_' . BOOL_TRUE . '_class' => 'success',
    'is_confirm_wage_' . BOOL_TRUE . '_text' => 'เสร็จสิ้น',

    'driving_job_type' => 'ลักษณะงาน',
    'driving_job_type_' . DrivingJobTypeStatusEnum::MAIN_JOB => 'งานหลัก',
    'driving_job_type_' . DrivingJobTypeStatusEnum::SIDE_JOB => 'งานเพิ่มเติม',

    'self_drive_type' => 'ประเภทบริการงานเช่า',
    'self_drive_text' => 'สถานะself drive',

    'self_drive_type_' . SelfDriveTypeEnum::SEND => 'ส่งรถ',
    'self_drive_type_' . SelfDriveTypeEnum::PICKUP => 'รับรถ',
    'self_drive_type_' . SelfDriveTypeEnum::OTHER => 'พร้อมคนขับ',
    'self_drive_type_' . SelfDriveTypeEnum::SELF_DRIVE => 'ยืมรถ',

    'service_type_' . ServiceTypeEnum::SELF_DRIVE => 'self drive',
    'service_type_' . ServiceTypeEnum::BUS => 'bus',
    'service_type_' . ServiceTypeEnum::BOAT => 'boat',
    'service_type_' . ServiceTypeEnum::LIMOUSINE => 'limousine',
    'service_type_' . ServiceTypeEnum::MINI_COACH => 'mini coach',
    'service_type_' . ServiceTypeEnum::SLIDE_FORKLIFT => 'slide forklift',

    'estimate_prepare_date' => 'เวลาเตรียมรถโดยประมาณ',
    'estimate_start_date' => 'เวลาเริ่มงานโดยประมาณ',
    'estimate_rented_date' => 'เวลารถถึงลูกค้าโดยประมาณ',
    'estimate_end_job_date' => 'เวลาจบงานโดยประมาณ',
    'estimate_arrive_date' => 'เวลากลับถึง TLS โดยประมาณ',
    'estimate_end_date' => 'เวลาคืนกุญแจโดยประมาณ',
    'atk_check' => 'ผลการตรวจ atk',
    'alcohol_check' => 'ผลการตรวจค่าแอลกอฮอล์',
    'alcohol' => 'ค่าแอลกอฮอล์',
    'actual_arrive_date' => 'วัน-เวลา ที่กลับมาถึง TLS จริง',

    'pickup_return_date' => 'วันที่ส่ง/รับ รถ',
    'origin' => 'จุดรับ',
    'destination' => 'จุดส่ง',
    'job_parent_table' => 'ข้อมูลงานอ้างอิง',
    'service_type' => 'ประเภทบริการ',
    'customer' => 'ลูกค้า',
    'dealer' => 'Dealer',
    'rental_start_date' => 'วันเวลาที่เริ่มเช่า',
    'rental_end_date' => 'วันเวลาที่สิ้นสุดเช่า',
    'rental_origin' => 'สถานที่รับ',
    'rental_destination' => 'สถานที่ส่ง',
    'delivery_place' => 'สถานที่ส่งมอบ',
    'delivery_date' => 'วันที่ส่งมอบ',
    'contract_start_date' => 'วันที่เริ่มสัญญา',
    'contract_end_date' => 'วันที่สิ้นสุดสัญญา',
    'license_plate' => 'ทะเบียนรถ',
    'license_plate_chassis_no' => 'ทะเบียนรถ/หมายเลขตัวถัง',
    'driver_detail' => 'ข้อมูลพนักงานขับรถ',
    'work_day' => 'วันที่ทำงาน (อ้างอิงตามงาน)',
    'view_reserve' => 'ดูตารางงานพนักงานขับรถ',
    'supplier' => 'Supplier',
];
