<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="customer_type" :value="$d->customer_type" :list="$customer_type_list" :label="__('customers.customer_type')" />
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label" for="customer_id">{{ __('long_term_rentals.customer_code') }}<span
                class="text-danger">*</span></label>
        <select name="customer_id" id="customer_id" class="form-control js-select2-default" style="width: 100%;">
            @if (!empty($d->customer_id))
                <option value="{{ $d->customer_id }}">{{ $customer_code }}</option>
            @endif
        </select>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('long_term_rentals.customer')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_tax" :value="$d->customer ? $d->customer->tax_no : null" :label="__('long_term_rentals.customer_tax')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_email" :value="$d->customer_email" :label="__('long_term_rentals.email')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_tel" :value="$d->customer_tel" :label="__('long_term_rentals.tel')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_province_id" :value="$d->customer_province_id" :list="$province_list" :label="__('long_term_rentals.province')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_zipcode" :value="$d->customer_zipcode" :label="__('long_term_rentals.zipcode')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-12">
        <x-forms.input-new-line id="customer_address" :value="$d->customer_address" :label="__('long_term_rentals.address')" />
    </div>
</div>
