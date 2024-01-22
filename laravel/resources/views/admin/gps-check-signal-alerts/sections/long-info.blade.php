<div id="long_rental" @if (strcmp($d->job_type, 'App\\Models\\LongTermRental') == 0) style="display: block" @else style="display: none" @endif>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.worksheet_no_rental') }}</p>
            <p class="size-text" id="long_worksheet_no">{{ $d->worksheet_no }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.rental_duration') }}</p>
            <p class="size-text" id="rental_duration">{{ $d->rental_duration }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.type') }}</p>
            <p class="size-text" id="long_type">{{ $d->long_type }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.delivery_date') }}</p>
            <p class="size-text" id="delivery_date">{{ $d->delivery_date }}</p>
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.delivery_place') }}</p>
            <p class="size-text" id="delivery_place">{{ $d->delivery_place }}</p>
        </div>
    </div>
</div>
