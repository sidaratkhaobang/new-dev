<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('ownership_transfers.transfer_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="find_copy_chassis_date" :value="$d->find_copy_chassis_date" :label="__('ownership_transfers.find_copy_chassis_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="transfer_date" :value="$d->transfer_date" :label="__('ownership_transfers.transfer_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="estimate_transfered_date" :value="$d->estimate_transfered_date" :label="__('ownership_transfers.estimate_transfered_date')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_registration_book_date" :value="$d->receive_registration_book_date" :label="__('ownership_transfers.receive_registration_book_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_actual_transfer_date" :value="null" :label="__('ownership_transfers.amount_actual_transfer_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="car_ownership_date" :value="$d->car_ownership_date" :label="__('ownership_transfers.car_ownership_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="return_registration_book_date" :value="$d->return_registration_book_date" :label="__('ownership_transfers.return_registration_book_date')"
                    :optionals="['required' => true]" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.input-new-line id="link" :value="$d->link" :label="__('registers.link')" />
            </div>
        </div>
    </div>
</div>
