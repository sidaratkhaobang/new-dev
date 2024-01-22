<?php

namespace App\View\Components\Tables;

use Illuminate\View\Component;

class Dropdown extends Component
{
    public $id;
    public $edit_route;
    public $view_route;
    public $delete_route;
    public $view_permission;
    public $manage_permission;
    public $routes;

    public function __construct(
        $id = null,
        $editRoute = null,
        $viewRoute = null,
        $deleteRoute = null,
        $viewPermission = null,
        $managePermission = null,
        $routes = [],
        $optionals = []
    ) {
        $this->id = $id;
        $this->edit_route = isset($routes['edit_route']) ? $routes['edit_route'] : $editRoute;
        $this->view_route = isset($routes['view_route']) ? $routes['view_route'] : $viewRoute;
        $this->delete_route = isset($routes['delete_route']) ? $routes['delete_route'] : $deleteRoute;
        $this->view_permission = isset($routes['view_permission']) ? $routes['view_permission'] : $viewPermission;
        $this->manage_permission = isset($routes['manage_permission']) ? $routes['manage_permission'] : $managePermission;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.tables.dropdown');
    }
}
