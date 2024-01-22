<?php

namespace App\Enums;

abstract class DrivingJobStatusEnum
{
    const INITIAL = 'INITIAL';
    const PENDING = 'PENDING';
    const IN_PROCESS = 'IN_PROCESS';
    const COMPLETE = 'COMPLETE';
    const CANCEL = 'CANCEL';
}
