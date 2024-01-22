<?php

namespace App\Repositories;

class SideBarHeading
{
    public $title;

    function __construct($title)
    {
        $this->title = $title;
    }

    function render()
    {
        return view('admin.components.sidebar-heading', [
            'title' => $this->title,
        ])->render();
    }
}
