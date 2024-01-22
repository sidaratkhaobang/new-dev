<div class="row push">
    <div class="col-sm-4">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('products.name')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="service_type_id" :value="$d->service_type_id" :list="$service_types" :label="__('products.service_type')" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-4">
        <x-forms.radio-inline id="calculate_type" :value="$d->calculate_type" :list="$calculate_type_list" :optionals="['required' => true]" :label="__('products.calculate_type')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="standard_price" :value="number_format($d->standard_price,2)" :label="__('products.standard_price')" :optionals="['required' => true,'input_class' => 'number-format col-sm-4']" />
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branches" :label="__('products.branch')" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-4">
        <x-forms.checkbox-inline id="reserve_date" :list="$days" :label="__('products.reserve_date')" :value="$booking_day_arr" />
    </div>
    <div class="col-sm-3">
        <x-forms.time-input id="start_booking_time" :value="$d->start_booking_time" :label="__('products.start_booking_time')" />
    </div>
    <div class="col-sm-3">
        <x-forms.time-input id="end_booking_time" :value="$d->end_booking_time" :label="__('products.end_booking_time')" />
    </div>
    <div class="col-sm-2">
        <x-forms.input-new-line id="reserve_booking_duration" :value="$d->reserve_booking_duration" :label="__('products.reserve_booking_duration')" :optionals="['input_class' => 'number-format col-sm-4']" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-4">
        <x-forms.radio-inline id="status" :value="$d->status" :list="$status_list" :optionals="['required' => true]" :label="__('lang.status')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="start_date" :value="$d->start_date" :label="__('products.start_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="end_date" :value="$d->end_date" :label="__('products.end_date')" />
    </div>
    <div class="col-sm-2">
        <x-forms.radio-inline id="is_used_application" :value="$d->is_used_application" :list="$yes_no_list" :label="__('products.is_used_application')" :optionals="['required' => true]" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-4">
        <x-forms.input-new-line id="fix_days" :value="$d->fix_days" :label="__('products.fix_days')" :optionals="['input_class' => 'number-format col-sm-4']" />
    </div>
    <div class="col-sm-3">
        <x-forms.time-input id="fix_return_time" :value="$d->fix_return_time" :label="__('products.fix_return_time')" />
    </div>
    <div class="col-sm-5">
        <x-forms.select-option id="gl_account[]" :value="$gl_account" :list="$gl_account_list" :label="__('products.gl_account')" :optionals="['multiple' => true]" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-7">
        <x-forms.select-option id="car_type[]" :value="$car_type" :list="$car_type_list" :label="__('products.car_type')" :optionals="['multiple' => true]" />
    </div>
</div>