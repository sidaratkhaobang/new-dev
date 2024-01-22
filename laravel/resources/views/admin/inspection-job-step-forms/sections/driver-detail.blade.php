<h4>{{ __('inspection_cars.sheet_detail') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="driver_department" :value="null"  :label="__('inspection_cars.department')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="driver_name" :value="null" :list="$car_status" :label="__('inspection_cars.fullname_driver')" />
    </div>
</div>