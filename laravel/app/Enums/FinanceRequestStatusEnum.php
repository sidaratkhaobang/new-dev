<?php

namespace App\Enums;

abstract class FinanceRequestStatusEnum
{
    const PENDING = 'PENDING';
    const PENDING_APPROVE = 'PENDING_APPROVE';
    const APPROVE = 'APPROVE';
    const REJECT = 'REJECT';
    const SUCCESS = 'SUCCESS';
}
