@push('styles')
    <style>
        .car-wrap {
            margin: 20px 0;
        }

        .img-fluid {
            width: 250px;
            height: 100px;
            object-fit: cover;
        }

        .hide {
            display: none !important;
        }
    </style>
@endpush
@push('custom_styles')
    <style>
        .badge-cus {
            min-width: 30rem;
            font-size: 14px;
            line-height: 20px;
        }
    </style>
@endpush

<div class="car-wrap">
    <div class="py-1 car-section text-center">
        @if (isset($car->image) && isset($car->image['url']))
            <img id="car-img" class="img-fluid" src='{{ $car->image['url'] }}' alt="">
        @else
            <img id="car-img" class="img-fluid" src='{{ asset('images/car-sample/car-placeholder.png') }}' alt="">
        @endif
    </div>
    <div class="py-1 row justify-content-center">
        <div class="col-sm-10 col-md-8">
            <p id="p-class-name" class="fs-6 fw-bolder mb-2">
                {{ $car->car_class_name ?? ' -' }}
            </p>
            <p class="font-w800 mb-1">หมายเลขตัวถัง:
                <span id="span-chassis-no">{{ $car->chassis_no ?? '' }}</span>
            </p>
            <p class="font-w800 mb-1">ทะเบียนรถ:
                <span id="span-license-plate">{{ $car->license_plate ?? ' -' }}</span>
            </p>
            <p class="font-w800 mb-1">หมายเลขเครื่องยนต์:
                <span id="span-engine-no">{{ $car->engine_no ?? ' -' }}</span>
            </p>
        </div>
    </div>
    @if (isset($auction))
        <div class="py-1 row justify-content-center">
            <div class="col-12 text-center">
                <div class="mt-2 me-2 badge badge-cus badge-bg-{{ __('car_auctions.class_' . $car->status) }}">
                    <span>{{ __('car_auctions.status_' . $car->status) }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
