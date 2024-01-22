<?php

namespace App\Enums;

abstract class PaymentGatewayEnum
{
    const BBL_EDC = "BBL_EDC";
    const SCB_BILL_PAY = 'SCB_BILL_PAY';
    const APP_2C2P = 'APP_2C2P';
    const OMISE = 'OMISE';
    const MC_PAYMENT = 'MC_PAYMENT';
    const CHEQUE = 'CHEQUE';
}
