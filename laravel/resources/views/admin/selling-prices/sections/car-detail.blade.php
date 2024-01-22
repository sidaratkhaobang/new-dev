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
        <div class="col-sm-4">
            <x-forms.input-new-line id="price" :value="$d->price" :label="__('selling_prices.price')" :optionals="[
                'input_class' => 'number-format col-sm-4',
                'required' => true,
            ]" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="vat_value" :value="number_format($d->vat, 2)" :label="__('selling_prices.vat')" />
            <x-forms.hidden id="vat" :value="$d->vat" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="total_value" :value="number_format($d->total, 2)" :label="__('selling_prices.total')" />
            <x-forms.hidden id="total" :value="$d->total" />
        </div>
    </div>
</div>
