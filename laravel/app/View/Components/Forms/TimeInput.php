<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class TimeInput extends Component
{
    public $id;
    public $value;
    public $label;
    public $type;
    public $placeholder;
    public $required;
    public $label_class;
    public $input_class;

    public function __construct($id, $value, $label, $optionals = [])
    {
        $this->id = $id;
        $this->value = $value;
        $this->label = $label;
        $this->type = (isset($optionals['type']) ? $optionals['type'] : 'text');
        $this->placeholder = (isset($optionals['placeholder']) ? $optionals['placeholder'] : '');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'text-start col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'js-flatpickr form-control flatpickr-input');
    }

    public function render()
    {
        return view('admin.components.forms.time-input');
    }
}
