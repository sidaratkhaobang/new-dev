<?php

use App\Enums\AccidentSlideEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\AccidentTypeEnum;
use App\Enums\CarTypeAccidentEnum;
use App\Enums\CaseAccidentEnum;
use App\Enums\ClaimantAccidentEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\GarageTypeEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\RepairClaimEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\ResponsibleEnum;
use App\Enums\RightsEnum;
use App\Enums\WoundType;
use App\Enums\ZoneEnum;
use App\Enums\ZoneTypeEnum;

return [

    'page_title' => 'แจ้งอุบัติเหตุรถยนต์',
    'page_title_sheet' => 'ใบแจ้งอุบัติเหตุรถยนต์',
    'add_new' => 'เพิ่มข้อมูล',
    'name' => 'ชื่ออู่',
    'total_items' => 'รายการทั้งหมด',
    'images' => 'รูปภาพประกอบ (ขนาดไฟล์ไม่เกิน 10 MB)',
    'garage_detail' => 'ข้อมูลอู่',
    'garage_type' => 'ประเภทอู่',
    'email' => 'อีเมล',
    'tel' => 'เบอร์โทร',
    'car_type' => 'ประเภทรถ',
    'EMCS' => 'EMCS',
    'onsite_service' => 'บริการนอกสถานที่',
    'insurance' => 'รับงานบริษัทประกัน',
    'address' => 'ที่อยู่',
    'sector' => 'ภาค',
    'province' => 'จังหวัด',
    'amphure' => 'อำเภอ/เขต',
    'district' => 'ตำบล/แขวง',
    'zip_code' => 'รหัสไปรษณีย์',
    'coordinator_name' => 'ชื่อผู้ประสานงานอู่',
    'status' => 'สถานะ',
    'garage' => 'อู่',
    'province_district' => 'จังหวัด/อำเภอ',
    'onsite_install_service' => 'บริการติดตั้งนอกสถานที่',
    'car_accident_detail' => 'ข้อมูลอุบัติเหตุรถยนต์',
    'car_detail' => 'รายละเอียดรถยนต์',
    'accident_type' => 'ประเภทอุบัติเหตุ',
    'claim_type' => 'ประเภทการเคลม',
    'report_date' => 'วันเวลาที่แจ้ง',
    'reporter' => 'ผู้แจ้ง',
    'report_no' => 'เลขที่รับแจ้ง',
    'license_plate_chassis_engine' => 'ทะเบียนรถ / หมายเลขตัวถัง / หมายเลขเครื่องยนต์',
    'chassis_no' => 'หมายเลขตัวถัง',
    'car_class' => 'รุ่นรถ',
    'worksheet_no_ref' => 'เลขที่ใบขอเช่า',
    'customer_name' => 'ลูกค้า',
    'policy_number' => 'เลขกรมธรรม์',
    'insurance_company' => 'บริษัทประกันภัย',
    'insurance_tel' => 'เบอร์ติดต่อประกัน',
    'coverage_start_date' => 'วันที่เริ่มคุ้มครอง',
    'coverage_end_date' => 'วันที่สิ้นสุดคุ้มครอง',
    'accident_detail' => 'รายละเอียดอุบัติเหตุ',
    'partie' => 'คู่กรณี',
    'main_area' => 'พื้นที่หลักในการใช้รถ',
    'case' => 'เคส',
    'accident_description' => 'ลักษณะที่เกิดเหตุ',
    'accident_place' => 'สถานที่เกิดเหตุ',
    'current_place' => 'สถานที่ที่รถอยู่ปัจจุบัน',
    'driver' => 'ผู้ขับขี่',
    'accident_date' => 'วันเวลาที่เกิดเหตุ',
    'case' => 'เคส',
    'wrong_type' => 'ประเภทความผิด',
    'amount_wounded_driver' => 'จำนวนผู้บาดเจ็บฝ่ายผู้ขับขี่รถ',
    'amount_wounded_parties' => 'จำนวนผู้บาดเจ็บฝ่ายคู่กรณี/อื่น',
    'amount_wounded_total' => 'จำนวนผู้บาดเจ็บทั้งหมด',
    'amount_deceased_driver' => 'จำนวนผู้เสียชีวิตฝ่ายผู้ขับขี่รถ',
    'amount_deceased_parties' => 'จำนวนผู้เสียชีวิตฝ่ายคู่กรณี/อื่น',
    'amount_deceased_total' => 'จำนวนผู้เสียชีวิตทั้งหมด',
    'cradle_recommend' => 'อู่ที่แนะนำเข้าซ่อม',
    'wounded' => 'ผู้บาดเจ็บ',
    'deceased' => 'ผู้เสียชีวิต',
    'repair' => 'การซ่อม',
    'remark' => 'หมายเหตุ',
    'claim_by' => 'เปิดเคลมโดย',

    'forklift_detail' => 'ข้อมูลรถยก (ถ้ามี)',
    'first_lifting' => 'การยกรถครั้งที่ 1',
    'first_lifter' => 'ผู้ยกรถครั้งที่ 1',
    'first_lift_date' => 'วันที่ยกรถครั้งที่ 1',
    'first_lift_price' => 'ค่ายกรถครั้งที่ 1',
    'lift_tel' => 'เบอร์โทรผู้ติดต่อยกรถ',
    'lift_from' => 'ยกจาก',
    'lift_to' => 'ยกไป',
    'lift_price' => 'ค่ารถยก',

    'worksheet_no' => 'เลขที่ใบแจ้ง',
    'main_license_plate' => 'ทะเบียนรถหลัก',
    'license_plate' => 'ทะเบียนรถ',
    'accident_datetime' => 'วันเวลาที่เกิดอุบัติเหตุ',
    'customer' => 'ลูกค้า',

    'need_folklift' => 'ต้องการรถยกของ  TLS',
    'lift_date' => 'วันที่ต้องการรถยก',
    'need_folklift' => 'ต้องการรถยกของ  TLS',
    'segment' => 'ส่วนงาน',

    'replacement_car_detail' => 'ข้อมูลรถทดแทน (ถ้ามี)',
    'replacement_car_files' => 'แบบฟอร์มขอรถทดแทนจากลูกค้า (ขนาดไม่เกิน 10 MB)',

    'need_replacement' => 'ต้องการรถทดแทน',
    'replacement_date' => 'วันที่ต้องการรถทดแทน',
    'replacement_type' => 'ประเภทงานรถทดแทน',
    'need_driver_replacement' => 'ต้องการพนักงานส่งรถทดแทน',
    'replacement_place' => 'สถานที่รับ/ส่งรถทดแทน',

    'place' => 'สถานที่',

    'contact_accident' => 'ข้อมูลการติดต่อฝ่ายอุบัติเหตุ',
    'fullname' => 'ชื่อ - นามสกุล',
    'department' => 'แผนก',

    'status_' . STATUS_ACTIVE => 'ใช่',
    'status_' . STATUS_DEFAULT => 'ไม่ใช่',

    'need_' . STATUS_ACTIVE => 'ต้องการ',
    'need_' . STATUS_DEFAULT => 'ไม่ต้องการ',


    'status_' . STATUS_ACTIVE => 'มี',
    'status_' . STATUS_DEFAULT => 'ไม่มี',

    'garage_type_' . GarageTypeEnum::GENERAL_GARAGE => 'อู่ทั่วไป',
    'garage_type_' . GarageTypeEnum::GLASS_GARAGE => 'ร้านกระจก',

    'mistake_' . MistakeTypeEnum::TRUE => 'ถูก',
    'mistake_' . MistakeTypeEnum::FALSE => 'ผิด',
    'mistake_' . MistakeTypeEnum::BOTH => 'ประมาทร่วม',

    'accident_type_' . AccidentTypeEnum::SOFT_CAR => 'รถเบา',
    'accident_type_' . AccidentTypeEnum::HARD_CAR => 'รถหนัก',

    'accident_type_index_' . AccidentTypeEnum::SOFT_CAR => 'ขับเคลื่อนได้',
    'accident_type_index_' . AccidentTypeEnum::HARD_CAR => 'ขับเคลื่อนไม่ได้',

    'claim_type_' . ClaimTypeEnum::FRESH_CLAIM => 'เคลมสด',
    'claim_type_' . ClaimTypeEnum::DRY_CLAIM => 'เคลมแห้ง',
    'claim_type_' . ClaimTypeEnum::ACCLIMATE => 'ปรับสภาพ',

    'claimant_' . ClaimantAccidentEnum::TENANT => 'ผู้เช่า',
    'claimant_' . ClaimantAccidentEnum::GARAGE => 'อู่',
    'claimant_' . ClaimantAccidentEnum::TLS => 'เจ้าหน้าที่ TLS',


    'car_type_' . CarTypeAccidentEnum::SEDAN_CAR => 'รถเก๋ง',
    'car_type_' . CarTypeAccidentEnum::PICKUP_CAR => 'รถกระบะ',
    'car_type_' . CarTypeAccidentEnum::VAN => 'รถตู้',
    'car_type_' . CarTypeAccidentEnum::BUS => 'รถบัส',
    'car_type_' . CarTypeAccidentEnum::ALL_TYPE => 'รถยนต์ทุกชนิด',
    'car_type_' . CarTypeAccidentEnum::TRUCK => 'รถบรรทุก',
    'car_type_' . CarTypeAccidentEnum::GENERAL => 'งานทั่วไป',


    'zone_type_' . ZoneEnum::NORTH => 'ภาคเหนือ',
    'zone_type_' . ZoneEnum::NORTHEAST => 'ภาคตะวันออกเฉียงเหนือ',
    'zone_type_' . ZoneEnum::WESTERN => 'ภาคตะวันตก',
    'zone_type_' . ZoneEnum::CENTRAL => 'ภาคกลาง',
    'zone_type_' . ZoneEnum::SOUTH => 'ภาคใต้',


    'case_' . CaseAccidentEnum::BUMP_FALL => 'กระแทก/ตกหลุม',
    'case_' . CaseAccidentEnum::MALICIOUS => 'การกระทำมุ่งร้าย/เจตนาร้าย',
    'case_' . CaseAccidentEnum::OVERTURNING => 'การคว่ำ',
    'case_' . CaseAccidentEnum::CRASH => 'การชน',
    'case_' . CaseAccidentEnum::LOSS => 'การสูญหาย',
    'case_' . CaseAccidentEnum::FALL_WAYSIDE => 'ตกข้างทาง',
    'case_' . CaseAccidentEnum::UNKNOWN_CRASH => 'ถูกชนไม่ทราบคู่กรณี',
    'case_' . CaseAccidentEnum::STONE_THROWN => 'หินกระเด็นใส่',
    'case_' . CaseAccidentEnum::STONE_THROWN_CAR => 'หินกระเด็นใส่ตัวรถ',
    'case_' . CaseAccidentEnum::STONE_THROWN_TAIL_LAMP => 'หินกระเด็นใส่ไฟท้าย',
    'case_' . CaseAccidentEnum::OTHER => 'อื่น ๆ',

    'replace_type_' . ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN => 'ส่งรถทดแทนรับรถหลัก',

    'status_job_' . AccidentStatusEnum::WAITING_CLAIM => 'รอแจ้งเคลม',
    'status_job_' . AccidentStatusEnum::WAITING_REPAIR => 'รอแจ้งซ่อม',
    'status_job_' . AccidentStatusEnum::IN_PROGRESS => 'อยู่ระหว่างซ่อม',
    'status_job_' . AccidentStatusEnum::SUCCESS => 'รถซ่อมเสร็จ',
    'status_job_' . AccidentStatusEnum::NOT_REPAIR => 'ไม่ซ่อม',
    'status_job_' . AccidentStatusEnum::TTL => 'TTL',
    'status_job_' . AccidentStatusEnum::WAITING_OPEN_ORDER => 'รอเปิดใบสั่งซ่อม',
    'status_job_' . AccidentStatusEnum::OPEN_ORDER => 'เปิดใบสั่งซ่อม',

    'class_job_' . AccidentStatusEnum::WAITING_CLAIM => 'primary',
    'class_job_' . AccidentStatusEnum::WAITING_REPAIR => 'warning',
    'class_job_' . AccidentStatusEnum::IN_PROGRESS => 'warning',
    'class_job_' . AccidentStatusEnum::SUCCESS => 'success',
    'class_job_' . AccidentStatusEnum::NOT_REPAIR => 'secondary',
    'class_job_' . AccidentStatusEnum::TTL => 'secondary',
    'class_job_' . AccidentStatusEnum::WAITING_OPEN_ORDER => 'warning',
    'class_job_' . AccidentStatusEnum::OPEN_ORDER => 'success',


    'accident_job' => 'ข้อมูลงานอุบัติเหตุ',
    'claim_detail' => 'ข้อมูลการเคลม',
    'car_rental_table' => 'ข้อมูลงานเช่าและรถ',
    'lifter' => 'ผู้ยกรถ',
    'optional_file' => 'เอกสารเพิ่มเติม',
    'folklift_detail' => 'ข้อมูลการยกรถ',
    'date' => 'วันที่',
    'list' => 'รายการ',
    'price' => 'ราคา (บาท)',
    'cost_detail' => 'ข้อมูลค่าใช้จ่ายอื่นๆ',
    'folklift_price' => 'ค่ารถยก (บาท)',

    'customer_claim_amount' => 'จำนวนลูกค้าเปิดเคลม',
    'tls_claim_amount' => 'จำนวน TLS เปิดเคลม',
    'save_claim_amount' => 'จำนวนประหยัดเคลม',
    'compensation_payment' => 'เรียกเก็บค่าสินไหมกับคู่สัญญา (บาท)',
    'repair' => 'การซ่อม',
    'claim_summary_detail' => 'ข้อมูลสรุปการเคลม',

    'insurance_detail' => 'ข้อมูลประกันภัยรถ',
    'insurance_company' => 'บริษัทประกันภัย',
    'policy_no' => 'กรมธรรม์เลขที่',
    'coverage_start_date' => 'วันที่เริ่มคุ้มครอง',
    'coverage_end_date' => 'วันที่สิ้นสุดการคุ้มครอง',


    'car_claim_detail' => 'ข้อมูลการเคลมรถ',
    'inform_no' => 'เลขที่รับแจ้ง',
    'claim_no' => 'เลขเคลม',
    'claim_type' => 'ประเภทการเคลม',
    'responsible_person' => 'ผู้รับผิดชอบ',
    'car_claim_detail' => 'ข้อมูลการเคลมรถ',
    'except_damages' => 'ใช้สิทธิ์ยกเว้นค่าเสียหายส่วนแรก',
    'car_claim_detail' => 'ข้อมูลการเคลมรถ',
    'right_reason' => 'สาเหตุที่ไม่ใช้สิทธิ์',
    'first_damage_cost' => 'มูลค่าความเสียหายส่วนแรก',
    'tls_cost' => 'ค่าใช้จ่าย TLS รับผิดชอบ',

    'repair_table' => 'ตารางการซ่อม',
    'before_image' => 'รูปภาพก่อนซ่อม',
    'after_image' => 'รูปภาพหลังซ่อม',
    'max_size' => '(ขนาดไม่เกิน 10 MB)',
    'repair_list' => 'รายการซ่อม',
    'repair_characteristics' => 'ลักษณะการซ่อม',
    'wound_characteristics' => 'ลักษณะแผล',
    'spare_parts_supplier' => 'ผู้จัดหาอะไหล่',
    'report_tel' => 'เบอร์โทร',

    'spare_part_status_' . STATUS_ACTIVE => 'จากศูนย์/อู่',
    'spare_part_status_' . STATUS_DEFAULT => 'ประกัน',

    'receive_status_' . STATUS_ACTIVE => 'ใช่',
    'receive_status_' . STATUS_DEFAULT => 'ไม่',

    'repair_claim_' . RepairClaimEnum::HARD_BUMP => 'ชนหนัก',
    'repair_claim_' . RepairClaimEnum::SOFT_BUMP => 'ชนเบา',
    'repair_claim_' . RepairClaimEnum::TTL => 'TTL',

    // 'benefit_' . RepairClaimEnum::TTL => 'TTL',
    'wound_type_' . WoundType::A => 'A',
    'wound_type_' . WoundType::B => 'B',
    'wound_type_' . WoundType::C => 'C',
    'wound_type_' . WoundType::D => 'D',
    'wound_type_' . WoundType::OLD_SPARE_PART => 'อะไหล่เก่าแท้',
    'wound_type_' . WoundType::REPAIR_SPARE_PART => 'ซ่อมชิ้นอะไหล่',
    'wound_type_' . WoundType::NO_CHANGE_SPARE_PART => 'ไม่เปลี่ยนอะไหล่',

    'responsible_' . ResponsibleEnum::INSURANCE_ACCEPT => 'ประกันรับผิดชอบ',
    'responsible_' . ResponsibleEnum::INSURANCE_REJECT => 'ประกันปฏิเสธ',
    'responsible_' . ResponsibleEnum::TLS_ACCEPT => 'TLS รับผิดชอบ',

    'rights_' . RightsEnum::USE_RIGHTS => 'ใช้สิทธิ์',
    'rights_' . RightsEnum::NOT_USE_RIGHTS => 'ไม่ใช้สิทธิ์',

    'withdraw_true' => 'ตั้งเบิกทรู',
    'download_data_claim' => 'ดาวน์โหลด DataClaim',
    'print_repair_sheet' => 'พิมพ์ใบส่งซ่อม',

    'slide_type' => 'ประเภทรถสไลด์',
    'slide_date' => 'วันที่ยกรถ',
    'slide_file' => 'ใบงานรถสไลด์',

    'slide_worksheet' => 'ใบงานรถสไลด์',
    'replacement_pickup_date' => 'วันที่ต้องรับ /ส่ง รถหลัก/รถทดแทน',
    'customer_receive' => 'ลูกค้ามารับรถทดแทนที่ TLS',

    'replacement_date' => 'วันที่รับ/ส่ง',
    'license_plate_main' => 'ทะเบียนรถหลัก',
    'license_plate_replacement' => 'ทะเบียนรถทดแทน',
    'pickup_way' => 'วิธีการรับ/ส่ง',
    'pickup_place' => 'สถานที่รับ/ส่ง',
    'replacement_file' => 'ใบงานรถหลัก/รถทดแทน',
    'origin_place_detail' => 'ข้อมูลต้นทาง',
    'destination_place_detail' => 'ข้อมูลปลายทาง',
    'lift_date_from' => 'วันเวลาที่ต้นทางไปรับรถ',
    'lift_date_to' => 'วันเวลาที่ปลายทางไปส่งรถ',
    'origin_contact' => 'ผู้ติดต่อต้นทาง',
    'destination_contact' => 'ผู้ติดต่อปลายทาง',
    'origin_tel' => 'เบอร์ติดต่อต้นทาง',
    'destination_tel' => 'เบอร์ติดต่อปลายทาง',
    'origin_place' => 'สถานที่ต้นทาง',
    'destination_place' => 'สถานที่ปลายทาง',
    'work_type' => 'ประเภทงาน',
    'customer_receive_self' => 'ลูกค้ารับ/ส่งเอง',
    'replacement_car_detail' => 'ข้อมูลรถหลัก/รถทดแทน',
    'slide_detail' => 'ข้อมูลรถสไลด์',


    'slide_' . AccidentSlideEnum::TLS_SLIDE => 'รถสไลด์ของ TLS',
    'slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE => 'รถสไลด์ของ third party',
];