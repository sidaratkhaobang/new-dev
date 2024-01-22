<h4 class="mt-4">{{ __('gps.car_table') }}</h4>
<div class="row push mb-4">
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.license_plate') }}</p>
        <p class="size-text" id="license_plate">{{ $d->license_plate }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.engine_no') }}</p>
        <p class="size-text" id="engine_no">{{ $d->engine_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.chassis_no') }}</p>
        <p class="size-text" id="chassis_no">{{ $d->chassis_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.car_class') }}</p>
        <p class="size-text" id="car_class">{{ $d->car_class }}</p>
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.car_color') }}</p>
        <p class="size-text" id="car_color">{{ $d->car_color }}</p>
    </div>
    <div class="col-sm-3">
        <p class="grey-text">{{ __('gps.fleet') }}</p>
        <p class="size-text" id="fleet">{{ $d->fleet }}</p>
    </div>
</div>
<hr>
