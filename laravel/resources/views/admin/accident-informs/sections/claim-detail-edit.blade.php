<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.claim_summary_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_claim_customer" :value="$d->amount_claim_customer" :label="__('accident_informs.customer_claim_amount')"
                    :optionals="[ 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_claim_tls" :value="$d->amount_claim_tls" :label="__('accident_informs.tls_claim_amount')" :optionals="[ 'type' => 'number', 'min' => 0]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="save_claim_amount" :value="null" :label="__('accident_informs.save_claim_amount')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="compensation" :value="$d->compensation" :label="__('accident_informs.compensation_payment')" :optionals="['input_class' => 'number-format']" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3" id="repair_type_id">
                <x-forms.select-option id="repair_type" :value="$d->repair_type" :list="$repair_list" :label="__('accident_informs.repair')"
                    :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
