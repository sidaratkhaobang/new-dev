<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Progress extends Component
{
    public $type;
    public $step;

    public function __construct($type, $step)
    {
        $this->type = $type;
        $this->step = intval($step);
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $progress = config('progress');
        $step_flow = isset($progress[$this->type]) ? $progress[$this->type] : [];

        return view('admin.components.progress-bar-step', [
            'step_flow' => $step_flow,
        ]);
    }
}
