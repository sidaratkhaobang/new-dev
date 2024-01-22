<?php

namespace App\Enums;

abstract class PRStatusEnum
{
    const DRAFT = 'DRAFT';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT';
    const CANCEL = 'CANCEL';
    const COMPLETE = 'COMPLETE';
}
