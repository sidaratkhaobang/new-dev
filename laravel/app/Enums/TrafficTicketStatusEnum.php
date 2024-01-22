<?php

namespace App\Enums;

abstract class TrafficTicketStatusEnum
{
    const DRAFT = 'DRAFT';
    const GUITY_PENDING = 'GUITY_PENDING';
    const SEND_POLICE_PENDING = 'SEND_POLICE_PENDING';
    const PAYMENT_PENDING = 'PAYMENT_PENDING';
    const COMPLETE = 'COMPLETE';
}