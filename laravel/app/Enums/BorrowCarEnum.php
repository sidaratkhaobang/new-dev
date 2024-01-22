<?php

namespace App\Enums;

abstract class BorrowCarEnum
{
    const PENDING_REVIEW = 'PENDING_REVIEW'; // รออนุมัติการยืมรถ
    const CONFIRM = 'CONFIRM'; // อนุมัติการยืมรถ
    const REJECT = 'REJECT'; // ไม่ยืนยันการรับโอนในระบบ
    const PENDING_DELIVERY = 'PENDING_DELIVERY'; // รอส่งมอบ
    const IN_PROCESS = 'IN_PROCESS'; // อยู่ระหว่างการยืมรถ
    const SUCCESS = 'SUCCESS'; // จบงานการยืมรถ
}
