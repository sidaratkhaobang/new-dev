<?php

namespace Tests\Browser;

use App\Models\InsurancePackage;
use App\Models\Insurer;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InsuranceRequestPremiumTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public $username;
    public $sum_insured_car;
    public $sum_insured_accessories;
    public $sum_insured;
    public $insurer_id;
    public $insurance_package;
    public $premium_first_year;
    public $premium_per_year;
    public $compulsory_motor_insurance_premium;
    public $insurance_life_person;
    public $insurance_life_total;
    public $insurance_property;
    public $insurance_first;
    public $insurance_car_damage;
    public $insurance_car_accident;
    public $insurance_car_body;
    public $insurance_driver;
    public $insurance_passenger;
    public $insurance_healthcare;
    public $insurance_bail;

    function setUp(): void
    {
        parent::setUp();
        $this->username = env('USERNAME_TEST', '');
        $this->sum_insured_car = "10000";
        $this->sum_insured_accessories = "10000";
        $this->sum_insured = "10000";
        $this->insurer_id = Insurer::where('status', '1')?->inRandomOrder()?->first()?->id;
        $this->insurance_package = InsurancePackage::inRandomOrder()?->first()?->id;
        $this->premium_first_year = '10000';
        $this->premium_per_year = '10000';
        $this->compulsory_motor_insurance_premium = '10000';
        $this->insurance_life_person = '1000';
        $this->insurance_life_total = '1000';
        $this->insurance_property = '1000';
        $this->insurance_first = '1000';
        $this->insurance_car_damage = '1000';
        $this->insurance_car_accident = '1000';
        $this->insurance_car_body = '1000';
        $this->insurance_driver = '1000';
        $this->insurance_passenger = '1000';
        $this->insurance_healthcare = '1000';
        $this->insurance_bail = '1000';
    }

    public function test_TC_ADD_INSURANCES_COMPANIE_01()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs(User::where('username', $this->username)->first())
                ->visit('/admin/request-premium')
                ->pause(1000)
                ->assertSee('ขอค่าเบี้ยประกัน')
                ->click('#dropdown-dropleft-dark')
                ->clickLink("แก้ไข")
                ->assertSee('แก้ไขขอค่าเบี้ยประกัน')
                ->value('.sum_insured_car', $this->sum_insured_car)
                ->value('.sum_insured_accessories', $this->sum_insured_accessories)
                ->value('.sum_insured', $this->sum_insured)
                ->jsClick('.btn-nav-accordion');
            $carElements = $browser->elements('.block-car');
            $carsCount = count($carElements) - 1;
            for ($index = 0; $index < $carsCount; $index++) {
                $carSelectorInsurance = "car[data][" . $index . "][insurer_id]";
                $carSelectorInsurancePackage = "car[data][" . $index . "][insurance_package_id]";
                $browser->select($carSelectorInsurancePackage, $this->insurance_package);
                $browser->select($carSelectorInsurance, $this->insurer_id);
            }
            $browser->jsValueAll('.input-premium-first-year', $this->premium_first_year);
            $browser->jsValueAll('.input-premium-per-year', $this->premium_per_year);
            $browser->jsValueAll('.input-compulsory-motor-insurance-premium', $this->compulsory_motor_insurance_premium);
            $browser->jsKeyUp('.input-premium-first-year');
            $browser->jsKeyUp('.input-premium-per-year');
            $browser->jsKeyUp('.input-compulsory-motor-insurance-premium');
            $browser->jsValueAll('.input-insurance-life-person', $this->insurance_life_person);
            $browser->jsValueAll('.input-insurance-life-total', $this->insurance_life_total);
            $browser->jsValueAll('.input-insurance-property', $this->insurance_property);
            $browser->jsValueAll('.input-insurance-first', $this->insurance_first);
            $browser->jsValueAll('.input-insurance-car-damage', $this->insurance_car_damage);
            $browser->jsValueAll('.input-insurance-car-accident', $this->insurance_car_accident);
            $browser->jsValueAll('.input-insurance-car-body', $this->insurance_car_body);
            $browser->jsValueAll('.input-insurance-driver', $this->insurance_driver);
            $browser->jsValueAll('.input-insurance-passenger', $this->insurance_passenger);
            $browser->jsValueAll('.input-insurance-healthcare', $this->insurance_healthcare);
            $browser->jsValueAll('.input-insurance-bail', $this->insurance_bail);
            $browser->script('window.scrollTo(0, 2000);');
            $browser->jsClick('.btn-save-form')
                ->waitForTextIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->assertSeeIn('.swal2-container', 'บันทึกข้อมูลเรียบร้อย')
                ->press('ตกลง');
            $browser->screenshot('request_premium_edit');
        });
    }
}
