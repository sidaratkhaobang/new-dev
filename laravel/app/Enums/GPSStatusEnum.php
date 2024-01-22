<?php

namespace App\Enums;

abstract class GPSStatusEnum
{
    const PENDING = 'PENDING'; //รอตรวจสอบ
    const NORMAL_SIGNAL = 'NORMAL_SIGNAL'; //สัญญาณปกติ
    const NO_SIGNAL = 'NO_SIGNAL'; //ไม่มีสัญญาณ
    const CHECK_SIGNAL = 'CHECK_SIGNAL'; //ตรวจสอบสัญญาณ
}
