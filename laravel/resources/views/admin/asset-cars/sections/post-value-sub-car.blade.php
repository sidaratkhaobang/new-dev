<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.posting_key') }}</p>
        <p class="grey-text" id="posting_key_sub">70</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.gl') }}</p>
        <p class="grey-text" id="gl_sub">{{ $d->license_plate }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.local_amount') }}</p>
        <p class="grey-text" id="local_amount_sub">{{ number_format($accessory_price, 2) }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.all_location') }}</p>
        <p class="grey-text" id="all_location_sub">{{ $d->lot_no }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.transaction_type') }}</p>
        <p class="grey-text" id="transaction_type_sub">100</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.asset_value_date') }}</p>
        <p class="grey-text" id="asset_value_date_sub">{{ $d->asset_value_date }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.quantity') }}</p>
        <p class="grey-text" id="quantity_sub">1</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <p class="size-text">{{ __('asset_cars.line_item_text') }}</p>
        <p class="grey-text" id="line_item_text_sub">ชื่ออุปกรณ์-
            @foreach ($asset_accessory as $item_accessory)
                @php
                    $last_item = $loop->last ? '' : '+ ';
                @endphp
                {{ $item_accessory->accessory_name }} {{ $last_item }}
            @endforeach
            {{ $d->lot_no }}
        </p>
    </div>
</div>
