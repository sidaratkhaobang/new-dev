<?php

namespace App\View\Components\ShortTermRental;

use Illuminate\View\Component;
use App\Traits\RentalTrait;

class StepDriver extends Component
{
    use RentalTrait;

    public $success;
    public $show;
    public $istoggle;
    public $showstep;
    public $cars;

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
            $this->cars = RentalTrait::getRentalLineCarList($rentalid);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-driver');
    }
}
