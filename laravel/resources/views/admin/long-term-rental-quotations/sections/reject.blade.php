<h4>{{ __('long_term_rentals.reject_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="col-sm-6">
        <x-forms.input-new-line id="reason" :value="$d->quotation->reject_reason" :label="__('purchase_orders.disapprove_reason')" />
    </div>
</div>