<?php

namespace App\Enums;

abstract class InsuranceStatusEnum
{
    const PENDING = 'PENDING';
    const IN_PROCESS = 'IN_PROCESS';
    const COMPLETE = 'COMPLETE';
    const REQUEST_CANCEL = 'REQUEST_CANCEL';
    const CANCEL = 'CANCEL';
}