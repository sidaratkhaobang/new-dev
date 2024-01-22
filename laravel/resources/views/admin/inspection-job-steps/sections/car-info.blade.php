<div class="row push">
    <div class="col-sm-3">
        <x-forms.select-option id="car_status_id" :value="$d->rental_type" :list="$rental_type_list" :label="__('inspection_cars.car_type')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_id" :value="$car_name" :label="__('inspection_cars.license_plate')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="engine_no" :value="$d->engine_no ? $d->engine_no : null" :label="__('inspection_cars.engine_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no ? $d->chassis_no : null" :label="__('inspection_cars.chassis_no')" />
    </div>
</div>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_category" :value="$d->car_categories_name ? $d->car_categories_name : null" :label="__('inspection_cars.car_category')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="car_class" :value="$d->car_class_name ? $d->car_class_name : null" :label="__('inspection_cars.class')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_color" :value="$d->car_colors_name ? $d->car_colors_name : null" :label="__('inspection_cars.color')" />
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <x-forms.input-new-line id="engine_size" :value="$d->engine_size ? $d->engine_size : null" :label="__('inspection_cars.engine_size')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="gear" :value="$d->car_gear_name ? $d->car_gear_name : null" :label="__('inspection_cars.gear')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="fuel_type" :value="$d->oil_type ? $d->oil_type : null" :label="__('inspection_cars.fuel_type')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tire" :value="$d->car_tire_name ? $d->car_tire_name : null" :label="__('inspection_cars.tire')" />
    </div>
</div>