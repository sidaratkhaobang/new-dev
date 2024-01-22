<?php

namespace App\Enums;

abstract class InstallEquipmentPOStatusEnum
{
    const PENDING_REVIEW = 'PENDING_REVIEW'; // รอรีวิว
    const CONFIRM = 'CONFIRM'; // อนุมัติ
    const REJECT = 'REJECT'; // ไม่อนุมัติ
    const CANCEL = 'CANCEL'; // ยกเลิก
    const COMPLETE = 'COMPLETE'; // เสร็จสิ้น
}
