<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class Example2Test extends TestCase
{
    public function test_cal_subtotal()
    {
        $this->assertEquals(100, get_total_exclude_vat(107), 'debug message 1');
        $this->assertEquals(200, get_total_exclude_vat(214), 'debug message 2');
        $this->assertEquals(93.46, get_total_exclude_vat(100), 'debug message 3');
        $this->assertEquals(93.46, get_total_exclude_vat("100"), 'debug message 4');
        $this->assertEquals(934579.44, get_total_exclude_vat("1,000,000"), 'debug message 5');
        $this->assertEquals(0, get_total_exclude_vat([]), 'debug message 6');
        $this->assertIsFloat(934579.44, get_total_exclude_vat("1,000,000"), 'debug message 7');
    }
//
     public function test_cal_vat()
    {
        $this->assertEquals(7, get_vat_from_total(107));
        $this->assertEquals(14, get_vat_from_total(214));
        $this->assertEquals(6.54, get_vat_from_total(100));
        $this->assertEquals(6.54, get_vat_from_total("100"));
        $this->assertEquals(65420.56, get_vat_from_total("1,000,000"));
        $this->assertEquals(0, get_vat_from_total([]));
    }

    public function test_summary_total()
    {
        $this->assertEquals(107, (get_total_exclude_vat(107.00) + get_vat_from_total(107)));
        $this->assertEquals(100, (get_total_exclude_vat(100) + get_vat_from_total(100.00)));
        $this->assertEquals(1000000, (get_total_exclude_vat('1,000,000') + get_vat_from_total(1000000)));
    }
}
