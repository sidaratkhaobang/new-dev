<?php

namespace App\Enums;

abstract class ImportCarStatusEnum
{
    const PENDING = 'PENDING';
    const PENDING_REVIEW = 'PENDING_REVIEW';
    const SENT_REVIEW = 'SENT_REVIEW';
    const WAITING_DELIVERY = 'WAITING_DELIVERY';
    const DELIVERY_COMPLETE = 'DELIVERY_COMPLETE';
    const CANCEL = 'CANCEL';
}
