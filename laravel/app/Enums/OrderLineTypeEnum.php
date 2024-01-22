<?php

namespace App\Enums;

abstract class OrderLineTypeEnum
{
    const PRODUCT = 'PRODUCT';
    const PRODUCT_DIFF = 'PRODUCT_DIFF'; // ส่วนต่างแพคเกจ
    const ADDITIONAL_PRODUCT = 'ADDITIONAL_PRODUCT';
    const ADDITIONAL_PRODUCT_COST = 'ADDITIONAL_PRODUCT_COST';
    const ADDITIONAL_PRODUCT_DIFF_COST = 'ADDITIONAL_PRODUCT_DIFF_COST';
    const EXTRA = 'EXTRA';
    const OTHER = 'OTHER';
}
