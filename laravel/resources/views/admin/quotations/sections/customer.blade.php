<h4>{{ __('quotations.customer_table') }}</h4>
<hr>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_type" :value="__('customers.type_' . $d->customer_type) " :label="__('quotations.customer_type')"  />
    </div>
    <div class="col-sm-3">
        <label class="text-start col-form-label" for="customer_id">{{ __('quotations.customer_code') }}</label>
        <select name="customer_id" id="customer_id" class="form-control js-select2-default" style="width: 100%;">
            @if (!empty($d->customer_id))
                <option value="{{ $d->customer_id }}">{{ $customer_code }}</option>
            @endif
        </select>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('quotations.customer_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_tel" :value="$d->customer_tel" :label="__('quotations.customer_tel')" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-12">
        <x-forms.input-new-line id="customer_address" :value="$d->customer_address" :label="__('quotations.customer_address')" />
    </div>
</div>
