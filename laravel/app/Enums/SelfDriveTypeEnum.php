<?php

namespace App\Enums;

abstract class SelfDriveTypeEnum
{
    const SEND = 'SEND';  //ส่งรถให้ลูกค้า
    const PICKUP = 'PICKUP';  //รับรถจากลูกค้า
    const OTHER = 'OTHER';
    const SELF_DRIVE = 'SELF_DRIVE';
}
