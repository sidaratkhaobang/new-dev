<?php

namespace App\Enums;

abstract class ReceiptLineNameEnum
{
    const CAR_RENTAL = 'CAR_RENTAL'; //ค่าเช่ารถยนต์
    const VOUCHER_OF_CASH = 'VOUCHER_OF_CASH'; //ค่าบัตรกำนัลแทนเงินสด
    const OTHER = 'OTHER'; //ค่าอื่น ๆ
}
