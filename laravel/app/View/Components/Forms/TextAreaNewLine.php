<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class TextAreaNewLine extends Component
{
    public $id;
    public $value;
    public $label;
    public $row;
    public $sub_label;
    public $type;
    public $placeholder;
    public $maxlength;
    public $required;
    public $label_class;
    public $input_class;
    public $form_class;
    public $html;
    public $html_class;
    public $readonly;

    public function __construct($id, $value, $label, $optionals = [])
    {
        $this->id = $id;
        $this->value = $value;
        $this->label = $label;
        $this->row =  (isset($optionals['row']) ? $optionals['row'] : 4);
        $this->sub_label =  (isset($optionals['sub_label']) ? $optionals['sub_label'] : false);
        $this->placeholder = (isset($optionals['placeholder']) ? $optionals['placeholder'] : '');
        $this->maxlength = (isset($optionals['maxlength']) ? $optionals['maxlength'] : 100);
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'text-start col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'col-sm-12');
        $this->form_class = (isset($optionals['form_class']) ? $optionals['form_class'] : 'form-group row');
        $this->html = (isset($optionals['html']) ? $optionals['html'] : false);
        $this->html_class = (isset($optionals['html_class']) ? $optionals['html_class'] : 'js-summernote');
        $this->readonly = (isset($optionals['readonly']) ? $optionals['readonly'] : false);

        if ($this->id == 'tel') {
            $this->value = str_replace(",", "\n", str_replace(array("[", "]", "\""), "", $this->value));
        }
    }

    public function render()
    {
        return view('admin.components.forms.text-area-new-line');
    }
}
