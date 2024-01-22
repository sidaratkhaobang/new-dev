<?php

namespace App\Enums;

abstract class DebtCollectionStatusEnum
{
    const PENDING = 'PENDING'; //รอดำเนินการ
    const WAITING = 'WAITING'; //อยู่ระหว่างรอชำระ
    const COMPLETE = 'COMPLETE'; //ดำเนินการเสร็จสิ้น
    const OVERDUE = 'OVERDUE'; //ค้างชำระ
}
