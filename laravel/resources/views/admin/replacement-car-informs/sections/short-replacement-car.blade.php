<div class="short-replacement-car-section hide">
    <h4>{{ __('replacement_cars.replacement_info') }}</h4>  
    <br>
    <div class="row push mb-4">
        <div class="col-sm-6">
            <x-forms.input-new-line id="exist_replace_car_license" :value="null" :label="__('replacement_cars.replace_license_plate')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="exist_replace_car_car_class" :value="null" :label="__('replacement_cars.class')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="exist_replace_car_car_color" :value="null" :label="__('replacement_cars.color')" />
        </div>
    </div>
</div>
