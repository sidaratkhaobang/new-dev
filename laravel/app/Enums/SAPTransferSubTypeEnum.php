<?php

namespace App\Enums;

abstract class SAPTransferSubTypeEnum
{
    // CASH_SALE_S_RENTAL
    const AFTER_PAYMENT = 'AFTER_PAYMENT'; //จ่ายเงินสำเร็จ
    const START_SERVICE = 'START_SERVICE'; //เริ่มให้บริการ
    const AFTER_SERVICE_INFORM = 'AFTER_SERVICE_INFORM'; //แจ้งยอด
    const AFTER_SERVICE_PAID = 'AFTER_SERVICE_PAID'; //จ่ายเงินสำเร็จของบริการ
    const PAYMENT_FEE = 'PAYMENT_FEE'; //ค่าธรรมเนียม
    const EXPIRED_COUPON = 'EXPIRED_COUPON'; //คูปองหมดอายุ
    const INVOICE_ISSUE = 'INVOICE_ISSUE'; //แจ้งหนี้
}
