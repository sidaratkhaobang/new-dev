<?php

use App\Enums\RepairStatusEnum;
use App\Enums\RepairTypeEnum;
use App\Enums\RepairEnum;

return [
    'page_title' => 'ใบแจ้งซ่อม',
    'worksheet_no' => 'เลขที่ใบแจ้งซ่อม',
    'type_job' => 'ประเภทงาน',
    'license_plate' => 'ทะเบียนรถ',
    'customer' => 'ลูกค้า',
    'repair_order_no' => 'เลขที่ใบสั่งซ่อม',
    'alert_date' => 'วันที่แจ้งซ่อม',
    'repair_ref' => 'อ้างอิงเลขที่ใบสั่งซ่อม',
    'center_date' => 'วันที่นำรถเข้าศูนย์จริง',
    'expected_date' => 'วันที่คาดว่าจะซ่อมเสร็จ',
    'completed_date' => 'วันที่ซ่อมเสร็จจริง',

    'user_by' => 'ผู้จัดทำใบงาน',
    'user_role' => 'บทบาท',
    'user_date' => 'วันที่จัดทำ',
    'user_branch' => 'สาขา',

    'car_table' => 'ข้อมูลรถ',
    'car_no' => 'ทะเบียนรถ/หมายเลขตัวถัง/หมายเลขเครื่องยนต์',
    'rental' => 'งานเช่า',
    'rental_no' => 'เลขที่ใบขอเช่า',
    'rental_name' => 'ผู้เช่า',
    'contract_no' => 'เลขที่สัญญา',
    'contract_start_date' => 'วันเริ่มสัญญา',
    'contract_end_date' => 'วันสิ้นสุดสัญญา',
    'insurance' => 'ประกันภัย',
    'insurance_no' => 'กรมธรรม์ประกันภัยเลขที่',
    'company' => 'บริษัท',
    'coverage_start_date' => 'วันเริ่มคุ้มครอง',
    'coverage_end_date' => 'วันสิ้นสุดคุ้มครอง',
    'sum_insured' => 'ทุนประกัน',

    'repair_table' => 'ข้อมูลใบแจ้งซ่อม',
    'repair_type' => 'ประเภทการซ่อม/เช็กระยะ',
    'repair_date' => 'วันเวลาที่แจ้งซ่อม/เช็กระยะ',
    'informer_type' => 'ประเภทผู้แจ้ง',
    'informer' => 'ผู้แจ้ง',
    'contact' => 'ชื่อผู้ติดต่อ',
    'tel' => 'เบอร์โทร',
    'mileage' => 'เข็มไมล์ล่าสุด',
    'place' => 'สถานที่ใช้รถ',
    'remark' => 'หมายเหตุ',
    'document' => 'เอกสารเพิ่มเติม (ขนาดไม่เกิน 10 MB)',

    'service_center_table' => 'ข้อมูลการนำรถเข้า/ออกจากศูนย์บริการ',
    'in_center' => 'การนำเข้าศูนย์',
    'in_center_date' => 'วันที่คาดว่าจะนำเข้าศูนย์',
    'is_driver_in_center' => 'ต้องการพนักงานขับรถ(สำหรับนำเข้าศูนย์)',
    'out_center' => 'การนำออกจากศูนย์',
    'out_center_date' => 'วันที่คาดว่าจะนำออกศูนย์',
    'is_driver_out_center' => 'ต้องการพนักงานขับรถ (สำหรับนำออกจากศูนย์)',
    'is_customer' => 'ลูกค้านำเข้าเอง',
    'is_tls' => 'เจ้าหน้าที่ TLS ดำเนินการ',

    'replacement_table' => 'ข้อมูลรถหลัก/รถทดแทน',
    'is_replacement' => 'ต้องการรถทดแทน',
    'replacement_date' => 'วันที่ต้องการรถทดแทน',
    'replacement_type' => 'ประเภทงานรถทดแทน',
    'replacement_place' => 'สถานที่รับ/ส่งรถทดแทน',
    'replacement_create' => 'เปิดงานรถทดแทน',

    //Modal - ข้อมูลการซ่อม
    'description_repair_table' => 'รายละเอียดแจ้งซ่อม / เช็กระยะ',
    'date' => 'วันที่',
    'description' => 'รายละเอียด',
    'check' => 'ตรวจเช็ก',
    'qc' => 'QC',

    //Modal - ประวัติซ่อมบำรุง
    'maintain_history' => 'ประวัติซ่อมบำรุง',
    'maintain_date' => 'วันที่ซ่อมบำรุง',
    'maintain_type' => 'ประเภทงานที่ซ่อม',
    'maintain_mileage' => 'เข็มไมล์ขณะซ่อม',
    'maintain_description' => 'รายละเอียดการซ่อม',
    'maintain_contact' => 'ผู้ติดต่อ',
    'maintain_user' => 'ชื่อผู้ขอซ่อม',
    'maintain_tel' => 'เบอร์โทรผู้ขอซ่อม',

    //Modal - ประวัติอุบัติเหตุ
    'accident_history' => 'ประวัติอุบัติเหตุ',
    'accident_date' => 'วันที่เกิดอุบัติเหตุ',
    'accident_detail' => 'รายละเอียดอุบัติเหตุ',

    //Modal - เงื่อนไขบริการ
    'condition' => 'เงื่อนไขบริการ',

    'repair_type_' . RepairTypeEnum::CHECK_DISTANCE => 'เช็กระยะ',
    'repair_type_' . RepairTypeEnum::GENERAL_REPAIR => 'ซ่อมทั่วไป',
    'repair_type_' . RepairTypeEnum::CHECK_AND_REPAIR => 'เช็กระยะและซ่อมทั่วไป',

    'informer_' . RepairEnum::CUSTOMER => 'ลูกค้า',
    'informer_' . RepairEnum::TLS => 'พนักงาน TLS',

    'repair_text_' . RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER => 'รอเปิดใบสั่งซ่อม',
    'repair_class_' . RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER => 'primary',
    'repair_text_' . RepairStatusEnum::WAIT_APPROVE_QUOTATION => 'รออนุมัติใบเสนอราคา',
    'repair_class_' . RepairStatusEnum::WAIT_APPROVE_QUOTATION => 'warning',
    'repair_text_' . RepairStatusEnum::REJECT_QUOTATION => 'ไม่อนุมัติใบเสนอราคา',
    'repair_class_' . RepairStatusEnum::REJECT_QUOTATION => 'danger',
    'repair_text_' . RepairStatusEnum::PENDING_REPAIR => 'รอดำเนินการซ่อม',
    'repair_class_' . RepairStatusEnum::PENDING_REPAIR => 'primary',
    'repair_text_' . RepairStatusEnum::IN_PROCESS => 'อยู่ระหว่างการซ่อม',
    'repair_class_' . RepairStatusEnum::IN_PROCESS => 'warning',
    'repair_text_' . RepairStatusEnum::COMPLETED => 'ซ่อมเสร็จสิ้น',
    'repair_class_' . RepairStatusEnum::COMPLETED => 'success',
    'repair_text_' . RepairStatusEnum::CANCEL => 'ยกเลิกใบสั่งซ่อม',
    'repair_class_' . RepairStatusEnum::CANCEL => 'danger',
    'repair_text_' . RepairStatusEnum::EXPIRED => 'ใบสั่งซ่อมหมดอายุ',
    'repair_class_' . RepairStatusEnum::EXPIRED => 'danger',

    'round' => 'ครั้งที่',
    'send_and_pickup_date' => 'วันที่รับ/ส่ง',
    'main_license_plate' => 'ทะเบียนรถหลัก',
    'replacement_license_plate' => 'ทะเบียนรถทดแทน',
    'send_pickup_method' => 'วิธีการรับ/ส่ง',
    'send_pickup_place' => 'สถานที่รับ/ส่ง',
    'replacement_worksheet' => 'ใบงานรถหลัก/รถทดแทน',
    'replacement_modal_header' => 'เปิดงานรถหลัก/รถทดแทน',
    'is_at_tls' => 'ลูกค้ามารับรถทดแทนที่ TLS',
    'slide_worksheet' => 'ใบงานรถสไลด์',
    'main_car_not_found' => 'กรุณาเลือกรถหลัก',
    'slide_worksheet_required' => 'กรุณาเลือกใบงานรถสไลด์',


];
