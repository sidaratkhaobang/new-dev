<h4>{{ __('gps.rental_data') }}</h4>
<div class="row push mb-4">
    <div class="col-sm-3">
        @if ($replacement_car_class == $d->job_type)
            <p class="grey-text">{{ __('gps.replacement_no') }}</p>
            <p class="size-text" id="replacement_no">{{ $d->worksheet_no }}</p>
        @else
            <p class="grey-text">{{ __('gps.worksheet_no_rental') }}</p>
            <p class="size-text" id="short_worksheet_no">{{ $d->worksheet_no }}</p>
        @endif
    </div>
    <div class="col-sm-3">
        @if ($short_class == $d->job_type)
            <p class="grey-text">{{ __('gps.service_type') }}</p>
            <p class="size-text" id="service_type">{{ $d->service_type }}</p>
        @elseif($long_class == $d->job_type)
            <p class="grey-text">{{ __('gps.rental_duration') }}</p>
            <p class="size-text" id="rental_duration">{{ $d->rental_duration }}</p>
        @elseif($replacement_car_class == $d->job_type)
            <p class="grey-text">{{ __('gps.replacement_type') }}</p>
            <p class="size-text" id="replacement_type">{{ $d->replacement_type }}</p>
        @endif
    </div>
    <div class="col-sm-3">
        @if ($short_class == $d->job_type)
            <p class="grey-text">{{ __('gps.customer') }}</p>
            <p class="size-text" id="short_customer">{{ $d->customer }}</p>
        @elseif($long_class == $d->job_type)
            <p class="grey-text">{{ __('gps.type') }}</p>
            <p class="size-text" id="long_type">{{ $d->long_type }}</p>
        @elseif($replacement_car_class == $d->job_type)
            <p class="grey-text">{{ __('gps.customer') }}</p>
            <p class="size-text" id="replacement_customer">{{ $d->replacement_customer }}</p>
        @endif
    </div>
    <div class="col-sm-3">
        @if ($short_class == $d->job_type)
            <p class="grey-text">{{ __('gps.pickup_date') }}</p>
            <p class="size-text" id="pickup_date">{{ $d->pickup_date }}</p>
        @elseif($long_class == $d->job_type)
            <p class="grey-text">{{ __('gps.delivery_date') }}</p>
            <p class="size-text" id="delivery_date">{{ $d->delivery_date }}</p>
        @elseif($replacement_car_class == $d->job_type)
            <p class="grey-text">{{ __('gps.replacement_date') }}</p>
            <p class="size-text" id="replacement_date">{{ $d->replacement_date }}</p>
        @endif
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        @if ($short_class == $d->job_type)
            <p class="grey-text">{{ __('gps.return_date') }}</p>
            <p class="size-text" id="return_date">{{ $d->return_date }}</p>
        @elseif($long_class == $d->job_type)
            <p class="grey-text">{{ __('gps.delivery_place') }}</p>
            <p class="size-text" id="delivery_place">{{ $d->delivery_place }}</p>
        @elseif($replacement_car_class == $d->job_type)
            <p class="grey-text">{{ __('gps.replacement_place') }}</p>
            <p class="size-text" id="replacement_place">{{ $d->replacement_place }}</p>
        @endif

    </div>
</div>
<hr>
