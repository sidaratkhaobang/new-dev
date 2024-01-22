<?php

namespace App\Enums;

abstract class RepairEnum
{
    //informer type
    const CUSTOMER = 'CUSTOMER'; // ลูกค้า
    const TLS = 'TLS'; // พนักงาน TLS

    //open by
    const CALL_CENTER = 'CALL_CENTER'; // Call Center
    const REPAIR_DEPARTMENT = 'REPAIR_DEPARTMENT'; // ฝ่ายซ่อมบำรุง

    //order line
    const ADD_ON = 'ADD_ON'; // เพิ่มเข้าไปใหม่
    const MASTER = 'MASTER'; // ดึงจาก master
}
