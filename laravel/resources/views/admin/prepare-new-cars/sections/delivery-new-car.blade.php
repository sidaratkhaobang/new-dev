<div class="row push mb-4">
    <div class="col-sm-4">
        <label class="text-start col-form-label">{{ __('import_cars.delivery_date') }}</label>
        <div class="input-group">
            <input class="form-control delivery_date_modal js-flatpickr form-control flatpickr-input" id="delivery_date2"
                name="delivery_date" placeholder="" data-date-format="d-m-Y">
            <span class="input-group-text">
                <i class="far fa-calendar-check"></i>
            </span>
        </div>
    </div>
    <div class="col-sm-8 ">
        <label class="text-start col-form-label">{{ __('import_cars.delivery_place') }}</label>
        {{-- <x-forms.input-new-line id="delivery_place" :value="null" :label="__('import_cars.delivery_place')" :optionals="['placeholder' => 'ระบุข้อมูล']"/> --}}
        <div class="input-group">
            <input class="form-control delivery_place_modal js-flatpickr form-control flatpickr-input"
                id="delivery_place" name="delivery_place2" placeholder="">
        </div>
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.input-new-line id="car_entry" :value="null" :label="__('import_cars.car_entry')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="car_inspection" :value="null" :label="__('import_cars.car_inspection')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-4">
        <label class="text-start col-form-label">{{ __('import_cars.remark') }}</label>
        <input type="text" id="remark_line" class="form-control" />
    </div>
</div>
