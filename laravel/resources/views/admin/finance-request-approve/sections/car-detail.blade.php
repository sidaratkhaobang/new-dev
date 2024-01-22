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

        .badge-custom {
            min-width: 120px !important;
        }
    </style>
@endpush
@if(!empty($car))
    <div class="car-wrap">
        <div class="py-1 car-section text-center">
            @if (isset($car?->image) && isset($car?->image['url']))
                <img id="car-img" class="img-fluid" src='{{ $car?->image['url'] }}'
                     alt="">
            @else
                <img id="car-img" class="img-fluid"
                     src='{{ asset('images/car-sample/car-placeholder.png') }}' alt="">
            @endif
        </div>
        <div class="py-1 row justify-content-center">
            <div class="col-sm-10 col-md-8">
                {{--            <p class="text-center">{!! badge_render(__('cars.class_' . $car->status), __('cars.status_' . $car->status)) !!} </p>--}}
                <p id="p-class-name {{ $class_car_class_name ?? '' }}" class="fs-6 fw-bolder mb-2">
                    {{ $car?->class_name ?? ' -' }}
                </p>
                {{--            <p class="font-w800 mb-1">ประเภทรถ:--}}
                {{--                {{ $car->rental_type ? __('cars.rental_type_' . $car->rental_type) : '-' }}</p>--}}
                <p class="font-w800 mb-1 {{ $class_chassis_no ?? '' }}">หมายเลขตัวถัง:
                    <span id="span-chassis-no">{{ $car?->chassis_no ?? '' }}</span>
                </p>
                <p class="font-w800 mb-1 {{ $class_engine_no ?? '' }}">หมายเลขเครื่องยนต์:
                    <span id="span-engine-no">{{ $car?->engine_no ?? ' -' }}</span>
                </p>
                <p class="font-w800 mb-1 {{ $class_license_plate ?? '' }}">ทะเบียนรถ:
                    <span id="span-license-plate">{{ $car?->license_plate ?? ' -' }}</span>
                </p>
                <p class="font-w800 mb-1 {{ $class_license_plate ?? '' }}">สีรถ:
                    <span id="span-license-plate">{{ $car?->carColor?->name ?? ' -' }}</span>
                </p>
                <p class="font-w800 mb-1 {{ $class_license_plate ?? '' }}">CC:
                    <span id="span-license-plate">{{ $car?->engine_size ?? ' -' }}</span>
                </p>
                {{--            <div class="mt-1 {{ $class_icon ?? '' }}">--}}
                {{--                --}}{{-- <div class="col-6"> --}}
                {{--                <div class="mt-2 me-2 badge badge-custom badge-bg-{{ $car->have_gps ? 'primary' : 'secondary' }}">--}}
                {{--                    <i class="fa fa-{{ $car->have_gps ? 'check' : 'xmark' }} me-1"></i>--}}
                {{--                    <span>GPS</span>--}}
                {{--                    <i class="fa fa-location-crosshairs ms-1"></i>--}}
                {{--                </div>--}}
                {{--                --}}{{-- </div> --}}
                {{--                --}}{{-- <div class="col-6"> --}}
                {{--                <div class="mt-2 badge badge-custom badge-bg-{{ $car->have_cctv ? 'primary' : 'secondary' }}">--}}
                {{--                    <i class="fa fa-{{ $car->have_cctv ? 'check' : 'xmark' }} me-1"></i>--}}
                {{--                    <span>CCTV</span>--}}
                {{--                    <i class="fa fa-video ms-1"></i>--}}
                {{--                </div>--}}
                {{--                --}}{{-- </div> --}}
                {{--            </div>--}}
            </div>
        </div>
    </div>
@endif

