<?php

namespace Tests\Browser;

use App\Models\Insurer;
use App\Models\User;
use App\Traits\InsuranceTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InsuranceCmiRenewTest extends DuskTestCase
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
    public $number_bar_cmi;
    public $policy_reference_cmi;
    public $endorse_cmi;
    public $premium;
    public $discount;
    public $stamp_duty;
    public $tax;
    public $statement_no;
    public $tax_invoice_no;
    public $status_pay_premium;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->type_vmi = InsuranceTrait::getTypeVMIList()?->random(1)?->first()?->id;
        $this->type_cmi = InsuranceTrait::getTypeCMIList()?->random(1)?->first()?->id;
        $this->sum_insured_car = '100';
        $this->sum_insured_accessory = '100';
        $this->insurer_id = Insurer::where('status', '1')?->inRandomOrder()?->first()?->id;
        $this->beneficiary_id = InsuranceTrait::getLeasingList()?->random(1)?->first()->id;
        $this->remark = 'ทดสอบAutomatTest';
        $this->send_date = Carbon::now()->format('Y-m-d H:i:s');
        $this->term_start_date = Carbon::now()->format('Y-m-d H:i:s');
        $this->term_end_date = Carbon::now()->addDays(30)->format('Y-m-d H:i:s');
        $this->number_bar_cmi = 'Test01';
        $this->policy_reference_cmi = 'Test01';
        $this->endorse_cmi = 'Test01';
        $this->premium = '1000';
        $this->discount = '1000';
        $this->stamp_duty = '1000';
        $this->tax = '1000';
        $this->statement_no = 'Test01';
        $this->tax_invoice_no = 'Test01';
        $this->status_pay_premium = InsuranceTrait::getPremiumPaymentStatus()?->random(1)?->first()->id;
    }

    public function test_TC_RECORD_INFORMATION_AFTER_RENEWAL_ACT()
    {

        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/insurance-cmi-renew?renew=RENEW&status=IN_PROCESS')
                ->assertSee('ต่ออายุ พรบ.')
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->value('#receive_date', $this->term_start_date)
                ->value('#check_date', $this->term_start_date)
                ->value('#number_bar_cmi', $this->number_bar_cmi)
                ->value('#policy_reference_cmi', $this->policy_reference_cmi)
                ->value('#endorse_cmi', $this->endorse_cmi)
                ->typeSlowly('#premium', $this->premium)
                ->typeSlowly('#discount', $this->discount)
                ->typeSlowly('#stamp_duty', $this->stamp_duty)
                ->typeSlowly('#tax', $this->tax)
                ->value('#statement_no', $this->statement_no)
                ->value('#tax_invoice_no', $this->tax_invoice_no)
                ->value('#statement_date', $this->term_start_date)
                ->value('#account_submission_date', $this->term_start_date)
                ->value('#operated_date', $this->term_start_date)
                ->select('#status_pay_premium', $this->status_pay_premium)
                ->select('#insurer_id', $this->insurer_id);
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('cmi_renew_register');
        });
    }
}
