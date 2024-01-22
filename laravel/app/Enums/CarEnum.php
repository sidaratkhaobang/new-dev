<?php

namespace App\Enums;

abstract class CarEnum
{
    const DRAFT = 'DRAFT'; // แบบร่าง
    const NEWCAR = 'NEWCAR'; //รถใหม่
    const NEWCAR_PENDING = 'NEWCAR_PENDING'; //กำลังเตรียมรถใหม่
    const EQUIPMENT = 'EQUIPMENT'; //รถติดตั้งอุปกรณ์
    const LEASE = 'LEASE'; //รถติดสัญญาเช่า
    const PENDING_RETURN = 'PENDING_RETURN'; //รถรอส่งคืน
    const ACCIDENT = 'ACCIDENT'; //รถอุบัติเหตุ
    const REPAIR = 'REPAIR'; //รถซ่อมบำรุง
    const PENDING_SALE = 'PENDING_SALE'; //รถรอส่งขาย
    const READY_TO_USE = 'READY_TO_USE'; //รถพร้อมใช้
    const CONTRACT_EXPIRED  = 'CONTRACT_EXPIRED'; //รถหมดสัญญา
    const SOLD_OUT = 'SOLD_OUT'; //ขายแล้ว

    const PENDING_REVIEW = 'PENDING_REVIEW'; //รถรอตรวจสอบ
    const PENDING_DELIVER = 'PENDING_DELIVER'; //รถรอส่งมอบ
}
