<?php

namespace App\View\Components\ShortTermRental;

use Illuminate\View\Component;

class SubmitGroup extends Component
{
    public $input_class;
    public $status;
    public $input_class_submit;
    public $isdraft;
    public $btn_name;
    public $btn_draft_name;
    public $data_status;
    public $icon_class_name;
    public $icon_draft_class_name;
    public $return_list;
    public $step;
    public $input_class_clear;
    public $rentalid;

    public function __construct($rentalid, $step = 0, $optionals = [])
    {
        $this->rentalid = $rentalid;
        $this->step = $step;
        $this->input_class = (isset($optionals['input_class']) ? $optionals['input_class'] : 'text-end');
        $this->status = (isset($optionals['status']) ? $optionals['status'] : null);
        $this->input_class_submit = (isset($optionals['input_class_submit']) ? $optionals['input_class_submit'] : 'btn-save-form');
        $this->isdraft = (isset($optionals['isdraft']) ? $optionals['isdraft'] : false);
        $this->btn_name = (isset($optionals['btn_name']) ? $optionals['btn_name'] : __('lang.save'));
        $this->btn_draft_name = (isset($optionals['btn_draft_name']) ? $optionals['btn_draft_name'] : __('lang.save_draft'));
        $this->data_status = (isset($optionals['data_status']) ? $optionals['data_status'] : null);
        $this->icon_class_name = (isset($optionals['icon_class_name']) ? $optionals['icon_class_name'] : 'icon-save');
        $this->icon_draft_class_name = (isset($optionals['icon_draft_class_name']) ? $optionals['icon_draft_class_name'] : 'icon-save');
        $this->input_class_clear = (isset($optionals['input_class_clear']) ? $optionals['input_class_clear'] : 'btn-cancel-status');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->return_list = [
            [
                'text' => __('short_term_rentals.step_title.service_type'),
                'url' => route('admin.short-term-rental.service-types.edit', ['rental_id' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.service_type'),
            ],
            [
                'text' => __('short_term_rentals.step_title.channel'),
                'url' => route('admin.short-term-rental-channel.edit', ['short_term_rental_channel' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.info'),
            ],
            [
                'text' => __('short_term_rentals.step_title.info'),
                'url' => route('admin.short-term-rental.info.edit', ['rental_id' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.info'),
            ],
            [
                'text' => __('short_term_rentals.step_title.asset'),
                'url' => route('admin.short-term-rental.asset.edit', ['rental_id' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.asset'),
            ],
            [
                'text' => __('short_term_rentals.step_title.driver'),
                'url' => route('admin.short-term-rental.driver.edit', ['rental_id' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.driver'),
            ],
            [
                'text' => __('short_term_rentals.step_title.promotion'),
                'url' => route('admin.short-term-rental.promotion.edit', ['rental_id' => $this->rentalid]),
                'icon_name' => __('short_term_rentals.step_icon.promotion'),
            ],
        ];
        return view('admin.components.short-term-rental.submit-group');
    }
}
