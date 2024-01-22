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
    </div>
    <div class="row my-2">
        <div class="col-sm-3 fw-bolder">
            {{ $car?->registered_date ? get_thai_date_format($car->registered_date, 'd/m/y') : '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->carColor?->name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->ownership ?? '-' }}</div>
    </div>
    <hr>
    <div class="row">
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_FINANCE, SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-sm-3">{{ __('selling_prices.noti_finance_date') }}</div>
            <div class="col-sm-3">{{ __('selling_prices.expected_off_finance') }}</div>
        @endif
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-sm-3">{{ __('selling_prices.expected_transfer_ownership') }}</div>
            <div class="col-sm-3">{{ __('selling_prices.transfer_ownership') }}</div>
        @endif
    </div>
    <div class="row my-2">
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_FINANCE, SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-sm-3 fw-bolder">
                {{ $d?->request_finance_date ? get_thai_date_format($d->request_finance_date, 'd/m/y') : '-' }}</div>
            <div class="col-sm-3 fw-bolder">
                {{ $d?->expected_finance_date ? get_thai_date_format($d->expected_finance_date, 'd/m/y') : '-' }}</div>
        @endif
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-sm-3 fw-bolder">
                {{ $d?->expected_transfer_ownership_date ? get_thai_date_format($d->expected_transfer_ownership_date, 'd/m/y') : '-' }}
            </div>
            <div class="col-sm-3 fw-bolder">
                {{ $d?->transfer_ownership_date ? get_thai_date_format($d->transfer_ownership_date, 'd/m/y') : '-' }}
            </div>
        @endif
    </div>
    <div class="row">
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_FINANCE, SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-3">
                <x-forms.select-option id="transfer_status" :value="$d->status" :list="null" :label="__('lang.status')"
                    :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                        'default_option_label' => $transfer_status_name,
                    ]" />
            </div>
        @endif
        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_TRANSFER]))
            <div class="col-3">
                <x-forms.select-option id="ownership" :value="null" :list="null" :label="__('selling_prices.ownership')"
                    :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                    ]" />
            </div>
        @endif
    </div>
</div>
