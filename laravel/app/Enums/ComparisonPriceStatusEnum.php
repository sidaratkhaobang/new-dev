<?php

namespace App\Enums;

abstract class ComparisonPriceStatusEnum
{
    const DRAFT = 'DRAFT';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT';
}
