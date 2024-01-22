<h4>{{ __('parking_lots.slot_detail') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-6">
        <x-forms.select-option id="zone_id" :value="null" :list="null" :label="__('parking_lots.zone_text')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="slot_number" :value="null" :list="null" :label="__('parking_lots.slot_number')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_group_list" :value="null" :label="__('parking_lots.group_car')" />
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="total_car_park" :value="null" :label="__('parking_lots.total_slot')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="total_empty_car_park" :value="null" :label="__('parking_lots.total_available_slot')" />
    </div>
</div>
