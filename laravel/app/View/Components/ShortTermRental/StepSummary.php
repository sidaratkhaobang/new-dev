<?php

namespace App\View\Components\ShortTermRental;

use App\Models\RentalBill;
use Illuminate\View\Component;

class StepSummary extends Component
{
    public $success;
    public $show;
    public $istoggle;
    public $showstep;
    public $rental_bills;

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
        $this->rental_bills = RentalBill::select('rental_bills.*', 'quotations.id as quotation_id', 'quotations.qt_no')
            ->leftJoin('quotations', 'quotations.reference_id', '=', 'rental_bills.id')
            ->where('quotations.reference_type', RentalBill::class)
            ->where('rental_id', $rentalid)
            ->orderBy('bill_type')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.short-term-rental.step-summary');
    }
}
