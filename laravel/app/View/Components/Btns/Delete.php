<?php

namespace App\View\Components\Btns;

use Illuminate\View\Component;

class Delete extends Component
{
    public $route_delete;

    public function __construct($routeDelete)
    {
        $this->route_delete = $routeDelete;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.btns.delete');
    }
}
