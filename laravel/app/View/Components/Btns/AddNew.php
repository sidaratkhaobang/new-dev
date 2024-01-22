<?php

namespace App\View\Components\Btns;

use Illuminate\View\Component;

class AddNew extends Component
{
    public $route_create;
    public $btn_text;

    public function __construct($routeCreate, $btnText)
    {
        $this->route_create = $routeCreate;
        $this->btn_text = $btnText;

    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.btns.add-new');
    }
}
