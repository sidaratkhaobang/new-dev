<div id="short_rental" @if (strcmp($d->job_type, 'App\\Models\\Rental') == 0) style="display: block" @else style="display: none" @endif>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.worksheet_no_rental') }}</p>
            <p class="size-text" id="short_worksheet_no">{{ $d->worksheet_no }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.service_type') }}</p>
            <p class="size-text" id="service_type">{{ $d->service_type }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.customer') }}</p>
            <p class="size-text" id="short_customer">{{ $d->customer }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.pickup_date') }}</p>
            <p class="size-text" id="pickup_date">{{ $d->pickup_date }}</p>
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.return_date') }}</p>
            <p class="size-text" id="return_date">{{ $d->return_date }}</p>
        </div>
    </div>
</div>
