<?php

namespace App\Enums;

abstract class LongTermRentalJobType
{
    const AUCTION = 'AUCTION'; // ยื่นซอง
    const QUOTATION = 'QUOTATION'; // เสนอราคาทั่วไป
    const EBIDDING = 'EBIDDING'; // EBidding
    const BUDGET = 'BUDGET'; // ขอราคาตั้งงบ 
    const OTHER = 'OTHER'; // นอกเหนือจากรายการข้างบน
}
