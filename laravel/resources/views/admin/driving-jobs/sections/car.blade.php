<div class="row push">
    <div class="col-sm-6">
        <x-forms.label id="car_id" :value="$car?->display_name" :label="__('cars.license_plate_chassis_engine')"/>
    </div>
    <div class="col-sm-6">
        <x-forms.label id="car_class_name" :value="$car?->car_class_name" :label="__('cars.class')"/>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <x-forms.label id="rental_type_name" :value="$car?->rental_type_name" :label="__('cars.rental_type')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.label id="car_color_name" :value="$car?->car_color_name" :label="__('cars.color')"/>
    </div>
</div>
