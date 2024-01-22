<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class ViewImage extends Component
{
    public $id;
    public $label;
    public $label_class;
    public $list;

    public function __construct($id, $label, $list, $optionals = [])
    {
        $this->id = $id;
        $this->label = $label;
        $this->list = $list;
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'text-start col-form-label');
    }

    public function render()
    {
        return view('admin.components.forms.view-image');
    }
}
