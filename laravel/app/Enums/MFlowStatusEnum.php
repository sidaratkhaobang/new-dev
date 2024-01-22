<?php

namespace App\Enums;

abstract class MFlowStatusEnum
{
    const DRAFT = 'DRAFT'; // ร่าง
    const PENDING = 'PENDING'; // รอแจ้งผู้เช่า/ผู้เกี่ยวข้อง
    const IN_PROCESS = 'IN_PROCESS'; // รอชำระค่าปรับ
    const COMPLETE = 'COMPLETE'; // ดำเนินการเสร็จสิ้น
    const CLOSE = 'CLOSE'; // ปิดใบงาน M-Flow
}
