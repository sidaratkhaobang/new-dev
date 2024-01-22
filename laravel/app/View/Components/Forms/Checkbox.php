<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public $id;
    public $label;
    public $value;
    public $list;
    public $label_class;
    public $input_class;
    public $extra_input;
    public $sub_label;
    public $name;
    public $required;

    public function __construct($id, $label, $value, $list = [], $optionals = [])
    {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->list = $list;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-sm-2 text-right col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'col-sm-4 input-pd');
        $this->extra_input = (isset($optionals['extra_input']) ? $optionals['extra_input'] : false);
        $this->sub_label = (isset($optionals['sub_label']) ? $optionals['sub_label'] : false);
        $this->name = (isset($optionals['name']) ? $optionals['name'] : false);
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.checkbox');
    }
}
