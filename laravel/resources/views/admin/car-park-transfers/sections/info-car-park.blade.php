@php
    $car_park = get_car_park_detail($d->car_id);
@endphp
<p>หากไม่จองช่องจอดล่วงหน้า ระบบจะทำการจองช่องจอดให้อัตโนมัติ เมื่อนำรถผ่านเข้าคลัง</p>
<p>หากต้องการจองช่องจอดล่วงหน้า กรุณาระบุวันที่ต้องการจอง</p>
<div class="row">
    <div class="col-sm-3">
        <x-forms.date-input id="est_transfer_date" name="est_transfer_date" :value="$d->est_transfer_date" :label="__('car_park_transfers.est_transfer_date')"
            :optionals="['placeholder' => __('lang.select_date')]" />
    </div>
    <div class="col-sm-2">&nbsp;</div>
    <div class="col-sm-2">
        <x-forms.label id="car_park_zone_code" :value="$car_park?->zone_code" :label="__('car_park_transfers.zone')" />
    </div>
    <div class="col-sm-2">
        <x-forms.label id="car_park_number" :value="$car_park?->car_park_number" :label="__('car_park_transfers.parking_slot')" />
    </div>
    <div class="col-sm-3">
        <x-forms.label id="car_park_branch" :value="$car_park?->branch_name" :label="__('car_park_transfers.branch')" />
    </div>
    {{-- <div class="col-sm-3" style="margin-top: 2.5rem;">
        <a class="btn btn-primary" onclick="generateZone()">{{ __('car_park_transfers.generate_parking') }}</a>
    </div> --}}
</div>
