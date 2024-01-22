<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Radiotwoline extends Component
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
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-sm-4 text-right col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'col-sm-4 input-pd');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.radio-twoline');
    }
}
