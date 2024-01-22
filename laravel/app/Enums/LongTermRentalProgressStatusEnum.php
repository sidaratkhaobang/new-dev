<?php

namespace App\Enums;

abstract class LongTermRentalProgressStatusEnum
{
    const COMPLETE = 'COMPLETE';
    const PROCESSING = 'PROCESSING';
    const SUCCESS_ORDER = 'SUCCESS_ORDER';
    const WAITING_DELIVERY = 'WAITING_DELIVERY';
    const DELIVERING = 'DELIVERING';
    // const INSTALLING = 'INSTALLING';
}
