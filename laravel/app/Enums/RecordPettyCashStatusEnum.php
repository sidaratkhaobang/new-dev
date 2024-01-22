<?php

namespace App\Enums;

abstract class RecordPettyCashStatusEnum
{
    const DRAFT = 'DRAFT'; // ร่าง
    const PENDING = 'PENDING'; // รอบัญชีตรวจสอบ
    const COMPLETE = 'COMPLETE'; // บันทึกเบิกเงินสดย่อยเสร็จสิ้น
}
