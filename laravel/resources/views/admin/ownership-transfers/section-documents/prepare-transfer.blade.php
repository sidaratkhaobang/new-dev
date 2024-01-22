<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.registered_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="request_transfer_kit_date" :value="$d->request_transfer_kit_date" :label="__('ownership_transfers.request_transfer_kit_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_transfer_kit_date" :value="$d->receive_transfer_kit_date" :label="__('ownership_transfers.receive_transfer_kit_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_wait_transfer_kit_date" :value="$d->amount_wait_transfer_kit_date" :label="__('ownership_transfers.amount_wait_transfer_kit_date')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="request_power_attorney_tls_date" :value="$d->request_power_attorney_tls_date" :label="__('ownership_transfers.request_power_attorney_tls_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_power_attorney_tls_date" :value="$d->receive_power_attorney_tls_date" :label="__('ownership_transfers.receive_power_attorney_tls_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_wait_power_attorney_tls_date" :value="$d->amount_wait_transfer_kit_date" :label="__('ownership_transfers.amount_wait_power_attorney_tls_date')" />
            </div>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_cmi" :value="$d->receive_cmi" :label="__('registers.receive_cmi')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_cmi" :value="$d->receive_cmi" :label="__('registers.receive_cmi')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="receive_cmi_amount" :value="$d->pr_no" :label="__('registers.receive_cmi_amount')" />
            </div>
        </div> --}}
       
    </div>
</div>
