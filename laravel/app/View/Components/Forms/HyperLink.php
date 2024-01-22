<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class HyperLink extends Component
{
    public $id;
    public $value;
    public $route;
    public $label;
    public $target;
    public $label_class;
    public $link_class;

    public function __construct($id, $label, $value, $route, $optionals = [])
    {
        $this->id = $id;
        $this->value = $value;
        $this->route = $route;
        $this->label = $label;
        $this->target = (isset($optionals['target']) ? $optionals['target'] : '_blank');
        $this->label_class = (isset($optionals['label_class']) ? $optionals['label_class'] : 'col-form-label');
        $this->link_class = (isset($optionals['link_class']) ? $optionals['link_class'] : '');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.forms.hyper_link');
    }
}
