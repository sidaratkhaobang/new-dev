<?php

namespace App\Enums;

abstract class SAPTransferTypeEnum
{
    const CASH_SALE_S_RENTAL = 'CASH_SALE_S_RENTAL';
    const CASH_SALE_COUPON = 'CASH_SALE_COUPON';
    const CREDIT_S_RENTAL = 'CREDIT_S_RENTAL';
    const CREDIT_L_RENTAL = 'CREDIT_L_RENTAL';
    const BOAT_REPAIR = 'BOAT_REPAIR';
    const SALE_BOAT_PARTS = 'SALE_BOAT_PARTS';
    const DRIVER_EXCESS = 'DRIVER_EXCESS';
    const EARLY_RETURN_FINE = 'EARLY_RETURN_FINE';
    // const FINE_LOST_LABEL = 'FINE_LOST_LABEL';
    // const COMPENSATION_BENEFIT = 'COMPENSATION_BENEFIT';

    //AP
    const REPAIR_COST = 'REPAIR_COST'; // ค่าซ่อมรถ-ซ่อมบำรุง
    const TAX_COST = 'TAX_COST'; // ค่าต่อภาษี
    const INSURANCE_COST = 'INSURANCE_COST'; // ค่าเบี้ยประกัน
    const INSURANCE_RETURN_COST = 'INSURANCE_RETURN_COST'; // ค่าเบี้ยคืนประกัน
    const PATTY_CASH = 'PATTY_CASH'; // เบิกเงินสดย่อย
    const LOT_EQUIPMENT_CASH = 'LOT_EQUIPMENT_CASH'; // บันทึกจัด Lot ซื้ออุปกรณ์เงินสด
    const LOT_CAR_CASH = 'LOT_CAR_CASH'; // บันทึกจัด Lot รถเงินสด
    const LOT_CAR_LEASING = 'LOT_CAR_LEASING'; // บันทึกจัด Lot รถ Leasing
    const CLOSING_CONTRACT_CAR_EARLY = 'CLOSING_CONTRACT_CAR_EARLY'; // ปิดสัญญาเช่าซื้อรถก่อนกำหนด
    const FIRST_DAMAGE_COST = 'FIRST_DAMAGE_COST'; // บันทึกค่าเสียหายส่วนแรก
    const CAR_WASH_COST = 'CAR_WASH_COST'; // ค่าล้างรถ
    const IMPROVEMENT_COST = 'IMPROVEMENT_COST'; // ค่าปรับปรุงสภาพรถ
    const ACCIDENT_REPAIR_COST = 'ACCIDENT_REPAIR_COST'; // ค่าซ่อมอุบัติเหตุที่เรียกเก็บผู้ใช้รถ
    const OIL_FLEET_CARD_COST = 'OIL_FLEET_CARD_COST'; // ค่าน้ำมัน Fleet Card
    const DRIVER_COST = 'DRIVER_COST'; // ค่าพนักงานขับรถ Unity/Toyota/Para
    const COVERAGE_COST = 'COVERAGE_COST'; // ค่าคำประกันยื่นซอง/ประกวดราคา/สัญญาเช่า
    const OWNERSHIP_TRANSFER_COST = 'OWNERSHIP_TRANSFER_COST'; // ค่าโอนกรรมสิทธิ์รถ
    const BUY_BOAT_PARTS = 'BUY_BOAT_PARTS'; // บันทึกซื้ออะไหล่เรือ
}
