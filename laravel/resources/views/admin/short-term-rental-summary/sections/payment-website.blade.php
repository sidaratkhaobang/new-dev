<div class="row">
    {{-- <h4>{{ __('short_term_rentals.payment') }}</h4> --}}
    <div class="col-sm-12">
        <x-forms.hidden id="order_channel" :value="$order_channel" />
    </div>
    <div class="col-sm-4" id="payment_gateway_id">
        <x-forms.select-option id="payment_gateway" :value="$d->payment_gateway" :list="$payment_gateway_list" :label="__('short_term_rentals.payment_method')" />
    </div>
    <div class="col-sm-4">
        <x-forms.date-input id="payment_date" :value="$d->payment_date" :label="__('short_term_rentals.paid_date')" :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-4">
        @if (isset($view))
            <x-forms.view-image :id="'ref_sheet_image'" :label="__('short_term_rentals.paid_file')" :list="null" />
        @else
            <x-forms.upload-image :id="'ref_sheet_image'" :label="__('short_term_rentals.paid_file')" />
        @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-12" id="payment_remark_id">
        <x-forms.input-new-line id="payment_remark" :value="$d->payment_remark" :label="__('lang.remark')" />
    </div>
</div>