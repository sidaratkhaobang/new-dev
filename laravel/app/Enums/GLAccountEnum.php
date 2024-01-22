<?php

namespace App\Enums;

abstract class GLAccountEnum
{
    const WHT = "149051010";
    const DEFERRED_INCOME = '249210010';
    //const INCOME = ''; // calculate
    //const INPUT_TAX = ''; // TBC
    const OUTPUT_TAX = '249400080';
    const DEFERRED_OUTPUT_TAX = '249400060';
    const TRADE_RECEIVABLE = '110020010';
    const RECEIVABLE = '110020010';
    const SERVICE_FEE = '630080090';
    const INCOME = '410512002';
}
