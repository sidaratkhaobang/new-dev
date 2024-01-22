<div class="content">
    <div class="row">
        <div class="col-sm-3">{{ __('selling_prices.mileage') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.car_color') }}</div>
        <div class="col-sm-3">{{ __('car_classes.manufacturing_year') }}</div>
        <div class="col-sm-3">{{ __('cars.car_age') }}</div>
    </div>
    <div class="row my-2">
        <div class="col-sm-3 fw-bolder">{{ $car?->current_mileage ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->carColor?->name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->carClass?->manufacturing_year ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->car_age ?? '-' }}</div>
    </div>
    <div class="row mt-5">
        <div class="col-sm-3">{{ __('cars.registration_date') }}</div>
        <div class="col-sm-3">{{ __('selling_prices.car_color') }}</div>
        <div class="col-sm-3">{{ __('selling_prices.ownership') }}</div>
        @if ($d->close_cmi_vmi_date)
            <div class="col-sm-3">{{ __('car_auctions.close_cmi_vmi_date') }}</div>
        @endif
    </div>
    <div class="row my-2">
        <div class="col-sm-3 fw-bolder">
            {{ $car?->registered_date ? get_thai_date_format($car->registered_date, 'd/m/y') : '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->carColor?->name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->ownership ?? '-' }}</div>
        @if ($d->close_cmi_vmi_date)
            <div class="col-sm-3 fw-bolder">
                {{ $d?->close_cmi_vmi_date ? get_thai_date_format($d->close_cmi_vmi_date, 'd/m/y') : '-' }}</div>
        @endif
    </div>
</div>
