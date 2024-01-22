<?php

namespace App\View\Components\Btns;

use Illuminate\View\Component;

class Icon extends Component
{
    public $route;
    public $btn_class;
    public $icon_class;
    public $btn_text;

    public function __construct($route = '', $btnClass = '', $iconClass = '', $btnText = '')
    {
        $this->route = $route;
        $this->btn_class = $btnClass;
        $this->icon_class = $iconClass;
        $this->btn_text = $btnText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.btns.icon');
    }
}
