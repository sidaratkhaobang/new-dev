<x-blocks.block :title="__('short_term_rentals.customer_detail')" :optionals="['is_toggle' => false]" >
    <x-slot name="options" >
        @if(!isset($edit_rental))
        <button type="button" class="btn btn-primary btn-mini" onclick="openCustomerModal()">
            <i class="fa fa-circle-plus"></i>
        </button>
        @endif
    </x-slot>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="customer_type" :value="$d->customer_type" :list="$customer_type_list" :label="__('customers.customer_type')"/>
        </div>
        <div class="col-sm-3">
            <label class="text-start col-form-label" for="customer_id">
                {{ __('short_term_rentals.customer_code') }}
                <span class="text-danger">*</span>
            </label>
            <select name="customer_id" id="customer_id" class="form-control js-select2-default"
                    style="width: 100%;">
                @if (!empty($d->customer_id))
                    <option value="{{ $d->customer_id }}">{{ $customer_name }}</option>
                @endif
            </select>
        </div>
        <div class="col-sm-6">
            <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('short_term_rentals.customer')" :optionals="['required' => true]"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.input-new-line id="customer_tax_no" :value="$d->customer_tax_no" :label="__('short_term_rentals.tax_no')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="customer_email" :value="$d->customer_email" :label="__('short_term_rentals.email')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="customer_tel" :value="$d->customer_tel" :label="__('short_term_rentals.tel')"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-12">
            <x-forms.text-area-new-line id="customer_address" :value="$d->customer_address" :label="__('short_term_rentals.address')"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <x-forms.select-option id="customer_province_id" :value="$d->customer_province_id" :list="$province_list" :label="__('short_term_rentals.province')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="customer_district_id" :value="$d->customer_district_id" :list="[]" :label="__('short_term_rentals.amphure')" :optionals="[
                'select_class' => 'js-select2-custom',
                'ajax' => true,
                'default_option_label' => $customer_district_name ?? null
            ]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="customer_subdistrict_id" :value="$d->customer_subdistrict_id" :list="[]" :label="__('short_term_rentals.tumbon')" :optionals="[
                'select_class' => 'js-select2-custom',
                'ajax' => true,
                'default_option_label' => $customer_subdistrict_name ?? null
            ]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="customer_zipcode" :value="$customer_zipcode" :label="__('short_term_rentals.zipcode')" :optionals="['readonly' => true]" />
        </div>
    </div>
</x-blocks.block>

@include('admin.short-term-rental-info.modals.customer')