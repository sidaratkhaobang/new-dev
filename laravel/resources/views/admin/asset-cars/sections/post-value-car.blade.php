<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.posting_key') }}</p>
        <p class="grey-text" id="posting_key">70</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.gl') }}</p>
        <p class="grey-text" id="gl">{{ $d->license_plate }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.local_amount') }}</p>
        <p class="grey-text" id="local_amount">{{ number_format($d->car_price, 2) }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.all_location') }}</p>
        <p class="grey-text" id="all_location">{{ $d->lot_no }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.transaction_type') }}</p>
        <p class="grey-text" id="transaction_type">100</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.asset_value_date') }}</p>
        <p class="grey-text" id="asset_value_date">{{ $d->asset_value_date }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.quantity') }}</p>
        <p class="grey-text" id="quantity">1</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p class="size-text">{{ __('asset_cars.line_item_text') }}</p>
        <p class="grey-text" id="line_item_text">{{ $d->line_item_text }}</p>
    </div>
</div>
