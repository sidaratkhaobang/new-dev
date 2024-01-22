<?php

namespace App\Enums;

abstract class RequestReceiptStatusEnum
{
    const DRAFT = 'DRAFT'; 
    const WAITING_RECEIPT = 'WAITING_RECEIPT'; 
    const SUCCESS_RECEIPT = 'SUCCESS_RECEIPT';
}

