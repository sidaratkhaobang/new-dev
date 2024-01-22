<?php

namespace Tests\Browser;

use App\Models\InsurancePackage;
use App\Models\Insurer;
use App\Models\User;
use App\Traits\InsuranceTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InsuranceVmiRenewTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public $username;
    public $type_vmi;
    public $type_cmi;
    public $sum_insured_car;
    public $sum_insured_accessory;
    public $insurer_id;
    public $beneficiary_id;
    public $remark;
    public $send_date;
    public $term_start_date;
    public $term_end_date;
    public $insurance_type;
    public $insurance_package_id;
    public $policy_reference_vmi;
    public $endorse_vmi;
    public $premium;
    public $discount;
    public $stamp_duty;
    public $tax;
    public $statement_no;
    public $tax_invoice_no;
    public $status_pay_premium;
    public $policy_reference_child_vmi;
    public $pa;
    public $pa_and_bb;
    public $pa_per_endorsement;
    public $pa_total_premium;
    public $id_deductible;
    public $discount_deductible;
    public $fit_discount;
    public $fleet_discount;
    public $ncb;
    public $good_vmi;
    public $bad_vmi;
    public $other_discount_percent;
    public $other_discount;
    public $gps_discount;
    public $total_discount;
    public $net_discount;
    public $cct;
    public $gross;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->type_vmi = InsuranceTrait::getTypeVMIList()?->random(1)?->first()?->id;
        $this->type_cmi = InsuranceTrait::getTypeCMIList()?->random(1)?->first()?->id;
        $this->sum_insured_car = '100';
        $this->sum_insured_accessory = '100';
        $this->insurer_id = Insurer::where('status', '1')?->inRandomOrder()?->first()?->id;
        $this->beneficiary_id = InsuranceTrait::getLeasingList()?->random(1)?->first()?->id;
        $this->remark = 'ทดสอบAutomatTest';
        $this->send_date = Carbon::now()->format('Y-m-d H:i:s');
        $this->term_start_date = Carbon::now()->format('Y-m-d H:i:s');
        $this->term_end_date = Carbon::now()->addDays(30)->format('Y-m-d H:i:s');
        $this->insurance_type = InsuranceTrait::getInsuranceTypeList()?->random(1)?->first()?->id;
        $this->insurance_package_id = InsurancePackage::inRandomOrder()?->first()?->id;
        $this->policy_reference_vmi = 'Test01';
        $this->endorse_vmi = 'Test01';
        $this->premium = '1000';
        $this->discount = '1000';
        $this->stamp_duty = '1000';
        $this->tax = '1000';
        $this->statement_no = 'Test01';
        $this->tax_invoice_no = 'Test01';
        $this->status_pay_premium = InsuranceTrait::getPremiumPaymentStatus()?->random(1)?->first()->id;
        $this->policy_reference_child_vmi = 'Test01';
        $this->pa = '100';
        $this->pa_and_bb = '100';
        $this->pa_per_endorsement = '100';
        $this->pa_total_premium = '100';
        $this->id_deductible = '100';
        $this->discount_deductible = '100';
        $this->fit_discount = '100';
        $this->fleet_discount = '100';
        $this->ncb = '100';
        $this->good_vmi = '100';
        $this->bad_vmi = '100';
        $this->other_discount_percent = '100';
        $this->other_discount = '100';
        $this->gps_discount = '100';
        $this->total_discount = '100';
        $this->net_discount = '100';
        $this->cct = '100';
        $this->gross = '100';
    }

    public function test_TC_RECORD_INFORMATION_AFTER_RENEWAL_INSURANCE()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-vmi-renew?renew=RENEW&status=IN_PROCESS')
                ->assertSee('ต่ออายุประกัน')
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->value('#receive_date', $this->term_start_date)
                ->value('#check_date', $this->term_start_date)
                ->value('#policy_reference_vmi', $this->policy_reference_vmi)
                ->value('#endorse_vmi', $this->endorse_vmi)
                ->value('#policy_reference_child_vmi', $this->policy_reference_child_vmi)
                ->typeSlowly('#premium', $this->premium)
                ->typeSlowly('#discount', $this->discount)
                ->typeSlowly('#stamp_duty', $this->stamp_duty)
                ->typeSlowly('#tax', $this->tax)
                ->value('#statement_no', $this->statement_no)
                ->value('#tax_invoice_no', $this->tax_invoice_no)
                ->value('#statement_date', $this->term_start_date)
                ->value('#account_submission_date', $this->term_start_date)
                ->value('#operated_date', $this->term_start_date)
                ->value('#pa', $this->pa)
                ->value('#pa_and_bb', $this->pa_and_bb)
                ->value('#pa_per_endorsement', $this->pa_per_endorsement)
                ->value('#pa_total_premium', $this->pa_total_premium)
                ->value('#id_deductible', $this->id_deductible)
                ->value('#discount_deductible', $this->discount_deductible)
                ->value('#fit_discount', $this->fit_discount)
                ->value('#fleet_discount', $this->fleet_discount)
                ->value('#ncb', $this->ncb)
                ->value('#good_vmi', $this->good_vmi)
                ->value('#bad_vmi', $this->bad_vmi)
                ->value('#other_discount_percent', $this->other_discount_percent)
                ->value('#other_discount', $this->other_discount)
                ->value('#gps_discount', $this->gps_discount)
                ->value('#total_discount', $this->total_discount)
                ->value('#net_discount', $this->net_discount)
                ->value('#net_discount', $this->net_discount)
                ->value('#cct', $this->cct)
                ->value('#gross', $this->gross)
                ->select('#status_pay_premium', $this->status_pay_premium)
                ->select('#insurer_id', $this->insurer_id);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('vmi_register');
        });
    }
}
