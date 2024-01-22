<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function test_cal_vat()
    {
        $this->assertEquals(100, get_total_exclude_vat(107), 'debug message 1');
        $this->assertEquals(200, get_total_exclude_vat(214), 'debug message 2');
    }
}
