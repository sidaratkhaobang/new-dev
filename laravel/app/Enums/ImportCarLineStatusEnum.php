<?php

namespace App\Enums;

abstract class ImportCarLineStatusEnum
{
    const PENDING = 'PENDING';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM_DATA = 'CONFIRM_DATA';
    const REJECT_DATA = 'REJECT_DATA';
    const PENDING_DELIVERY = 'PENDING_DELIVERY';
    const VENDOR_CONFIRM_DATA = 'VENDOR_CONFIRM_DATA';
    const SUCCESS_DELIVERY = 'SUCCESS_DELIVERY';
}
