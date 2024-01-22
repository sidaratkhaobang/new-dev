<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Tooltip extends Component
{
    public $title;
    public $limit;

    public function __construct($title, $limit = null)
    {
        $this->title = $title;
        $this->limit = $limit;
    }

    public function render()
    {
        return view('admin.components.forms.tooltip');
    }
}
