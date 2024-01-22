<?php
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\InsuranceRegistrationEnum;

return [
    'page_title' => 'พรบ.',
    'cmi' => 'พรบ.',
    'list_title' => 'งาน พรบ.',
    'car_detail' => 'ข้อมูลรถ',
    'act_detail' => 'ข้อมูลพรบ.',
    'leasing' => 'ลิสซิ่ง',
    'dealer' => 'Dealer',
    'car_color' => 'สีรถ',
    'cc' => 'CC',
    'car_year' => 'ปีรถ',
    'car_price' => 'ราคาซื้อรถ (รวม VAT) / คัน',
    'accessory_price' => 'ราคาอุปกรณ์ / คัน',
    'registration_type' => 'ประเภทการจดทะเบียน',
    'pickup_date' => 'วันที่รับรถ',
    'delivery_date' => 'วันที่ส่งมอบลูกค้า',
    'payment_dealer_date' => 'วันที่จ่ายเงินให้ดิลเลอร์',
    'insurance_class' => 'รุ่น / Insurance',
    'typev_mi' => 'TypeV MI',
    'typec_mi' => 'TypeC MI',
    'sum_insured_car' => 'ทุนประกันตัวรถ (80%)',
    'sum_insured_accessory' => 'ทุนประกันอุปกรณ์ (80%)',
    'sum_insured_total' => 'ทุนประกันรวม',
    'year_act' => 'ปีที่จัดทำ',
    'insurance_company' => 'บริษัทประกันภัย',
    'beneficiary' => 'ผู้รับผลประโยชน์',
    'delivery_doc_date' => 'วันที่ส่งเอกสาร',
    'receive_doc_date' => 'วันที่ TLS รับเอกสาร',
    'check_date' => 'วันที่ตรวจข้อมูล',
    'cmi_bar_no' => 'หมายเลข พรบ. (บาร์)',
    'cmi_no' => 'เลขกรมธรรม์ พรบ.',
    'endorsement' => 'สลักหลัง (พรบ.)',
    'cmi_bracket' => ' (พรบ.)',
    'policy_start_date' => 'วันที่เริ่มคุ้มครอง',
    'policy_end_date' => 'วันที่สิ้นสุดการคุ้มครอง',
    'premium' => 'ค่าเบี้ย',
    'premium_net' => 'เบี้ยสุทธิ',
    'discount' => 'ส่วนลด',
    'stamp_duty' => 'อากรแสตมป์',
    'tax' => 'ภาษี',
    'premium_total' => 'รวมเบี้ย',
    'withholding_tax_1' => 'หักภาษี ณ ที่จ่าย 1%',
    'statement_no' => 'เลขที่ใบแจ้งเบี้ย',
    'tax_invoice_no' => 'เลขที่ใบกำกับภาษี',
    'statement_date' => 'วันที่ออกใบแจ้งเบี้ยและใบกำกับภาษี',
    'account_submission_date' => 'วันที่ส่งบัญชี',
    'operated_date' => 'วันที่ดำเนินการจ่าย',
    'status_pay_premium' => 'สถานะการจ่ายค่าเบี้ย (พรบ.)',
    'cmi_info' => 'ข้อมูล พรบ.',
    'coverage_info' => 'ข้อมูลความคุ้มครอง',
    'cmi_headline' => 'ความคุ้มครองประกันภัยรถยนต์ ภาคบังคับ (พรบ.)',
    'lot' => 'Lot',
    'worksheet_no' => 'เลขที่ใบงานประกันภัย',
    'cmi_type' => 'ประเภทการทำ พรบ.',
    'vmi_type' => 'ประเภทการทำประกัน',
    'year_insurance' => 'ปีที่ทำประกัน',
    'po_no' => 'เลขที่ใบจัดซื้อรถ',
    'license_plate' => 'ทะเบียนรถ',
    'license_plate_chassis' => 'ทะเบียนรถ / เลขตัวถัง',
    'renter' => 'ผู้เช่า',
    'type_' . InsuranceRegistrationEnum::REGISTER => 'รถใหม่',
    'type_' . InsuranceRegistrationEnum::RENEW => 'ต่ออายุ',

    'status_' . InsuranceStatusEnum::PENDING => 'รอดำเนินการ',
    'status_' . InsuranceStatusEnum::IN_PROCESS => 'ระหว่างดำเนินการ',
    'status_' . InsuranceStatusEnum::COMPLETE => 'เสร็จสิ้น',
    'status_' . InsuranceStatusEnum::CANCEL => 'ยกเลิก',
    'status_' . InsuranceStatusEnum::REQUEST_CANCEL => 'ขอยกเลิก',
    'status_' . InsuranceCarStatusEnum::CANCEL_POLICY => 'ยกเลิก',

    'class_' . InsuranceStatusEnum::PENDING => 'primary',
    'class_' . InsuranceStatusEnum::IN_PROCESS => 'warning',
    'class_' . InsuranceStatusEnum::COMPLETE => 'success',
    'class_' . InsuranceStatusEnum::CANCEL => 'danger',
    'class_' . InsuranceCarStatusEnum::REQUEST_CANCEL => 'primary',
    'class_' . InsuranceCarStatusEnum::CANCEL_POLICY => 'danger',


    'make_cmi' => 'จัดทำ พรบ.',
    'data_required_make_cmi' => 'กรุณากรอกข้อมูลใน พรบ. เพื่อดำเนินการจัดทำ พรบ. ต่อไป',
    'status_fail_make_cmi' => 'พรบ. มีสถานะที่ไม่สามารถจัดทำ พรบ. ได้',

    'premium_status_' . STATUS_DEFAULT => 'กำลังดำเนินการจ่าย',
    'premium_status_' . STATUS_ACTIVE => 'จ่ายแล้ว',
    'premium_gt_zero' => 'รายการค่าเบี้ย ต้องมีค่ามากกว่า 0',
    'download' => 'ดาวน์โหลดเทมเพลต',
    'excel_upload' => 'อัปโหลดไฟล์ Excel',
    'file_type' => 'ประเภทไฟล์',
    'rental_info' => 'ข้อมูลผู้เช่า',
    'rental_duration' => 'จำนวนเช่า (เดือน)',
    'customer_group' => 'กลุ่มลูกค้า',
    'customer_address' => 'ที่อยู่ลูกค้า',
    'cancel_title' => 'ยกเลิกพรบ.',
    'cancel_cmi_file_title' => 'ข้อมูลการยกเลิกพรบ',
    'cancel_vmi_file_title' => 'ข้อมูลการยกเลิกประกัน',
    'cancel_info' => 'ข้อมูลการยกเลิก',
    'cancel_reason' => 'เหตุผลในการยกเลิก',
    'request_cancel_date' => 'วันที่แจ้งยกเลิก',
    'actual_cancel_date' => 'วันที่แจ้งบริษัทประกันภัยยกเลิก',

    'refund' => 'ค่าเบี้ยคืน',
    'refund_stamp' => 'ค่าอากรแสตมป์คืน',
    'refund_vat' => 'ค่า VAT',
    'refund_total' => 'รวมเบี้ยคืน',
    'credit_note' => 'Credit Note',
    'credit_note_date' => 'Credit Note Date',
    'check_date_en' => 'Date Check',
    'send_account_date' => 'Date send Account',
    'remission_type' => 'ประเภทการลดหนี้',
    // policy_start_date
];