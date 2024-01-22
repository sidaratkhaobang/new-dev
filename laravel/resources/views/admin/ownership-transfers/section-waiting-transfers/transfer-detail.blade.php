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
        </div>
      
    </div>
</div>
