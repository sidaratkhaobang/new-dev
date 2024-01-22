<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_orders.participant'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="contacts_tls" :value="$d->contacts_tls" :label="__('accident_orders.tls')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="contacts_insurance" :value="$d->contacts_insurance" :label="__('accident_orders.insurance')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="contacts_customer" :value="$d->contacts_customer" :label="__('accident_orders.customer_driver')" />
            </div>
        </div>
    </div>
</div>
