{{-- <div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.select-option id="voucher_type" :value="null" :list="$voucher_type" :label="__('promotions.voucher_type')" />
    </div>
    <div class="col-sm-3" id="package_amount" style="display: none;">
        <x-forms.input-new-line id="package_amount" :value="null" :label="__('promotions.package_amount')" :optionals="['type' => 'number']" />
    </div>
    <div class="col-sm-3" id="voucher_amount" style="display: none;">
        <x-forms.input-new-line id="voucher_amount" :value="null" :label="__('promotions.voucher_amount')" :optionals="['type' => 'number']" />
    </div>
</div> --}}


<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="selling_price" :value="0" :label="__('promotions.selling_price')" :optionals="['input_class' => 'number-format col-sm-4','required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="start_sale_date" name="start_sale_date" :value="$d->start_sale_date" :label="__('promotions.start_sale_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="end_sale_date" name="end_sale_date" :value="$d->end_sale_date" :label="__('promotions.end_sale_date')" />
    </div>
</div>

<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.radio-inline id="pattern_code" :value="$d->pattern_code" :list="$pattern_list" :label="__('promotions.pattern_code')" :optionals="['required' => true]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="prefix_code" :value="$d->prefix_code" :label="__('promotions.prefix_code')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="code_digit" :value="0" :label="__('promotions.code_digit')" :optionals="['input_class' => 'number-format', 'required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="amount_code" :value="$d->amount_code" :label="__('promotions.amount_code_pack')" :optionals="['input_class' => 'number-format','required' => true]" />
    </div>
</div>
