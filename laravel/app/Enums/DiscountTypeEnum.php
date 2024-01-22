<?php

namespace App\Enums;

abstract class DiscountTypeEnum
{
    const PERCENT = 'PERCENT';
    const AMOUNT = 'AMOUNT';
    const FIXED_PRICE = 'FIXED_PRICE';
    const FREE_PRODUCT = 'FREE_PRODUCT';
    const FREE_ADDITIONAL_PRODUCT = 'FREE_ADDITIONAL_PRODUCT';
    const FREE_CAR_CLASS = 'FREE_CAR_CLASS';
}
