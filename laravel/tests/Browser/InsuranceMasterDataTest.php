<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InsuranceMasterDataTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public $username;
    public $insurance_id;
    public $insurance_th;
    public $insurance_en;
    public $website;
    public $insurance_phone;
    public $insurance_email;
    public $insurance_fax;
    public $address;
    public $coordinator_name;
    public $coordinator_email;
    public $coordinator_phone;
    public $remark;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->insurance_id = "InsuranceTest04";
        $this->insurance_th = "InsuranceTest04";
        $this->insurance_en = "InsuranceTest04";
        $this->website = null;
        $this->insurance_phone = null;
        $this->insurance_email = null;
        $this->insurance_fax = null;
        $this->address = null;
        $this->remark = null;
    }

//    InsuranceMasterData
    public function test_TC_ADD_INSURANCES_COMPANIE_01()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurances-companies')
                ->pause(1000)
                ->assertSee('บริษัทประกันภัย')
                ->clickLink('เพิ่มข้อมูล')
                ->assertSee('เพิ่มบริษัทประกันภัย')
                ->value('#insurance_id', $this->insurance_id)
                ->value('#insurance_th', $this->insurance_th)
                ->value('#insurance_en', $this->insurance_en)
                ->jsClick('#status1')
                ->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->screenshot('vmi_master_data_success')
                ->press('ตกลง');
        });
    }

    public function test_TC_ADD_INSURANCES_COMPANIE_02()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurances-companies')
                ->pause(1000)
                ->assertSee('บริษัทประกันภัย')
                ->clickLink('เพิ่มข้อมูล')
                ->assertSee('เพิ่มบริษัทประกันภัย')
                ->value('#insurance_id', $this->insurance_id)
                ->value('#insurance_th', $this->insurance_th)
                ->value('#insurance_en', $this->insurance_en)
                ->jsClick('#status2')
                ->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->screenshot('vmi_master_data_success')
                ->press('ตกลง');
        });
    }

    public function test_TC_EDIT_INSURANCES_COMPANIE()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurances-companies')
                ->pause(1000)
                ->assertSee('บริษัทประกันภัย')
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->value('#insurance_id', $this->insurance_id)
                ->value('#insurance_th', $this->insurance_th)
                ->value('#insurance_en', $this->insurance_en)
                ->jsClick('#status1')
                ->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง')
                ->assertSee($this->insurance_th)
                ->screenshot('vmi_master_data_edit_success');
        });
    }

    public function test_TC_DELETE_INSURANCES_COMPANIE()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurances-companies')
                ->pause(1000)
                ->assertSee('บริษัทประกันภัย')
                ->click('#dropdown-dropleft-dark')
                ->clickLink("ลบ")
                ->press('ตกลง')
                ->waitForTextIn('.swal2-container', 'ลบข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'ลบข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('vmi_master_data_delete_success');
        });
    }

}
