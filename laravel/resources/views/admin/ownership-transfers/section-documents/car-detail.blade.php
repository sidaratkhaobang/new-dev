<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.car_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.label id="car_class" :value="$d->car?->carClass?->full_name ?? null" :label="__('registers.car_class')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="engine_no" :value="$d->car?->engine_no ?? null" :label="__('registers.engine_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="chassis_no" :value="$d->car?->chassis_no ?? null" :label="__('registers.chassis_no')" />
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="license_plate" :value="$d->car?->license_plate ?? '-'" :label="__('registers.license_plate')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_age_start" :value="$d->car_age_start ?? null" :label="__('ownership_transfers.car_age_start')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_registered_date" :value="$d->registered_date ?? null" :label="__('ownership_transfers.car_registered_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_status" :value="$d->car?->status ? __('cars.status_' . $d->car->status) : '-'" :label="__('registers.car_status')" />
            </div>
         
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                {{-- <x-forms.select-option id="car_characteristic" :value="$d->car->carCharacteristic->id ?? null" :list="$car_characteristic_list" :label="__('registers.car_characteristic')" :optionals="['required' => true]" /> --}}
            </div>
            <div class="col-sm-3">
                {{-- <x-forms.select-option id="car_category" :value="$d->car->carCategory->id ?? null" :list="$car_category_list" :label="__('registers.car_category')" :optionals="['required' => true]" /> --}}
            </div>
            <div class="col-sm-3">
                {{-- <x-forms.select-option id="register_sign" :value="$d->registered_sign" :list="$register_sign_list" :label="__('registers.license_plate_registered')" :optionals="['required' => true]" /> --}}
            </div>
        </div>
        {{-- <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.text-area-new-line id="description" :value="$d->description" :label="__('registers.optional_detail')" :optionals="['placeholder' => __('lang.input.placeholder'),'row' => 1]"/>
                </div>
           
        </div> --}}
    </div>
</div>
