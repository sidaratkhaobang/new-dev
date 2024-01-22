<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('cmi_cars.rental_info'),
    ])
    <div class="block-content">
        <div class="row mb-3">
            <div class="col-sm-3">{{ __('cmi_cars.renter') }}</div>
            <div class="col-sm-3">{{ __('cmi_cars.rental_duration') }}</div>
            <div class="col-sm-3">{{ __('cmi_cars.customer_group') }}</div>
            <div class="col-sm-3">{{ __('cmi_cars.customer_address') }}</div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3 fw-bolder">{{ $rental['customer_name'] ?? '-' }}</div>
            <div class="col-sm-3 fw-bolder">{{ $rental['rental_duration'] ?? '-' }}</div>
            <div class="col-sm-3 fw-bolder">{{ $rental['customer_group'] ?? '-' }}</div>
            <div class="col-sm-3 fw-bolder">
                <x-forms.tooltip :title="$rental['customer_address'] ?? '-' " :limit="100"></x-forms.tooltip>
            </div>
        </div>
    </div>
</div>
