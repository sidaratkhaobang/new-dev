<?php

namespace App\View\Components\ShortTermRental;

use App\Models\Rental;
use Illuminate\View\Component;

class StepChannel extends Component
{

    public $success;
    public $data_info;
    public $show;
    public $istoggle;
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
        $this->data_info = null;
        $this->showstep = $showstep;

        if ($success) {
            $this->data_info = Rental::find($rentalid);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-channel');
    }
}
