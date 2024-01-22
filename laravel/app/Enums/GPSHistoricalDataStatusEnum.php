<?php

namespace App\Enums;

abstract class GPSHistoricalDataStatusEnum
{
    const DRAFT = 'DRAFT'; //ร่าง
    const REQUEST = 'REQUEST'; //ขอ
    const CONFIRM = 'CONFIRM'; //ยืนยัน
    const REJECT = 'REJECT'; //ไม่อนุมัติ
}
