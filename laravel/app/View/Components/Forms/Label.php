<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Label extends Component
{
    public $id;
    public $value;
    public $label;
    public $label_class;
    public $text_class;

    public function __construct($id, $value, $label, $optionals = [])
    {
        $this->id = $id;
        $this->value = $value;
        $this->label = $label;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-form-label');
        $this->text_class = (isset($optionals['text_class']) ? $optionals['text_class'] : '');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.label');
    }
}
