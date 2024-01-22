<?php

use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\AccidentTypeEnum;
use App\Enums\CarTypeAccidentEnum;
use App\Enums\CaseAccidentEnum;
use App\Enums\ClaimantAccidentEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\GarageTypeEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\OfferGMStatusEnum;
use App\Enums\RepairClaimEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\ResponsibleEnum;
use App\Enums\RightsEnum;
use App\Enums\WoundType;
use App\Enums\ZoneEnum;
use App\Enums\ZoneTypeEnum;

return [

    'page_title' => 'ใบสั่งซ่อม',
    'add_new' => 'เพิ่มข้อมูล',
    'name' => 'ชื่ออู่',
    'total_items' => 'รายการทั้งหมด',
    'images' => 'รูปภาพประกอบ (ขนาดไฟล์ไม่เกิน 10 MB)',
    'garage_detail' => 'ข้อมูลอู่',
    'garage_type' => 'ประเภทอู่',
    'accident_detail' => 'ข้อมูลงานอุบัติเหตุทั้งหมด',
    'accident_open' => 'ใบสั่งซ่อมที่เปิด',
    'add_order' => 'เพิ่มใบสั่งซ่อม',
    'report_no' => 'เลขใบแจ้งอุบัติเหตุ/เลขเคลม',
    'accident_sheet' => 'ใบแจ้งอุบัติเหตุ',
    'repair_selected' => 'ใบสั่งซ่อมที่เลือก',
    'use_all' => 'ใช้กับทั้งหมด',
    'order_date' => 'วันที่สั่งซ่อม',
    'due_date' => 'กำหนดแล้วเสร็จ',
    'send_repair_date' => 'วันที่ส่งซ่อม',
    'completed' => 'กำหนดแล้วเสร็จ',
    'repair_price_detail' => 'ข้อมูลราคาซ่อม',
    'appointment_detail' => 'ข้อมูลการนัดหมาย',
    'appointment' => 'การแจ้งนัดหมาย',
    'informed' => 'แจ้งแล้ว',
    'appointment_date' => 'วันเวลาที่นัดหมาย',
    'appointment_place' => 'สถานที่นัดหมาย',
    'participant' => 'ผู้เข้าร่วมตรวจ 3 ฝ่าย',
    'tls' => 'TLS',
    'insurance' => 'ประกันภัย',
    'customer_driver' => 'ลูกค้า/ผู้ขับขี่',
    'garage_detail' => 'ข้อมูลอู่',
    'garage_car' => 'อู่ซ่อมรถ',
    'garage_area' => 'พื้นที่อู่',
    'garage_quotation' => 'ใบเสนอราคาจากอู่ (ขนาดไม่เกิน 10 MB)',
    'garage_quotation_file' => 'ใบเสนอราคาจากอู่',
    'garage_bidding' => 'วันที่อู่เสนอราคา',
    'car_insurance' => 'ทุนประกันตัวรถ',
    'equipment_insurance' => 'ทุนประกันอุปกรณ์/ทรัพย์สินภายในรถ',
    'repair_cost_total' => 'ค่าซ่อมทั้งหมด',
    'wage' => 'ค่าแรง',
    'spare_part_cost' => 'ค่าอะไหล่',
    'spare_part_discount' => 'ส่วนลดค่าอะไหล่',
    'spare_part_total' => 'ค่าอะไหล่ทั้งหมด',
    'spare_parts_supplier_all' => 'ผู้จัดหาอะไหล่ทั้งหมด',
    'spare_parts_supplier' => 'ผู้จัดหาอะไหล่',
    'send_garage' => 'วันที่ส่งซ่อมอู่',
    'due_date_complete' => 'กำหนดซ่อมเสร็จ (วัน)',
    'complete_date' => 'วันที่กำหนดแล้วเสร็จ',
    'garage' => 'อู่',
    'repair_date' => 'วันที่ส่งซ่อมอู่ ',
    'amount_completed' => '	กำหนดซ่อมเสร็จ (วัน)',
    'actual_repair_date' => 'วันที่ซ่อมเสร็จจริง',
    'repair_worksheet_no' => 'เลขที่ใบสั่งซ่อม',
    'accident_type' => 'ประเภทอุบัติเหตุ',
    'license_plate' => 'ทะเบียนรถ',
    'accident_worksheet_no' => 'เลขที่ใบแจ้งอุบัติเหตุ',
    'status' => 'สถานะ',
    'over_complete_date' => 'จำนวนวันที่เลยกำหนด',
    'accident_detail_table' => 'ข้อมูลอุบัติเหตุ',
    'offer_gm' => 'ส่งเสนอ GM',
    'offer_new_price' => 'เสนอราคาใหม่',
    'approved' => 'อนุมัติซ่อม',
    'ttl' => 'TTL',
    // 'appointment' => 'แจ้งนัดหมาย',
    'accident_open_list' => 'ข้อมูลใบสั่งซ่อมที่เปิด',
    'topic' => 'เรื่อง',
    'garage_repair' => 'อู่ที่ซ่อม',
    'true_leasing' => 'True Leasing',
    'email' => 'อีเมล',
    'insurance' => 'ประกัน',
    'customer' => 'ลูกค้า',
    'remark' => 'หมายเหตุ',
    'appointment_name' => 'รายชื่อผู้นัดหมาย',
    'accident_order_approve' => 'อนุมัติรายการซ่อม',
    'accident_order_sheet_approve' => 'อนุมัติใบสั่งซ่อม',
    'accident_order_sheet_ttl_approve' => 'อนุมัติใบสั่งซ่อม / Total Loss',
    'insurance_name' => 'ประกันภัย',
    'follow_repair' => 'การติดตามงานซ่อมล่าสุด',
    'total_loss_car_detail' => 'ข้อมูลรถ Total Loss',
    'total_loss_file' => 'หนังสืออนุมัติรถ Total Loss',
    'total_loss' => 'หนังสืออนุมัติรถ Total Loss (ขนาดไม่เกิน 10 MB)',
    'compensation' => 'ค่าสินไหมทดแทน',
    'noti_remove_stop_gps' => 'แจ้งถอด/หยุดสัญญาณ GPS',
    'noti_contract_rental_status' => 'แจ้งฝ่ายสัญญา สถานะรถเช่า',
    'noti_pick_up' => 'แจ้งเบิกเล่ม',
    'noti_remove_stop_gps_date' => 'วันที่ต้องการแจ้งถอด / หยุดสัญญาณ GPS',
    'noti_remove_stop' => 'แจ้งถอดและหยุดสัญญาณ',
    'purchase_option' => 'ค่าซากของรถ TTL',
    'car_claim_sheet' => 'ใบส่งรถเคลม',

    'status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST => 'รออนุมัติรายการสั่งซ่อม',
    'class_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST => 'warning',

    'status_job_' . AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION => 'รออู่เสนอราคา',
    'class_job_' . AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION => 'warning',

    'status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR => 'รออนุมัติการซ่อม',
    'class_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR => 'warning',

    'status_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL => 'รออนุมัติซ่อม/Total loss',
    'class_job_' . AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL => 'warning',

    'status_job_' . AccidentRepairStatusEnum::PROCESS_REPAIR => 'อยู่ระหว่างซ่อม',
    'class_job_' . AccidentRepairStatusEnum::PROCESS_REPAIR => 'warning',

    'status_job_' . AccidentRepairStatusEnum::SUCCESS_REPAIR => 'ซ่อมเสร็จสิ้น',
    'class_job_' . AccidentRepairStatusEnum::SUCCESS_REPAIR => 'success',

    'status_job_' . AccidentRepairStatusEnum::REJECT => 'ไม่อนุมัติรายการซ่อม',
    'class_job_' . AccidentRepairStatusEnum::REJECT => 'danger',

    'status_job_' . AccidentRepairStatusEnum::TTL => 'TTL',
    'class_job_' . AccidentRepairStatusEnum::TTL => 'secondary',

    'status_job_' . AccidentRepairStatusEnum::OFFER_NEW_PRICE => 'รอเสนอราคาใหม่',
    'class_job_' . AccidentRepairStatusEnum::OFFER_NEW_PRICE => 'primary',


    'status_ttl_' . OfferGMStatusEnum::OVER_PRICE => 'ซ่อมเกิน 200,000',
    'status_ttl_' . OfferGMStatusEnum::OFFER_NEW_PRICE => 'เสนอราคาใหม่',
    'status_ttl_' . OfferGMStatusEnum::CONSIDER_TOTAL_LOSS => 'พิจารณา Total loss',
    'status_ttl_' . OfferGMStatusEnum::CONSIDER_PARTIAL_LOSS => 'พิจารณา Partial loss',
 ];