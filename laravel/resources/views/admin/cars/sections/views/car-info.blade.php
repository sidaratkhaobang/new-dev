<h4>{{ __('cars.car_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="code" :value="$d->code" :label="__('cars.code')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('cars.license_plate_current')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')" :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')" :optionals="['required' => true]" />
        </div>
    </div>

    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.select-option :value="$d->car_brand_id" id="car_brand_id" :list="null" :label="__('car_classes.car_brand')"
                :optionals="['ajax' => true]" />
        </div>
        <div class="col-sm-6">
            <x-forms.select-option :value="$d->car_class_id" id="car_class_id" :list="null" :label="__('car_classes.class')"
                :optionals="['ajax' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="car_color_id" :value="$d->car_color_id" :list="null" :label="__('purchase_requisitions.car_color')"
                :optionals="['ajax' => true]" />
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.select-option :value="$d->car_group_id" id="car_group_id" :list="null" :label="__('cars.car_group')"
                :optionals="['ajax' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :value="$d->car_category_id" id="car_category_id" :list="null" :label="__('cars.car_category')"
                :optionals="['ajax' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="registration_date" :value="$d->registration_date" :label="__('cars.registration_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_age" :value="$d->car_age" :label="__('cars.car_age')" />
        </div>
    </div>

    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.date-input id="usage_start_date" name="usage_start_date" :value="$d->usage_start_date" :label="__('cars.usage_start_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="usage_age" :value="$d->usage_age" :label="__('cars.usage_age')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="last_mile" :value="$d->last_mile" :label="__('cars.last_mile')" />
        </div>
        <div class="col-sm-3">
            <label class="text-start col-form-label">{{ __('cars.current_status') }}
            </label>
            {!! badge_render('primary', __('cars.new_car')) !!}
        </div>
    </div>
</div>
