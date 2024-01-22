<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('tax_renewals.tax_renew_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="send_tax_renew_date" :value="$d->tax_forwarding_date" :label="__('tax_renewals.send_tax_renew_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="provider" :list="$provider_list" :value="$d->provider" :label="__('tax_renewals.provider')" :optionals="['required' => true]" />
            </div>
            {{-- <div class="col-sm-3">
                <x-forms.date-input id="amount_day_wait_cmi" :value="$d->amount_day_wait_cmi" :label="__('tax_renewals.amount_day_wait_cmi')" />
            </div> --}}
        </div>
    </div>
</div>
