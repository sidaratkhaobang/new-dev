<?php

namespace App\Enums;

abstract class QuotationStatusEnum
{
    const DRAFT = 'DRAFT';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT';
    const CANCEL = 'CANCEL';
}
