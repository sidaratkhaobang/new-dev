<?php

namespace App\Enums;

abstract class AuctionRejectReason
{
    const AUCTION = 'AUCTION';
    const QUOTATION = 'QUOTATION';
    const EBIDDING = 'EBIDDING';
    const BUDGET = 'BUDGET';
}
