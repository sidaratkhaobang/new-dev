<?php

namespace App\Enums;

abstract class RepairStatusEnum
{
    const WAIT_OPEN_REPAIR_ORDER = 'WAIT_OPEN_REPAIR_ORDER'; // รอเปิดใบสั่งซ่อม
    // const WAIT_APPROVE_REPAIR_ORDER = 'WAIT_APPROVE_REPAIR_ORDER'; // รออนุมัติใบสั่งซ่อม
    // const REJECT_REPAIR_ORDER = 'REJECT_REPAIR_ORDER'; // ไม่อนุมัติใบสั่งซ่อม
    const PENDING_REPAIR = 'PENDING_REPAIR'; // รอดำเนินการซ่อม
    const WAIT_APPROVE_QUOTATION = 'WAIT_APPROVE_QUOTATION'; // รออนุมัติใบเสนอราคา
    const REJECT_QUOTATION = 'REJECT_QUOTATION'; // ไม่อนุมัติใบเสนอราคา
    const IN_PROCESS = 'IN_PROCESS'; // อยู่ระหว่างการซ่อม
    const COMPLETED = 'COMPLETED'; // ซ่อมเสร็จสิ้น
    const CANCEL = 'CANCEL'; // ยกเลิกใบสั่งซ่อม
    const EXPIRED = 'EXPIRED'; // ใบสั่งซ่อมหมดอายุ
}
