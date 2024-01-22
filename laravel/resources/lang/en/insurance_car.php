<?php
use App\Enums\InsuranceCarStatusEnum;
return [
    'insurance_car_title' => 'งาน พรบ. - ประกันภัยทั้งหมด',
    'license_plate_chassis_no' => 'ทะเบียนรถ/หมายเลขตัวถัง',
    'policy_number' => 'เลขกรมธรรม์',
    'policy_number_no' => 'เลขที่กรมธรรม์',
    'cmi_number' => 'เลขที่กรมธรรม์ พรบ.',
    'cmi_data_not_found' => 'ไม่พบ พรบ ที่สามารถต่ออายุได้',
    'cars_require' => "ข้อมูลรถ",
    'insurance_company' => 'บริษัทประกันภัย',
    'status' => 'สถานะ',
    'type' => 'ประเภท',
    'sheet_protection' => 'ใบงานทำประกัน',
    'sheet_policy' => 'ใบงานทำพรบ.',
    'coverage_start_date' => 'วันที่เริ่มคุ้มครอง',
    'coverage_end_date' => 'วันที่สิ้นสุดคุ้มครอง',
    'renew_all_insurance' => 'ต่ออายุประกันทั้งหมด',
    'renew_all_cmi' => 'ต่ออายุ พรบ. ทั้งหมด',
    'renew_insurance' => 'ต่ออายุประกัน',
    'renew_cmi' => 'ต่ออายุ พรบ.',
    'insurance_list' => 'รายการประกันภัย',
    'worksheet_no' => 'เลขที่ใบงาน <br> ประกันภัย',
    'cmi_info' => 'ข้อมูล พรบ.',
    'coverage_info' => 'ข้อมูลความคุ้มครอง',
    'rental_detail' => 'ข้อมูลผู้เช่า',
    'renter' => 'ผู้เช่า',
    'rental_month' => 'จำนวนเช่า (เดือน)',
    'customer_group' => 'กลุ่มลูกค้า',
    'customer_address' => 'ที่อยู่ลูกค้า',
    'job_type' => 'ประเภทงาน',
    'cmi_year' => 'ปีที่จัดทำประกัน',
    'insurance_start_date' => 'วันที่เริ่มการคุ้มครอง',
    'insurance_end_date' => 'วันที่สิ้นสุดการคุ้มครอง',
    'company' => 'บริษัท',
    'year_require' => 'ปีที่จัดทำประกัน',
    'status_'.InsuranceCarStatusEnum::UNDER_POLICY => 'success',
    'class_' . InsuranceCarStatusEnum::UNDER_POLICY => 'อยู่ระหว่างคุ้มครอง',
    'status_'.InsuranceCarStatusEnum::END_POLICY => 'danger',
    'class_' . InsuranceCarStatusEnum::END_POLICY => 'สิ้นสุดกรมธรรม์',
    'status_'.InsuranceCarStatusEnum::REQUEST_CANCEL => 'primary',
    'class_' . InsuranceCarStatusEnum::REQUEST_CANCEL => 'ขอยกเลิก',
    'status_'.InsuranceCarStatusEnum::CANCEL_POLICY => 'danger',
    'class_' . InsuranceCarStatusEnum::CANCEL_POLICY => 'ยกเลิกกรมธรรม์',
    'status_'.InsuranceCarStatusEnum::RENEW_POLICY => 'warning',
    'class_' . InsuranceCarStatusEnum::RENEW_POLICY => 'ต่ออายุล่วงหน้า',
    'vmi_info' => 'ข้อมูล ประกัน',
    'cancel_reason' => 'เหตุผลในการขอยกเลิก',
    'class_' => 'รอดำเนินการ',
    'status_' => 'warning',

];
