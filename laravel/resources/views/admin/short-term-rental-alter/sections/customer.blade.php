<h4>{{ __('short_term_rentals.customer_detail') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="customer_type" :value="$d->customer_type" :list="$customer_type_list" :label="__('customers.customer_type')"/>
    </div>
    <div class="col-sm-3">
        <div class="row push">
            <div class="col-sm-10">
                <label class="text-start col-form-label" for="customer_id">
                    {{ __('short_term_rentals.customer_code') }}
                    <span class="text-danger">*</span>
                </label>
                <select name="customer_id" id="customer_id" class="form-control js-select2-default" style="width: 100%;">
                    @if (!empty($d->customer_id))
                            <option value="{{ $d->customer_id }}">{{ $customer_code }}</option>
                        @endif
                </select>
            </div>
            <div class="col-sm-2 align-self-end px-0">
                <button type="button" class="btn btn-secondary" disabled onclick="openCustomerModal()">
                    <i class="fa fa-circle-plus"></i>
                </button>
            </div>
        </div>
        @include('admin.short-term-rental-info.modals.customer')
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('short_term_rentals.customer')" :optionals="['required' => true]" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_email" :value="$d->customer_email" :label="__('short_term_rentals.email')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_tel" :value="$d->customer_tel" :label="__('short_term_rentals.tel')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_province_id" :value="$d->customer_province_id" :list="$province_list" :label="__('short_term_rentals.province')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_zipcode" :value="$d->customer_zipcode" :label="__('short_term_rentals.zipcode')" />
    </div>
</div>
<div class="row push mb-3">
    <div class="col-sm-9">
        <x-forms.input-new-line id="customer_address" :value="$d->customer_address" :label="__('short_term_rentals.address')" />
    </div>
</div>
<h4>{{ __('short_term_rentals.tax_invoice_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-12">
        <x-forms.checkbox-inline id="check_customer_address" :list="[
            [
                'id' => 1,
                'name' => __('short_term_rentals.check_customer_address'),
                'value' => 1,
            ],
        ]" :label="null"
            :value="[$check_customer_address]" />
    </div>
</div>
