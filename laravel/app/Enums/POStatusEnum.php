<?php

namespace App\Enums;

abstract class POStatusEnum
{
    const DRAFT = 'DRAFT';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT'; //ไม่อนุมัติ
    const CANCEL = 'CANCEL'; // ยกเลิก
    const COMPLETE = 'COMPLETE';
}
