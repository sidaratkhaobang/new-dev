<?php

namespace App\Enums;

abstract class CheckCreditStatusEnum
{
    const DRAFT = 'DRAFT';                      //ร่าง
    const PENDING_REVIEW = 'PENDING_REVIEW';    //รอตรวจสอบเครดิต
    const CONFIRM = 'CONFIRM';                  //อนุมัติ
    const REJECT = 'REJECT';                    //ไม่อนุมัติ
}
