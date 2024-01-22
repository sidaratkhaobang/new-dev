<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $username = env('USERNAME_TEST', '');
        $password = env('PASSWORD_TEST', '');

        $this->browse(function (Browser $browser) use ($username, $password) {
            $browser->visit('/')
                ->assertSee('เข้าสู่ระบบ')
                ->type('#username', $username)
                ->type('#password', $password)
                ->press('เข้าสู่ระบบ')
                ->assertPathIs('/admin/home');
        });
    }
}
