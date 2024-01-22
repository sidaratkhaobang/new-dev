{{-- <div class="row push mb-4">
    <div class="col-sm-8">
        <x-forms.input-new-line id="name" :value="null" :label="__('import_cars.name')" :optionals="['placeholder' => 'ระบุข้อมูล']"/>
    </div>
    <div class="col-sm-4">
      <x-forms.input-new-line id="name2" :value="null" :label="__('import_cars.name')" :optionals="['placeholder' => 'ระบุข้อมูล']"/>
  </div>
</div> --}}
<div class="row push mb-4">
    <div class="col-sm-4">
        <label class="text-start col-form-label">{{ __('import_cars.engine_no') }}</label>
        <input type="text" id="engine_no2" name="engine_no2" class="form-control"/>
    </div>
    <div class="col-sm-4">
        <label class="text-start col-form-label">{{ __('import_cars.chassis_no') }}</label>
        <input type="text" id="chassis_no2" class="form-control"/>
    </div>
    <div class="col-sm-4">
        <label class="text-start col-form-label">{{ __('import_cars.installation_completed_date') }}</label>
        <div class="input-group">
            <input type="date" class="form-control js-flatpickr form-control flatpickr-input" id="setup_date2"
                   name="setup_date" placeholder="" data-date-format="d-m-Y">
            <span class="input-group-text">
                <i class="far fa-calendar-check"></i>
            </span>
        </div>
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-4">
        {{-- <label class="text-start col-form-label">{{ __('import_cars.engine_no') }}</label> --}}
        {{--        <x-forms.input-new-line id="_registration_type" :value="null" :label="__('import_cars.registration_type')"/>--}}
        <x-forms.select-option id="_registration_type" :value="null" :list="$car_category_list ?? []"
                               :optionals="[
                                           'placeholder' => __('lang.search_placeholder'),
                               ]"
                               :label="__('import_cars.registration_type')"/>
    </div>
    <div class="col-sm-4">
        <x-forms.select-option id="car_characteristic" :value="null" :list="$car_characteristic_list ?? []"
                               :optionals="[
                                           'placeholder' => __('lang.search_placeholder'),
                               ]"
                               :label="__('import_cars.car_characteristic')"/>
    </div>

</div>
