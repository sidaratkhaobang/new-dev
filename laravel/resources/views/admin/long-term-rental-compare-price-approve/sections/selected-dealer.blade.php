<h4>{{ __('long_term_rentals.selected_dealers') }}</h4>
<hr>
<div class="row push">
    <div class="col-sm-6">
        <x-forms.select-option id="ordered_creditor_id" :value="null" :list="null" :label="__('purchase_orders.dealer')"
            :optionals="['required' => true]" />
    </div>
</div>
