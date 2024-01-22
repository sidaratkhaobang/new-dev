<?php

namespace App\View\Components\Btns;

use Illuminate\View\Component;

class Search extends Component
{
    public function __construct()
    {

    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.btns.search');
    }
}
