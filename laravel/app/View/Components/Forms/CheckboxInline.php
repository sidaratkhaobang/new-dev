<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class CheckboxInline extends Component
{
    public $id;
    public $list;
    public $label;
    public $value;
    public $label_class;
    public $input_class;
    public $required;

    public function __construct($id, $list, $label, $value, $optionals = [])
    {
        $this->id = $id;
        $this->list = $list;
        $this->label = $label;
        $this->value = $value;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'space-x-2');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
    }

    public function render()
    {
        return view('admin.components.forms.checkbox-inline');
    }
}
