<?php

namespace App\Enums;

abstract class RepairTypeEnum
{
    const CHECK_DISTANCE = 'CHECK_DISTANCE'; // เช็กระยะ
    const GENERAL_REPAIR = 'GENERAL_REPAIR'; // ซ่อมทั่วไป
    const CHECK_AND_REPAIR = 'CHECK_AND_REPAIR'; // เช็กระยะและซ่อมทั่วไป
}
