<?php

namespace App\View\Components\ShortTermRental;

use App\Traits\RentalTrait;
use Illuminate\View\Component;

class StepAsset extends Component
{
    public $success;
    public $show;
    public $istoggle;
    public $showstep;
    public $car_list;
    public $status_list;

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
        $this->car_list = RentalTrait::getManagementRentalCars($rentalid);
        $this->status_list = RentalTrait::getRentalStatusList();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-asset');
    }
}
