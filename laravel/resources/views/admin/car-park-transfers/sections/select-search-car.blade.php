<div class="col-sm-3">
    <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('car_park_transfers.license_plate')" :optionals="[
        'ajax' => true,
        'default_option_label' => $license_plate,
    ]" />
</div>
<div class="col-sm-3">
    <x-forms.select-option id="engine_no" :value="$engine_no_id" :list="null" :label="__('car_park_transfers.engine_no')" :optionals="[
        'ajax' => true,
        'default_option_label' => $engine_no,
    ]" />
</div>
<div class="col-sm-3">
    <x-forms.select-option id="chassis_no" :value="$chassis_no_id" :list="null" :label="__('car_park_transfers.chassis_no')" :optionals="[
        'ajax' => true,
        'default_option_label' => $chassis_no,
    ]" />
</div>
