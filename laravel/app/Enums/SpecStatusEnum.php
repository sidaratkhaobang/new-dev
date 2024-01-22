<?php

namespace App\Enums;

abstract class SpecStatusEnum
{
    const DRAFT = 'DRAFT';
    const ACCESSORY_CHECK = 'ACCESSORY_CHECK';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const CONFIRM = 'CONFIRM';
    const REJECT = 'REJECT';
    const PENDING_CHECK = 'PENDING_CHECK'; //check car
    const CHANGE_CAR = 'CHANGE_CAR';
    const NO_DELIVERY = 'NO_DELIVERY';
}
