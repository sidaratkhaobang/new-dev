<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('compensations.car_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="license_plate" :value="$car?->license_plate" :label="__('cars.license_plate')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="chassis_no" :value="$car?->chassis_no" :label="__('cars.chassis_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="engine_no" :value="$car?->engine_no" :label="__('cars.engine_no')" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="car_group" :value="$car?->carGroup?->name" :label="__('compensations.car_group')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="car_type" :value="$car?->car_type" :label="__('compensations.car_type')" />
            </div>
        </div>
    </div>
</div>
