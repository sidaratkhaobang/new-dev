<div id="replacement_car" @if (strcmp($d->job_type, 'App\\Models\\ReplacementCar') == 0) style="display: block" @else style="display: none" @endif>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.replacement_no') }}</p>
            <p class="size-text" id="replacement_no">{{ $d->worksheet_no }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.replacement_type') }}</p>
            <p class="size-text" id="replacement_type">{{ $d->replacement_type }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.customer') }}</p>
            <p class="size-text" id="replacement_customer">{{ $d->replacement_customer }}</p>
        </div>
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.replacement_date') }}</p>
            <p class="size-text" id="replacement_date">{{ $d->replacement_date }}</p>
        </div>
    </div>
    <div class="row push mb-4">
        <div class="col-sm-3">
            <p class="grey-text">{{ __('gps.replacement_place') }}</p>
            <p class="size-text" id="replacement_place">{{ $d->replacement_place }}</p>
        </div>
    </div>
</div>
