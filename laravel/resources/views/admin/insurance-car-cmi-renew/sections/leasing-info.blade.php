<div class="content">
    <div class="row">
        <div class="col-sm-3">{{ __('cmi_cars.leasing') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.dealer') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.car_color') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.cc') }}</div>
    </div>
    <div class="row my-2">
        <div class="col-sm-3 fw-bolder">{{ $car?->leasing_name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $po?->creditor?->name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->carColor?->name ?? '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->engine_size ?? '-' }}</div>
    </div>
    <div class="row mt-5">
        <div class="col-sm-3">{{ __('cmi_cars.car_year') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.car_price') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.accessory_price') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.registration_type') }}</div>
    </div>
    <div class="row my-2">
        <div class="col-sm-3 fw-bolder">-</div>
        <div class="col-sm-3 fw-bolder">{{ ($po?->total) ? number_format($po->total, 2) . ' ' . __('lang.baht') : '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ ($po?->total) ? number_format($po->total, 2) . ' ' . __('lang.baht') : '-' }}</div>
        <div class="col-sm-3 fw-bolder">{{ $car?->registration_type }}</div>
    </div>

    <div class="row mt-5">
        <div class="col-sm-3">{{ __('cmi_cars.pickup_date') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.delivery_date') }}</div>
        <div class="col-sm-3">{{ __('cmi_cars.payment_dealer_date') }}</div>
    </div>
    <div class="row my-2 mb-4">
        <div class="col-sm-3 fw-bolder">{{ ($car?->receive_date) ? get_thai_date_format( $car->receive_date, 'd/m/y') : '-' }}</div>
        <div class="col-sm-3 fw-bolder">-</div>
        <div class="col-sm-3 fw-bolder">-</div>
    </div>
    <hr>
    <div class="row my-4">
        <div class="col-sm-4">
            <x-forms.select-option :value="$d->car_class_insurance_id" id="car_class_insurance_id" :list="$car_class_insurance_list" :label="__('cmi_cars.insurance_class')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-4">
            <x-forms.select-option :value="$d->type_vmi" id="type_vmi" :list="$type_vmi_list" :label="__('cmi_cars.typev_mi')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-4">
            <x-forms.select-option :value="$d->type_cmi" id="type_cmi" :list="$type_cmi_list" :label="__('cmi_cars.typec_mi')"
                :optionals="['required' => true]" />
        </div>
    </div>
    <div class="row m">
        <div class="col-sm-4">
            <x-forms.input-new-line id="sum_insured_car" :value="$d->sum_insured_car" :label="__('cmi_cars.sum_insured_car')"
            :optionals="['input_class' => 'number-format', 'required' => true]" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="sum_insured_accessory" :value="$d->sum_insured_accessory" :label="__('cmi_cars.sum_insured_accessory')"
            :optionals="['input_class' => 'number-format', 'required' => true]" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="sum_insured_total" :value="$d->sum_insured_total" :label="__('cmi_cars.sum_insured_total')" />
        </div>
    </div>
</div>
