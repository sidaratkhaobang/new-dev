<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('tax_renewals.use_car_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="license_plate" :value="$d->receive_car_name ?? '-'" :label="__('tax_renewals.receive_car_name')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="rental_type" :value="$d->receive_car_tel ?? '-'" :label="__('tax_renewals.receive_car_tel')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="engine_size" :value="$d->recipient_name ?? '-'" :label="__('tax_renewals.use_car_name')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="fuel_type" :value="$d->tel ?? '-'" :label="__('tax_renewals.use_car_tel')" />
            </div>
        </div>
    </div>
</div>
