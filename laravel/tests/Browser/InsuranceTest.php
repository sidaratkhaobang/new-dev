<?php

namespace Tests\Browser;

use App\Models\Insurer;
use App\Models\User;
use App\Traits\InsuranceTrait;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InsuranceTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public $rental_duration_days;
    public $start_date;
    public $return_date;
    public $username;
    public $insurer_id;
    public $insurance_cancel_reason;
    public $cancel_remark;
    public $refund;
    public $refund_stamp;
    public $refund_vat;
    public $credit_note;
    public $beneficiary_id;
    public $sum_insured_car;
    public $sum_insured_accessory;
    public $tpbi_person;
    public $tpbi_aggregate;
    public $tppd_aggregate;
    public $deductible;
    public $own_damage;
    public $fire_and_theft;
    public $deductible_car;
    public $pa_driver;
    public $pa_passenger;
    public $medical_exp;
    public $bail_bond;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->duration_days = 30;
        $this->start_date = date('Y-m-d', strtotime('+1 days'));
        $this->end_date = date('Y-m-d', strtotime('+' . ($this->duration_days + 1) . ' days'));
        $this->insurer_id = Insurer::where('status', '1')?->inRandomOrder()?->first()?->id;
        $this->insurance_cancel_reason = 'Test01';
        $this->cancel_remark = 'Test01';
        $this->refund = 'Test01';
        $this->refund_stamp = 'Test01';
        $this->credit_note = 'Test01';
        $this->beneficiary_id = InsuranceTrait::getLeasingList()?->random(1)?->first()?->id;
        $this->sum_insured_car = '100';
        $this->sum_insured_accessory = '100';
        $this->tpbi_person = '100';
        $this->tpbi_aggregate = '100';
        $this->tppd_aggregate = '100';
        $this->deductible = '100';
        $this->own_damage = '100';
        $this->fire_and_theft = '100';
        $this->deductible_car = '100';
        $this->pa_driver = '100';
        $this->medical_exp = '100';
        $this->bail_bond = '100';
        $this->pa_passenger = '100';

    }

    public function testInsuranceRenewCmi()
    {
        $desiredElementSelector = '.car_cmi';
        $maxPages = 10;
        $this->browse(function (Browser $browser) use ($desiredElementSelector, $maxPages) {
            $currentPage = 1;
            while ($currentPage <= $maxPages) {
                $browser
                    ->loginAs(User::where('username', $this->username)->first())
                    ->visit('/admin/insurance-car?page=' . $currentPage)
                    ->pause(1000);
                $elements = $browser->elements($desiredElementSelector);
                if (!empty($elements)) {
                    $browser
                        ->click($desiredElementSelector);
                    $browser
                        ->script("return $('.car_cmi:checked').first().closest('.block-main-car').find('.btn-renew-cmi').click();");
                    $browser
                        ->value('#modal_renew_cmi_startdate', $this->start_date)
                        ->value('#modal_renew_cmi_enddate', $this->end_date)
                        ->select('modal_renew_insurance_company', $this->insurer_id);
                    $browser->screenshot('cmi_car_renew');
                    $browser->jsClick('.btn-save-form')
                        ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                        ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                        ->press('ตกลง')->screenshot('cmi_car_renew_success');
                    break;
                }
                $currentPage++;
            }
        });
    }

    public function testInsuranceRenewVmi()
    {
        $desiredElementSelector = '.car_vmi';
        $maxPages = 10;
        $this->browse(function (Browser $browser) use ($desiredElementSelector, $maxPages) {
            $currentPage = 1;
            while ($currentPage <= $maxPages) {
                $browser
                    ->loginAs(User::where('username', $this->username)->first())
                    ->visit('/admin/insurance-car?page=' . $currentPage)
                    ->pause(1000);
                $elements = $browser->elements($desiredElementSelector);
                if (!empty($elements)) {
                    $browser
                        ->click($desiredElementSelector);
                    $browser
                        ->script("return $('.car_vmi:checked').first().closest('.block-main-car').find('.btn-renew-vmi').click();");
                    $browser
                        ->value('#modal_renew_cmi_startdate', $this->start_date)
                        ->value('#modal_renew_cmi_enddate', $this->end_date)
                        ->select('modal_renew_insurance_company', $this->insurer_id);
                    $browser->jsClick('.btn-save-form')
                        ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                        ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย');
                    $browser->screenshot('vmi_car_renew_success');
                    $browser
                        ->press('ตกลง');
                    break;
                }
                $currentPage++;
            }
        });
    }

    public function testInsuranceRenewCmiAll()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car')
                ->pause(1000)
                ->script("return $('.btn-renew-all-cmi').first().click();");
            $browser->value('#modal_renew_cmi_startdate', $this->start_date)
                ->value('#modal_renew_cmi_enddate', $this->end_date)
                ->select('modal_renew_insurance_company', $this->insurer_id);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('cmi_car_renew_all_success');
        });

    }

    public function testInsuranceRenewVmiAll()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car')
                ->pause(1000)
                ->script("return $('.btn-renew-all-vmi').first().click();");
            $browser->value('#modal_renew_cmi_startdate', $this->start_date)
                ->value('#modal_renew_cmi_enddate', $this->end_date)
                ->select('modal_renew_insurance_company', $this->insurer_id);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('vmi_car_renew_all_success');
        });

    }

    public function testCmiShowPage()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("ดูข้อมูล");
            $browser->screenshot('screen_shot_show');

        });
    }

    public function testCmiEditPage()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข");
            $browser->screenshot('screen_shot_show');

        });
    }

    public function test_TC_CANCELLATION_INSURANCE_ACT()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car?status=UNDER_POLICY')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->jsClick('.btn-cancel-status')
                ->value('#insurance_cancel_date', $this->start_date)
                ->value('#insurance_cancel_reason', $this->insurance_cancel_reason);
            $browser->jsClick('.btn-save-cancel')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('insurance_cancle_test');
        });
    }

    public function test_TC_AFTER_CANCELLATION_INSURANCE_ACT()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/cancel-cmi-cars?status=REQUEST_CANCEL')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->value('#cancel_remark', $this->cancel_remark)
                ->value('#actual_cancel_date', $this->start_date)
                ->type('#refund', $this->refund)
                ->type('#refund_stamp', $this->refund_stamp)
                ->type('#refund_vat', $this->refund_vat)
                ->value('#credit_note', $this->credit_note)
                ->value('#credit_note_date', $this->start_date)
                ->value('#refund_check_date', $this->start_date)
                ->value('#send_account_date', $this->start_date);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('insurance_cancle_cmi');
        });

    }

    public function test_TC_EDIT_BENEFICIARY_NAME()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car?status=UNDER_POLICY&job_type=VMI')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->press('แก้ไข')
                ->clickLink('ผู้รับผลประโยชน์')
                ->select('#beneficiary_id', $this->beneficiary_id);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->script('window.scrollTo(0, 1000);');
            $browser->screenshot('insurance_request_change_vmi_1');
        });
    }

    public function test_TC_EDIT_INSURANCE_FUNDS()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car?status=UNDER_POLICY&job_type=VMI')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->press('แก้ไข')
                ->clickLink('ปรับวงเงินทุนประกัน')
                ->value('#sum_insured_car', $this->sum_insured_car)
                ->value('#sum_insured_accessory', $this->sum_insured_accessory);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->script('window.scrollTo(0, 1000);');
            $browser->screenshot('insurance_request_change_vmi_1');
        });
    }

    public function test_TC_EDIT_BUY_PROTECTION()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-car?status=UNDER_POLICY&job_type=VMI')
                ->pause(1000)
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->press('แก้ไข')
                ->clickLink('ซื้อความคุ้มครอง')
                ->clickLink('ข้อมูลความคุ้มครอง')
                ->value('#tpbi_person', $this->tpbi_person)
                ->value('#tpbi_aggregate', $this->tpbi_aggregate)
                ->value('#tppd_aggregate', $this->tppd_aggregate)
                ->value('#deductible', $this->deductible)
                ->value('#own_damage', $this->own_damage)
                ->value('#fire_and_theft', $this->fire_and_theft)
                ->value('#deductible_car', $this->deductible_car)
                ->value('#pa_driver', $this->pa_driver)
                ->value('#medical_exp', $this->medical_exp)
                ->value('#bail_bond', $this->bail_bond)
                ->value('#pa_passenger', $this->pa_passenger);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->script('window.scrollTo(0, 1000);');
            $browser->screenshot('insurance_request_change_vmi_1');
        });
    }
}
