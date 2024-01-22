<h4>{{ __('parking_lots.zone_original') }}</h4>
<hr>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="code" :value="$zone_detail->code" :label="__('parking_lots.zone_code')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="name" :value="$zone_detail->name" :label="__('parking_lots.zone_name')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="number" :value="$zone_detail->start_number.' - '. $zone_detail->end_number" :label="__('parking_lots.group_car')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_group_list_1" :value="$zone_detail->car_group_list" :label="__('parking_lots.group_car')"/>
    </div>
</div>
<div class="row push mb-5">
    <div class="col-sm-3">
        <x-forms.input-new-line id="remaining_car_in_park_amount" 
        :value="sizeof($car_list)" 
        :label="__('parking_lots.remaining_car_in_park_amount')"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="slot_in_use" :value="$zone_detail->slot_in_use" :label="__('parking_lots.slot_in_use')"/>
    </div>
</div>