<?php

use App\Enums\SAPAccountTypeEnum;
use App\Enums\SAPInterfaceLineTypeEnum;
use App\Enums\SAPStatusEnum;
use App\Enums\SAPTransferSubTypeEnum;
use App\Enums\SAPTransferTypeEnum;

return [
    'page_title' => 'รายรับ/รายจ่าย',
    'account_type' => 'ประเภทบัญชี',
    'transfer_type' => 'ประเภทรายรับ/รายจ่าย',
    'income_transfer_type' => 'ประเภทรายได้',
    'transfer_sub_type' => 'ประเภทการบันทึก',
    'export_excel' => 'Export ไฟล์ excel',
    'download_excel' => 'ดาวน์โหลดไฟล์ Excel',
    'from_date' => 'จาก วันที่บันทึก Transection (Document Date)',
    'to_date' => 'ถึง วันที่บันทึก Transection (Document Date)',
    'range_date' => 'วันที่บันทึก Transaction (Document Date)',
    'save_date' => 'วันที่บันทึก',
    'total_items' => 'รายการทั้งหมด',

    'transfer_type_' . SAPTransferTypeEnum::CASH_SALE_S_RENTAL => 'การขายเงินสด เช่าระยะสั้น',
    'transfer_type_' . SAPTransferTypeEnum::CASH_SALE_COUPON => 'การขายคูปอง เงินสด/เงินโอน/บัตรเครดิต',
    'transfer_type_' . SAPTransferTypeEnum::CREDIT_S_RENTAL => 'การขายเงินเชื่อ เช่าระยะสั้น',
    'transfer_type_' . SAPTransferTypeEnum::CREDIT_L_RENTAL => 'การขายเงินเชื่อ เช่าระยะยาว',
    'transfer_type_' . SAPTransferTypeEnum::BOAT_REPAIR => 'รายได้ค่าซ่อมเรือ',
    'transfer_type_' . SAPTransferTypeEnum::SALE_BOAT_PARTS => 'การขายอะไหล่เรือ',
    'transfer_type_' . SAPTransferTypeEnum::DRIVER_EXCESS => 'ค่าส่วนเกินพนักงาน',
    'transfer_type_' . SAPTransferTypeEnum::EARLY_RETURN_FINE => 'ค่าปรับรถคืนก่อนกำหนด',

    'transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_PAYMENT => 'จ่ายเงินสำเร็จ',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::START_SERVICE => 'เริ่มให้บริการ',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM => 'แจ้งยอด',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_PAID => 'จ่ายเงินสำเร็จของบริการ',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::PAYMENT_FEE => 'ค่าธรรมเนียม',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::EXPIRED_COUPON => 'คูปองหมดอายุ',
    'transfer_sub_type_' . SAPTransferSubTypeEnum::INVOICE_ISSUE => 'แจ้งหนี้',

    'doc_type' => 'ประเภท Document Type',
    'status_' . SAPStatusEnum::PENDING => 'รอบันทึกเข้า SAP',
    'status_' . SAPStatusEnum::SUCCESS => 'บันทึกเข้า SAP',
    'status_' . SAPStatusEnum::FAIL => 'บันทึกเข้า SAP ไม่สำเร็จ',
    'status_' . SAPStatusEnum::CANCEL => 'ยกเลิกบันทึกเข้า SAP',
    'income_account' => 'บัญชีรายได้',

    //AP
    'expense_account' => 'บัญชีรายจ่าย',

    'transfer_type_' . SAPTransferTypeEnum::REPAIR_COST => 'ค่าซ่อมรถ-ซ่อมบำรุง',
    'transfer_type_' . SAPTransferTypeEnum::TAX_COST => 'ค่าต่อภาษี',
    'transfer_type_' . SAPTransferTypeEnum::INSURANCE_COST => 'ค่าเบี้ยประกัน',
    'transfer_type_' . SAPTransferTypeEnum::INSURANCE_RETURN_COST => 'ค่าเบี้ยคืนประกัน',
    'transfer_type_' . SAPTransferTypeEnum::PATTY_CASH => 'เบิกเงินสดย่อย',
    'transfer_type_' . SAPTransferTypeEnum::LOT_EQUIPMENT_CASH => 'บันทึกจัด Lot ซื้ออุปกรณ์เงินสด',
    'transfer_type_' . SAPTransferTypeEnum::LOT_CAR_CASH => 'บันทึกจัด Lot รถเงินสด',
    'transfer_type_' . SAPTransferTypeEnum::LOT_CAR_LEASING => 'บันทึกจัด Lot รถ Leasing',
    'transfer_type_' . SAPTransferTypeEnum::CLOSING_CONTRACT_CAR_EARLY => 'ปิดสัญญาเช่าซื้อรถก่อนกำหนด',
    'transfer_type_' . SAPTransferTypeEnum::FIRST_DAMAGE_COST => 'บันทึกค่าเสียหายส่วนแรก',
    'transfer_type_' . SAPTransferTypeEnum::CAR_WASH_COST => 'ค่าล้างรถ',
    'transfer_type_' . SAPTransferTypeEnum::IMPROVEMENT_COST => 'ค่าปรับปรุงสภาพรถ',
    'transfer_type_' . SAPTransferTypeEnum::ACCIDENT_REPAIR_COST => 'ค่าซ่อมอุบัติเหตุที่เรียกเก็บผู้ใช้รถ',
    'transfer_type_' . SAPTransferTypeEnum::OIL_FLEET_CARD_COST => 'ค่าน้ำมัน Fleet Card',
    'transfer_type_' . SAPTransferTypeEnum::DRIVER_COST => 'ค่าพนักงานขับรถ Unity/Toyota/Para',
    'transfer_type_' . SAPTransferTypeEnum::COVERAGE_COST => 'ค่าคำประกันยื่นซอง/ประกวดราคา/สัญญาเช่า',
    'transfer_type_' . SAPTransferTypeEnum::OWNERSHIP_TRANSFER_COST => 'ค่าโอนกรรมสิทธิ์รถ',
    'transfer_type_' . SAPTransferTypeEnum::BUY_BOAT_PARTS => 'บันทึกซื้ออะไหล่เรือ',

    'add_list' => 'เพิ่มรายการ'
];
