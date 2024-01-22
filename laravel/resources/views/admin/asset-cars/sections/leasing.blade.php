<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.contract_no') }}</p>
        <p class="grey-text" id="contract_no">{{ $d->contract_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.leasing_name') }}</p>
        <p class="grey-text" id="leasing_name">{{ $d->leasing_name }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.contract_start_date') }}</p>
        <p class="grey-text" id="contract_start_date">{{ $d->contract_start_date }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.contract_end_date') }}</p>
        <p class="grey-text" id="contract_end_date">{{ $d->contract_end_date }}</p>
    </div>
</div>
