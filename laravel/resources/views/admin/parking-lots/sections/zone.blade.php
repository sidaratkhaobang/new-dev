<div class="row">
    <div class="col-sm-3">
        <x-forms.input-new-line id="code" :value="$d->code" :label="__('parking_lots.zone_code')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('parking_lots.zone_name')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="zone_size" :value="$d->zone_size" :list="$car_zone_size_list" :label="__('parking_lots.zone_size')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="total_slot" :value="$d->total_slot" :label="__('parking_lots.total_slot')" :optionals="['required' => true]" />
    </div>
</div>