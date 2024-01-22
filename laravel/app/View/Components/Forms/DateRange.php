<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class DateRange extends Component
{
    public $start_id;
    public $start_value;
    public $end_id;
    public $end_value;
    public $label;

    public function __construct($startId, $startValue, $endId, $endValue, $label, $optionals = [])
    {
        $this->start_id = $startId;
        $this->start_value = $startValue;
        $this->end_id = $endId;
        $this->end_value = $endValue;
        $this->label = $label;
    }

    public function render()
    {
        return view('admin.components.forms.date-range');
    }
}
