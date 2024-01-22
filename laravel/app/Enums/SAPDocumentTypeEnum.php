<?php

namespace App\Enums;

abstract class SAPDocumentTypeEnum
{
    //AR
    const D1 = 'D1'; // ใบแจ้งหนี้
    const D2 = 'D2'; // ใบลดหนี้
    const DN = 'DN'; // ใบเสร็จรับเงิน/ ใบกำกับ
    const DK = 'DK'; // ใบเสร็จรับเงิน (AUTO ตัดบัญชี AR)

    //AP
    const KR = 'KR'; // Payment Voucher
    const KA = 'KA'; // รายการปรับปรุง
}
