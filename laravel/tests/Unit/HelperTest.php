<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_phone()
    {
        $this->assertTrue(validate_phone('0865434423'));
        $this->assertTrue(validate_phone('021234567'));

        $this->assertFalse(validate_phone('02123456'));
        $this->assertFalse(validate_phone('0212345633343'));
        $this->assertFalse(validate_phone('4865434423'));
        $this->assertFalse(validate_phone('+66865434423'));
        $this->assertFalse(validate_phone('086-5434423'));
        $this->assertFalse(validate_phone('aaaaaaaaa'));
        $this->assertFalse(validate_phone('(086)9685543'));
        $this->assertFalse(validate_phone('0869685543 ต่อ 223'));
        $this->assertFalse(validate_phone('0s65434423'));
    }
}