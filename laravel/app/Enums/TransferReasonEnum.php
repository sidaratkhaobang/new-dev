<?php

namespace App\Enums;

abstract class TransferReasonEnum
{
    const DELIVER_CUSTOMER = 'DELIVER_CUSTOMER';
    const RECEIVE_WAREHOUSE = 'RECEIVE_WAREHOUSE';
    const DELIVER_GARAGE = 'DELIVER_GARAGE';
    const RECEIVE_GARAGE = 'RECEIVE_GARAGE';
}
