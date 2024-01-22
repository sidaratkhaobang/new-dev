<?php

use App\Enums\TransferTypeEnum;

return [

    'page_title' => 'ใบงานนำรถเข้า/ออก',
    'total_items' => 'ข้อมูลใบงานทั้งหมด',
    'opener_sheet_table' => 'ข้อมูลผู้เปิดใบงาน',
    'opener_sheet' => 'ผู้เปิดใบงาน',
    'department' => 'แผนก',
    'license_table' => 'ข้อมูลใบงาน',
    'title_info' => 'ข้อมูลใบงานนำรถเข้าออก',
    'transfer_type' => 'ประเภทงาน',
    'rental_type' => 'ประเภทใบงาน',
    'rental_no' => 'เลขที่ใบงาน',
    'reason' => 'ระบุสาเหตุการนำรถเข้า/ออก',
    'est_transfer_date' => 'วันที่ต้องการจอง',
    'period_date' => 'ช่วงเวลาใช้งานใบนำรถเข้า/ออก',
    'start_date' => 'ช่วงเวลาใช้งานเริ่มต้น',
    'end_date' => 'ช่วงเวลาใช้งานสิ้นสุด',
    'driver' => 'ผู้นำรถเข้า/ออก',
    'driver_table' => 'ข้อมูลพนักงานขับรถ',
    'driving_job' => 'ใบงาน พขร.',
    'driving_job_no' => 'เลขที่ใบงานพนักงานขับรถ',
    'driver_name' => 'ชื่อพนักงานขับรถ',
    'car_table' => 'ข้อมูลรถ',
    'car_type' => 'ประเภทรถ',
    'license_plate' => 'เลขทะเบียน',
    'engine_no' => 'หมายเลขเครื่องยนต์',
    'chassis_no' => 'หมายเลขตัวถัง',
    'car_category' => 'ชนิดรถ',
    'class' => 'รุ่น',
    'color' => 'สี',
    'key_storage' => 'สถานที่จัดเก็บกุญแจ',
    'parking_table' => 'ข้อมูลที่จอดรถ',
    'zone' => 'โซนจอด',
    'parking_slot' => 'ช่องจอด',
    'branch' => 'สาขา',
    'generate_parking' => 'Generate ช่องจอด',
    'status' => 'สถานะ',
    'period_use_date' => 'ช่วงเวลาการใช้งานใบงาน',
    'license_no' => 'เลขที่ใบงาน',
    'transfer_date' => 'วันและเวลาที่ผ่านล่าสุด',
    'add_new' => 'สร้างใบงานใหม่',
    'print_license' => 'พิมพ์ใบนำรถเข้า/ออก',
    'download_qr' => 'ดาวน์โหลด QR  Code การนำรถเข้า/ออก',
    'cancel_reason' => 'เหตุผลยกเลิกการใช้งานใบนำเข้า/ออกรถนี้',
    'transaction' => 'ข้อมูลการใช้งานใบงาน',
    'transfer_date_history' => 'วันและเวลาที่ผ่าน',
    'id' => 'รหัสใบงานนำรถเข้าออก',
    'transfer_print' => 'พิมพ์ใบนำรถเข้าออก',
    'is_difference_branch' => 'ใช้ต่างสาขาหรือไม่',
    'is_singular' => 'รูปแบบการนำรถเข้าออก',
    'is_singular_0' => 'ขนส่ง',
    'is_singular_1' => 'รายคัน',
    'origin_branch_id' => 'สาขานำรถออก',
    'destination_branch_id' => 'สาขานำรถเข้า',

    'class_warning' => 'warning',
    'text_warning' => 'รอดำเนินการ',

    'class_info' => 'info',
    'text_info' => 'กำลังใช้งาน',

    'class_success' => 'success',
    'text_success' => 'สิ้นสุดการใช้งาน',

    'class_danger' => 'danger',
    'text_danger' => 'ยกเลิกการใช้งาน',
    'driver_id' => 'รหัสพนักงานขับ',

    'transfer_type' => 'ข้อมูลรถเข้า/ออก',
    'transfer_type_' . TransferTypeEnum::IN => 'นำรถเข้า',
    'transfer_type_' . TransferTypeEnum::OUT => 'นำรถออก',

];
