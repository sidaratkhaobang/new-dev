<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class InlineRadio extends Component
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
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'space-x-2');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
        $this->model = (isset($optionals['model']) ? $optionals['model'] : false);
    }

    public function render()
    {
        return view('admin.components.forms.inline-radio');
    }
}
