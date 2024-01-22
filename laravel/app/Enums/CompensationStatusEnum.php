<?php

namespace App\Enums;

abstract class CompensationStatusEnum
{
    const PENDING = 'PENDING';
    const DATA_COLLECTION = 'DATA_COLLECTION';
    const UNDER_NEGOTIATION = 'UNDER_NEGOTIATION';
    const END_NEGOTIATION = 'END_NEGOTIATION';
    // const PENDING_APPROVE = 'PENDING_APPROVE';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const COMPLETE = 'COMPLETE';
    const CANCEL_CLAIM = 'CANCEL_CLAIM';
    const REJECT = 'REJECT';
}
