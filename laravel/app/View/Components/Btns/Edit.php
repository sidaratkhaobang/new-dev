<?php

namespace App\View\Components\Btns;

use Illuminate\View\Component;

class Edit extends Component
{
    public $route_edit;

    public function __construct($routeEdit)
    {
        $this->route_edit = $routeEdit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.btns.edit');
    }
}
