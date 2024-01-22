<?php

namespace App\Enums;

abstract class SAPStatusEnum
{
    const PENDING = 'PENDING';
    const SUCCESS = 'SUCCESS';
    const FAIL = 'FAIL';
    const CANCEL = 'CANCEL';
}