<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class UploadImageDropzone extends Component
{
    public $id;
    public $label;
    public $label_class;
    public $input_class;
    public $required;

    public function __construct($id, $label, $optionals = [])
    {
        $this->id = $id;
        $this->label = $label;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-sm-2 text-right col-form-label');
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : '');
        $this->required = (isset($optionals['required']) ? $optionals['required'] : false);
    }

    public function render()
    {
        return view('admin.components.forms.upload-image-dropzone');
    }
}
