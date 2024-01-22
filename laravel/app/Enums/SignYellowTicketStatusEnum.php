<?php

namespace App\Enums;

abstract class SignYellowTicketStatusEnum
{
    const DRAFT = 'DRAFT'; 
    const WAITING_WRONG = 'WAITING_WRONG'; 
    const WAITING_PAY_DLT = 'WAITING_PAY_DLT';
    const WAITING_PAY_FINE = 'WAITING_PAY_FINE';
    const SUCCESS = 'SUCCESS';
}

