<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Radio extends Component
{
    public $id;
    public $label;
    public $value;
    public $list;
    public $label_class;
    public $input_class;
    public $required;
    public $model;

    public function __construct($id, $label, $value, $list = [], $optionals = [])
    {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->list = $list;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-sm-2 text-end col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'col-sm-4 text-start input-pd');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
        $this->model = (isset($optionals['model']) ? $optionals['model'] : false);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.radio');
    }
}
