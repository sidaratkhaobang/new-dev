<?php

namespace App\Enums;

abstract class ApproveStepEnum 
{
    const PENDING = 'PENDING';
    const CONFIRM = 'CONFIRM';
    const PENDING_PREVIOUS = 'PENDING_PREVIOUS'; 
    const REJECT = 'REJECT'; 
}