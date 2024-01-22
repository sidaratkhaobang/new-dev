<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_code" :value="$d->code" :label="__('cars.code')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="license_plate" :value="$d->license_plate" :label="__('cars.license_plate_current')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')" :optionals="['required' => true]" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->car_brand_id" id="car_brand_id" :list="null" :label="__('car_classes.car_brand')"
            :optionals="['ajax' => true, 'default_option_label' => $car_brand_name, 'required' => true]" />
    </div>
    <div class="col-sm-6">
        <x-forms.select-option :value="$d->car_class_id" id="car_class_id" :list="null" :label="__('car_classes.class')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $car_class_name,
                'required' => true,
            ]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="car_color_id" :value="$d->car_color_id" :list="null" :label="__('purchase_requisitions.car_color')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $car_color_name,
                'required' => true,
            ]" />
    </div>
    {{-- <div class="col-sm-3">
        <x-forms.input-new-line id="last_mile" :value="$d->last_mile" :label="__('cars.last_mile')"
            :optionals="['required' => true]"
            />
    </div> --}}
    {{-- <div class="col-sm-3">
        <x-forms.select-option id="rental_type" :value="$d->rental_type" :list="$rental_type_list" :label="__('purchase_requisitions.rental_type')"
            :optionals="['required' => true]" />
    </div> --}}
</div>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line :value="null" id="car_group_name" :label="__('cars.car_group')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line :value="null" id="car_categorie_name" :label="__('cars.car_category')" />
    </div>
    {{--        <div class="col-sm-3"> --}}
    {{--            <x-forms.select-option id="car_group_id" :value="$d->car_group_id" :list="null" :label="__('cars.car_group')" --}}
    {{--                :optionals="[ --}}
    {{--                    'ajax' => true, --}}
    {{--                    'default_option_label' => $car_group_name, --}}
    {{--                    'required' => true --}}
    {{--                ]" /> --}}
    {{--        </div> --}}
    {{--        <div class="col-sm-3"> --}}
    {{--            <x-forms.select-option id="car_categorie_id" :value="$d->car_categorie_id" :list="null" :label="__('cars.car_category')" --}}
    {{--                :optionals="[ --}}
    {{--                    'ajax' => true, --}}
    {{--                    'default_option_label' => $car_category_name, --}}
    {{--                    'required' => true --}}
    {{--                ]" /> --}}
    {{--        </div> --}}
    <div class="col-sm-3">
        <x-forms.date-input id="registered_date" name="registered_date" :value="$d->registered_date" :label="__('cars.register_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_age" name="car_age" :value="$car_age ? $car_age : null" :label="__('cars.car_age')" />
    </div>

</div>
<div class="row push">
    <div class="col-sm-3">
        <x-forms.date-input id="start_date" name="start_date" :value="$d->start_date" :label="__('cars.start_system_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_age_start" name="car_age_start" :value="$car_age_start ? $car_age_start : null" :label="__('cars.car_storage_age')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="rental_type" :value="$d->rental_type" :list="$rental_type_list" :label="__('purchase_requisitions.rental_type')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->branch_id" id="branch_id" :list="$branch_lists" :label="__('lang.branch_name')"
            :optionals="['required' => true]" />
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->leasing_id" id="leasing_id" :list="$leasings" :label="__('cars.ownership')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->status" id="status" :list="$status_list" :label="__('lang.status')"
            :optionals="['required' => true]" />
    </div>
</div>