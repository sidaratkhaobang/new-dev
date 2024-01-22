<?php

namespace App\Enums;

abstract class TaxRenewalStatusEnum
{
    const PREPARE_DOCUMENT = 'PREPARE_DOCUMENT'; 
    const WAITING_SEND_TAX = 'WAITING_SEND_TAX'; 
    const RENEWING = 'RENEWING';
    const WAITING_TAX_REGISTER_BOOK = 'WAITING_TAX_REGISTER_BOOK';
    const SUCCESS = 'SUCCESS';
}

