<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ShortTermRentalTest extends DuskTestCase
{
    public $username;
    public $branch_id;
    public $product_id;
    public $pickup_date;
    public $return_date;
    public $rental_duration_days;
    public $origin_id;
    public $destination_id;
    public $customer_id;
    public $customer_name;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->branch_id = env('BRANCH_ID_TEST', '');
        $this->product_id = env('PRODUCT_ID_TEST', '');
        $this->rental_duration_days = 10;
        $this->pickup_date = date('Y-m-d', strtotime('+1 days')) . ' 12:00';
        $this->return_date = date('Y-m-d', strtotime('+' . ($this->rental_duration_days + 1) . ' days')) . ' 23:00';
        $this->origin_id = env('ORIGIN_ID_TEST', '');
        $this->destination_id = env('DESTINATION_ID_TEST', '');
        $this->customer_id = env('CUSTOMER_ID_TEST', '');
        $this->customer_name = env('CUSTOMER_NAME_TEST', '');
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/short-term-rentals')
                ->assertSeeIn('#main-container', 'เช่าระยะสั้น')
                ->assertSeeIn('#main-container', 'เพิ่มใบขอเช่า');

            $browser->clickLink('เพิ่มใบขอเช่า')
                ->assertSeeIn('#main-container', 'ประเภทงาน');

            $browser->click('@btn-0')
                ->press('ถัดไป')
                ->waitForTextIn('#main-container', 'ข้อมูลงาน')
                ->assertSeeIn('#main-container', 'ข้อมูลงาน')
                ->assertSeeIn('#main-container', 'ข้อมูลการจอง');

            $browser->select('branch_id', $this->branch_id)
                ->select2('#product_id', $this->product_id)
                ->value('#pickup_date', $this->pickup_date)
                ->value('#return_date', $this->return_date)
                ->select2('#origin_id', $this->origin_id)
                ->select2('#destination_id', $this->destination_id)

                ->select2('#customer_id', $this->customer_id)
                ->value('#customer_name', $this->customer_name)

                ->scrollIntoView('.btn-save-form')->waitFor('.btn-save-form', 5)
                // ->press('ถัดไป')
                ->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง')
                ->waitForTextIn('#main-container', 'เลือกรถเช่า')
                ->assertSeeIn('#main-container', 'เลือกรถเช่า');

            $browser->waitFor('.gantt-card', 10)
                ->click('@item-0')
                ->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง')
                ->waitForTextIn('#main-container', 'ข้อมูลผู้ขับขี่')
                ->assertSeeIn('#main-container', 'ข้อมูลผู้ขับขี่');


            $browser->scrollIntoView('.btn-save-form-data')->waitFor('.btn-save-form-data', 5)
                // ->press('ถัดไป')
                ->jsClick('.btn-save-form-data')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง')
                ->waitForTextIn('#main-container', 'เลือก Voucher')
                ->assertSeeIn('#main-container', 'เลือก Voucher');

            $browser->scrollIntoView('.btn-save-form')->waitFor('.btn-save-form', 5)->screenshot('aaa')
                ->press('ถัดไป')->screenshot('bbb')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง')
                ->waitForTextIn('#main-container', 'สรุปข้อมูล')
                ->assertSeeIn('#main-container', 'สรุปข้อมูล');
        });
    }
}
