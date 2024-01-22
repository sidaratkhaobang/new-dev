<?php

namespace App\Enums;

abstract class InsuranceCarStatusEnum
{
    const UNDER_POLICY = 'UNDER_POLICY';
    const  END_POLICY = 'END_POLICY';
    const  REQUEST_CANCEL = 'REQUEST_CANCEL';
    const  CANCEL_POLICY = 'CANCEL_POLICY';
    const  RENEW_POLICY = 'RENEW_POLICY';
}
