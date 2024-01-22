<div class="row">
    <div class="col-sm-6">
        <p class="size-text">{{ __('cars.brand') }}</p>
        <p class="grey-text" id="car_class_name">{{ $d->car_class_name }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.engine_no') }}</p>
        <p class="grey-text" id="engine_no">{{ $d->engine_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.chassis_no') }}</p>
        <p class="grey-text" id="chassis_no">{{ $d->chassis_no }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.license_plate') }}</p>
        <p class="grey-text" id="license_plate">{{ $d->license_plate }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.registration_date') }}</p>
        <p class="grey-text" id="registration_date">{{ $d->registration_date }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.car_age') }}</p>
        <p class="grey-text" id="car_age">{{ $d->car_age }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.cc') }}</p>
        <p class="grey-text" id="cc">{{ $d->cc }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('cars.car_group') }}</p>
        <p class="grey-text" id="car_group">{{ $d->car_group }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.po_no') }}</p>
        <p class="grey-text" id="po_no">{{ $d->po_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.car_price') }}</p>
        <p class="grey-text" id="car_price">{{ number_format($d->car_price, 2) }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.accessory_price') }}</p>
        <p class="grey-text" id="accessory_price">{{ number_format($accessory_price, 2) }}</p>
    </div>
</div>
