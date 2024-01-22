<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <h4>{{ __('borrow_car_lists.borrow_car_detail') }}</h4>
        <hr>
        <div class="row push mb-4">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="car_code" :value="$d->code" :label="__('cars.code')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('borrow_car_lists.license_plate')"  />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')"/>
                </div>
            </div>
        </div>

        <div class="row push mb-4">
           
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_brand" :value="$car_brand_name" :label="__('borrow_car_lists.car_brand')"  />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="car_class" :value="$car_class_name" :label="__('borrow_car_lists.car_class')"  />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_color" :value="$car_color_name" :label="__('purchase_requisitions.car_color')"  />
            </div>

        </div>

        <div class="row push mb-4">
           
            <div class="col-sm-3">
                <x-forms.input-new-line id="registered_date" :value="$d->registered_date" :label="__('cars.register_date')"  />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_age" :value="$car_age ? $car_age : null" :label="__('borrow_car_lists.car_age')"  />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="start_date" name="start_date" :value="$d->start_date" :label="__('cars.start_system_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_age_start" name="car_age_start" :value="$car_age_start ? $car_age_start : null" :label="__('cars.car_storage_age')" />
            </div>

        </div>


    </div>
</div>
