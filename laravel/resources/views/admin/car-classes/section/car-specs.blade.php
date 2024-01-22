<h4>{{ __('car_classes.car_specs') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('car_classes.code_class')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->carType ? $d->carType->car_brand_id: null" id="car_brand_id" :list="null" :label="__('car_classes.car_brand')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $car_brand_name,
            ]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->car_type_id" id="car_type_id" :list="null" :label="__('car_classes.car_type')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $car_type_name,
            ]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->carType ? $d->carType->car_category_id: null" id="car_category_id" :list="null" :label="__('car_classes.car_category')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $car_category_name,
            ]" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-8">
        <x-forms.input-new-line id="full_name" :value="$d->full_name" :label="__('car_classes.class')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="manufacturing_year" :value="$d->manufacturing_year" :label="__('car_classes.manufacturing_year')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-12">
        <x-forms.input-new-line id="description" :value="$d->description" :label="__('car_classes.description')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="engine_size" :value="$d->engine_size" :label="__('car_classes.engine_size')" :optionals="['placeholder' => 'cc']" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="gear_id" :value="$d->gear_id" :list="$gear" :label="__('car_classes.gear')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="drive_system_id" :value="$d->drive_system_id" :list="$drive_system" :label="__('car_classes.drive_system')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="central_lock_id" :value="$d->central_lock_id" :list="$central_lock" :label="__('car_classes.cental_lock')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.select-option id="car_seat_id" :value="$d->car_seat_id" :list="$car_seat" :label="__('car_classes.car_seat')" />
    </div>
    <div class="col-sm-6">
        <x-forms.select-option id="air_bag_id" :value="$d->air_bag_id" :list="$air_bag" :label="__('car_classes.air_bag')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.select-option id="side_mirror_id" :value="$d->side_mirror_id" :list="$side_mirror" :label="__('car_classes.side_mirror')" />
    </div>
    <div class="col-sm-6">
        <x-forms.select-option id="anti_thift_system_id" :value="$d->anti_thift_system_id" :list="$anti_thift_system" :label="__('car_classes.anti_thift_system')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="abs_id" :value="$d->abs_id" :list="$abs" :label="__('car_classes.abs')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="front_brake_id" :value="$d->front_brake_id" :list="$front_brake" :label="__('car_classes.front_brake')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="rear_brake_id" :value="$d->rear_brake_id" :list="$rear_brake" :label="__('car_classes.rear_brake')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="car_tire_id" :value="$d->car_tire_id" :list="$car_tire" :label="__('car_classes.tire')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="car_battery_id" :value="$d->car_battery_id" :list="$car_battery" :label="__('car_classes.battery')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="oil_type" :value="$d->oil_type" :label="__('car_classes.oil_type')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="oil_tank_capacity" :value="$d->oil_tank" :label="__('car_classes.oil_tank')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="car_wiper_id" :value="$d->car_wiper_id" :list="$car_wiper" :label="__('car_classes.wiper')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-12">
        <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('car_classes.remark')" />
    </div>
</div>
