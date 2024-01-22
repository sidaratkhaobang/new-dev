<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalculateController extends Controller
{
    const VAT_RATE = 7;
    function getVatFromGrandTotal($grand_total)
    { 
        return round(((float)$grand_total * CalculateController::VAT_RATE) / 107, 2);
    }

    function getVatExcludePriceTotal($price)
    { 
        return round(((float)$price * 100) / (100 + CalculateController::VAT_RATE));
    }
}
