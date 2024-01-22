<?php

namespace App\View\Components\Approve;

use Illuminate\View\Component;
use App\Traits\HistoryTrait;

class StepApprove extends Component
{
    public $model;
    public $id;
    public $configenum;
    public $approve_line_list;
    public $approve;
    public $approve_line_logs;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($model, $id, $configenum = null)
    {
        $this->model = $model;
        $this->id = $id;
        $this->configenum = $configenum;

        $approve_line = HistoryTrait::getHistory($model, $id, $configenum);
        $this->approve_line_list = $approve_line['approve_line_list'];
        $this->approve = $approve_line['approve'];
        $this->approve_line_logs = $approve_line['approve_line_logs'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('admin.components.approves.step-progress');
    }
}
