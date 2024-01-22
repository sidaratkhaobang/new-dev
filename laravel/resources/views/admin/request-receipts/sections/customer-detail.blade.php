<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.checkbox-inline id="is_select_db_customer" :list="[
            [
                'id' => 1,
                'name' => __('request_receipts.is_select_db_customer'),
                'value' => 1,
            ],
        ]" :optionals="['input_class' => 'mt-4']" :label="null"
            :value="[$d?->is_select_db_customer]" />
    </div>

    <div class="col-sm-6" id="customer_id_section"
        @if ($d->is_select_db_customer == true) style="display: block;" @else  style="display: none;" @endif>
        <x-forms.select-option id="customer_id" :list="[]" :value="$d?->customer_id" :label="__('request_receipts.customer')"
            :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $d?->customer_text]" />
    </div>
    <div class="col-sm-6" id="customer_name_section"
        @if ($d->is_select_db_customer == false) style="display: block;" @else  style="display: none;" @endif>
        <x-forms.input-new-line id="customer_name" :value="$d?->customer_name" :optionals="['required' => true]" :label="__('request_receipts.customer_name')" />
    </div>

    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_tax_no" :value="$d?->customer_tax_no" :optionals="['required' => true, 'oninput' => true]" :label="__('request_receipts.customer_tax_no')" />
    </div>

</div>
<div class="row mb-4">
    <div class="col-sm-12">
        <x-forms.input-new-line id="customer_address" :value="$d?->customer_address" :optionals="['required' => true]" :label="__('request_receipts.customer_address')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="customer_province_id" :value="$d?->customer_province_id" :list="null" :label="__('garages.province')"
            :optionals="['ajax' => true, 'default_option_label' => $province_name]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_district_id" :value="$d?->customer_district_id" :list="null" :label="__('garages.amphure')"
            :optionals="['ajax' => true, 'default_option_label' => $amphure_name]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_subdistrict_id" :value="$d?->customer_subdistrict_id" :list="null" :label="__('garages.district')"
            :optionals="['ajax' => true, 'default_option_label' => $district_name]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_zipcode" :value="$customer_zipcode" :label="__('long_term_rentals.zipcode')" />
    </div>
</div>
