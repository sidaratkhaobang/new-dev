<?php

namespace App\Enums;

abstract class TransferCarEnum
{
    const WAITING_RECEIVE = 'WAITING_RECEIVE'; // รอยืนยันรับโอนในระบบ
    const CONFIRM_RECEIVE = 'CONFIRM_RECEIVE'; // ยืนยันการรับโอนในระบบ
    const REJECT_RECEIVE = 'REJECT_RECEIVE'; // ไม่ยืนยันการรับโอนในระบบ
    const IN_PROCESS = 'IN_PROCESS'; // อยู่ระหว่างการรับ/ส่งรถ
    const SUCCESS = 'SUCCESS'; // โอนรถสำเร็จ
}
