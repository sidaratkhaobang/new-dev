<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GanttChart extends Component
{
    public $id;
    public $searchable;
    public $search_text;
    public $status_list;
    public $table_header;
    public $item_list;
    public $available_item_ids;
    public $check_available;
    public $select_multiple;
    public $show_count;
    public $count_unit;
    public $show_navigate_btn;
    public $start_date;
    public $end_date;
    public $can_select;
    public function __construct($id, $searchable = false, $statusList = [], $tableHeader, $itemList, $optionals = [])
    {
        $this->id = $id;
        $this->searchable = $searchable;
        $this->search_text = isset($optionals['search_text']) ? $optionals['search_text'] : null;
        $this->status_list = $statusList;
        $this->table_header = $tableHeader;
        $this->item_list = $itemList;
        $this->available_item_ids = isset($optionals['available_item_ids']) ? $optionals['available_item_ids'] : null;
        $this->select_multiple = isset($optionals['select_multiple']) ? $optionals['select_multiple'] : false;
        $this->show_count = isset($optionals['show_count']) ? $optionals['show_count'] : false;
        $this->count_unit = isset($optionals['count_unit']) ? $optionals['count_unit'] : null;
        $this->show_navigate_btn = isset($optionals['show_navigate_btn']) ? $optionals['show_navigate_btn'] : false;
        $this->start_date = isset($optionals['start_date']) ? $optionals['start_date'] : null;
        $this->end_date = isset($optionals['end_date']) ? $optionals['end_date'] : null;
        $this->can_select = isset($optionals['can_select']) ? $optionals['can_select'] : true;
    }

    public function render()
    {
        return view('admin.components.gantts.gantt-chart');
    }
}