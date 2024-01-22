<?php

namespace App\Enums;

abstract class LitigationStatusEnum
{
    const PENDING = 'PENDING';
    const IN_PROCESS = 'IN_PROCESS';
    const FOLLOW = 'FOLLOW';
    const COMPLETE = 'COMPLETE';
    // const PENDING_APPROVE = 'PENDING_APPROVE';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    // const APPROVE = 'APPROVE';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT';
}