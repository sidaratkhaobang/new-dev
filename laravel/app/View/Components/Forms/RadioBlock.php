<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class RadioBlock extends Component
{
    public $id;
    public $name;
    public $value;
    public $selected;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $name, $value, $selected = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.radio-block');
    }
}
