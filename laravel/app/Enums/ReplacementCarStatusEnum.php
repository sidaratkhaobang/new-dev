<?php

namespace App\Enums;

abstract class ReplacementCarStatusEnum
{
    const DRAFT = 'DRAFT'; //ร่าง
    const PENDING_INSPECT = 'PENDING_INSPECT'; //รอตรวจสอบ
    const PENDING = 'PENDING'; //รอดำเนินการ
    const PENDING_REVIEW = 'PENDING_REVIEW'; //รออนุมัติ (กรณีสเปคต่ำ)
    const IN_PROCESS = 'IN_PROCESS'; //อยู่ระหว่างดำเนินการ
    const APPROVE = 'APPROVE'; //อนุมัติ ไม่ใช่แล้ว
    const REJECT = 'REJECT'; //ไม่อนุมัติ
    const CANCEL = 'CANCEL'; //ยกเลิก
    const COMPLETE = 'COMPLETE'; //ดำเนินการเสร็จสิ้น
}