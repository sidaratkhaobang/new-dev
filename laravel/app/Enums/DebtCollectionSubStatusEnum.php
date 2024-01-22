<?php

namespace App\Enums;

abstract class DebtCollectionSubStatusEnum
{
    const DONE = 'DONE'; //แจ้งเรียบร้อย
    const LITIGATION = 'LITIGATION'; //งานคดีความ
    const NOT_CONTACT = 'NOT_CONTACT'; //ติดต่อไม่ได้
}
