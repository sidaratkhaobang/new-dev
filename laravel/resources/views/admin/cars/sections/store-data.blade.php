<div class="row">
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->car_storage" id="car_storage" :list="$storage_list" :label="__('cars.store_car')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option :value="$d->car_park" id="car_park" :list="$storage_location_list" :label="__('cars.store_place')" />    
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="zone" :value="$zone ? $zone->code : null " :label="__('cars.zone')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="slot_no" :value="$zone ? $zone->car_park_number : null" :label="__('cars.slot_no')" />
    </div>
</div>

