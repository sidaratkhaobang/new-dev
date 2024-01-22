<h4>{{ __('long_term_rentals.work_detail') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="worksheet_no" :value="$d->worksheet_no" :label="__('long_term_rentals.worksheet_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="job_type" :value="$d->lt_rental_type_id ? $d->lt_rental_type_id : null" :list="$lt_rental_type_list" :label="__('long_term_rentals.job_type')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="rental_duration" :value="$month" :list="$month_list" :label="__('long_term_rentals.rental_duration')"
        :optionals="['multiple' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="offer_date" :value="$d->offer_date" :label="__('long_term_rentals.offer_date')" :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('long_term_rentals.remark')" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.select-option id="customer_type" :value="$d->customer->customer_type" :list="$customer_type_list" :label="__('customers.customer_type')" />
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label" for="customer_id">{{ __('long_term_rentals.customer_code') }}</label>
        <select name="customer_id" id="customer_id" class="form-control js-select2-default" style="width: 100%;">
            @if (!empty($d->customer_id))
                <option value="{{ $d->customer_id }}">{{ $d->customer?->customer_code }}</option>
            @endif
        </select>
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('long_term_rentals.customer')" />
    </div>
</div>