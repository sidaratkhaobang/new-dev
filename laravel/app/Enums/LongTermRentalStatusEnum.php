<?php

namespace App\Enums;

abstract class LongTermRentalStatusEnum
{
    const NEW = 'NEW';
    const SPECIFICATION = 'SPECIFICATION';
    const COMPARISON_PRICE = 'COMPARISON_PRICE';
    const RENTAL_PRICE = 'RENTAL_PRICE';
    const QUOTATION = 'QUOTATION';
    const CONFIRM = 'CONFIRM';
    const QUOTATION_CONFIRM = 'QUOTATION_CONFIRM';
    const COMPLETE = 'COMPLETE';
    const CANCEL = 'CANCEL';
}
