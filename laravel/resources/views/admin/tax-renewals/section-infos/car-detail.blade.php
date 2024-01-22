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
                <x-forms.label id="car_class" :value="$d->car && $d->car->carClass ? $d->car->carClass->full_name : null" :label="__('registers.car_class')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="engine_no" :value="$d->car && $d->car->engine_no ?? null" :label="__('registers.engine_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="chassis_no" :value="$d->car && $d->car->chassis_no ?? null" :label="__('registers.chassis_no')" />
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="license_plate" :value="$d->car && $d->car->license_plate ?? '-'" :label="__('tax_renewals.license_plate')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="rental_type" :value="$d->car && $d->car->rental_type ? __('cars.rental_type_' . $d->car->rental_type) : '-'" :label="__('tax_renewals.rental_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="engine_size" :value="$d->car && $d->car->engine_size ? $d->car->engine_size : '-'" :label="__('tax_renewals.cc')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="fuel_type" :value="$d->car && $d->car->fuel_type ? $d->car->fuel_type : '-'" :label="__('tax_renewals.fuel_type')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="license_plate_registered" :value="$register && $register->registered_sign ? __('tax_renewals.registered_sign_type_' . $register->registered_sign) : '-'" :label="__('tax_renewals.license_plate_registered')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_age_start" :value="$d->car_age_start ?? null" :label="__('ownership_transfers.car_age_start')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="registered_date" :value="$d->registered_date ? $d->registered_date : null" :label="__('ownership_transfers.car_registered_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_status" :value="$d->car && $d->car->status ? __('cars.status_' . $d->car->status) : '-'" :label="__('registers.car_status')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="ownership" :value="$register && $register->car && $register->car->creditor ? $register->car->creditor->name : '-'" :label="__('tax_renewals.ownership')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="copy_pattern" :value="null" :label="__('tax_renewals.copy_pattern')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="tax_exp_date" :value="$register && $register->car ? get_date_by_format($register->car->car_tax_exp_date , 'd/m/Y') : '-'" :label="__('tax_renewals.tax_exp_date')" />
            </div>
        </div>
    </div>
</div>
