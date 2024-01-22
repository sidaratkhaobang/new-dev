<?php

namespace App\View\Components\ShortTermRental;

use Illuminate\View\Component;
use App\Traits\RentalTrait;

class StepPromotion extends Component
{
    use RentalTrait;

    public $success;
    public $show;
    public $istoggle;
    public $promotions;
    public $coupons;
    public $showstep;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rentalid, $success, $show = false, $istoggle = false, $showstep = true)
    {
        $this->success = $success;
        $this->istoggle = $istoggle;
        $this->show = $this->istoggle ? $show : false;
        $this->showstep = $showstep;
        if ($success) {
            $this->promotions = RentalTrait::getRentalLinePromotionList($rentalid);
            $this->coupons = RentalTrait::getRentalLineCouponList($rentalid);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-promotion');
    }
}
