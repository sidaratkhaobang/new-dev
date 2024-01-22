<div class="block {{ __('block.styles') }}">
    <div class="block-header">
        <h3 class="block-title">{{ __('short_term_rentals.sheet_detail') }} : {{ $operation->worksheet_no }}
        </h3>
        <div class="block-options">
        </div>
    </div>
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <h4 class="grey-text">{{ __('short_term_rentals.work_detail') }}</h4>
            <hr>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.branch') }}</p>
                    <p>{{ $operation->branch ? $operation->branch->name : '-' }}</p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.package') }}</p>
                    <p>{{ $operation->product ? $operation->product->name : '-' }}</p>
                </div>
            </div>
            <h4 class="grey-text">{{ __('short_term_rentals.work_detail') }}</h4>
            <hr>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.pickup_datetime') }}</p>
                    <p>{{ $operation->pickup_date ? get_thai_date_format($operation->pickup_date, 'd/m/Y H:i') : '-' }}
                    </p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.return_datetime') }}</p>
                    <p>{{ $operation->return_date ? get_thai_date_format($operation->return_date, 'd/m/Y H:i') : '-' }}
                    </p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.origin') }}</p>
                    <p>{{ $operation->origin ? $operation->origin->name : $operation->origin_name }}</p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.destination') }}</p>
                    <p>{{ $operation->destination ? $operation->destination->name : $operation->destination_name }}</p>
                </div>
            </div>
            <h4 class="grey-text">{{ __('short_term_rentals.customer_detail') }}</h4>
            <hr>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.customer_type') }}</p>
                    <p>{{ $operation->customer_type ? __('short_term_rentals.customer_type_' . $operation->customer_type) : '-' }}
                    </p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.customer_code') }}</p>
                    <p>{{ $operation->customer_code ? $operation->customer_code : '-' }}</p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.customer') }}</p>
                    <p>{{ $operation->customer_name ? $operation->customer_name : '-' }}</p>
                </div>
            </div>
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.email') }}</p>
                    <p>{{ $operation->customer_email ? $operation->customer_email : '-' }}</p>
                </div>
                <div class="col-sm-3">
                    <p class="grey-text size-text">{{ __('short_term_rentals.tel') }}</p>
                    <p>{{ $operation->customer_tel ? $operation->customer_tel : '-' }}</p>
                </div>
                <div class="col-sm-6">
                    <p class="grey-text size-text">{{ __('short_term_rentals.address') }}</p>
                    <p>{{ $operation->customer_address ? $operation->customer_address : '-' }}</p>
                </div>
            </div>
            @if ($cars)
                @foreach ($cars as $index => $car)
                    <div class="row push mb-4">
                        <div class="col-sm-6">
                            <h4 class="grey-text">{{ __('short_term_rentals.car_info') }}</h4>
                            <hr>
                            <div class="form-group row push mb-5 mt-3">
                                <div class="col-sm-6">
                                    <div class="block block-rounded block-link-shadow block-car"
                                        href="javascript:void(0)">
                                        <div class="block-content block-content-full d-flex justify-content-around">
                                            <div class="item item-block">
                                                <img src="{{ count($car->car_image_view) > 0 ? $car->car_image_view[0]['url'] : asset('images/car-sample/car-placeholder.png') }}"
                                                    style=" width:100px; height:70px;" class="fit-image">
                                            </div>
                                            <div class="ps-3 text-start text-block">
                                                <p class="fs-base mb-0 text-dark">
                                                    {{ $car->class_full_name }}
                                                </p>
                                                <p class="fs-lg fw-semibold mb-0 text-primary">
                                                    {{ $car->license_plate }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <h4 class="grey-text">{{ __('short_term_rentals.product_additional_detail') }}</h4>
                            <hr>
                            <div class="form-group row push mb-5 mt-3">
                                @foreach ($car->product_main as $index2 => $product)
                                    <div class="col-sm-3">
                                        <div class="block block-rounded block-link-shadow block-car"
                                            href="javascript:void(0)">
                                            <div class="block-content block-content-full d-flex ">
                                                <div class="ps-3 text-start">
                                                    <p class="fs-base mb-0 text-dark">
                                                        {{ $product['name'] }}
                                                    </p>
                                                    <p class="fs-lg fw-semibold mb-0 ">
                                                        <i class="fa-solid fa-shopping-cart fa-2xs"
                                                            style="color: #A3A3A3;" aria-hidden="true">&nbsp;
                                                            {{ $product['amount'] }}</i>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            @if (in_array($service_type, [ServiceTypeEnum::SLIDE_FORKLIFT]))
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <h4 class="grey-text">{{ __('short_term_rentals.product_send') }}</h4>
                        <hr>
                        @foreach ($product_transport_list as $index => $item_send)
                            <div class="form-group row push mb-5 mt-3">
                                <div class="col-sm-6">
                                    <div class="block block-rounded block-link-shadow block-car"
                                        href="javascript:void(0)">
                                        <div
                                            class="block-car block-content block-content-full d-flex justify-content-around block-car-content">
                                            <div class="item item-block ">
                                                <span class="fs-base text-dark">
                                                    {{ $item_send->brand_name }}
                                                </span>
                                                <span class="text-muted">
                                                    {{ $item_send->class_name }}
                                                </span>
                                                <img src="{{ count($item_send->product_files) > 0 ? $item_send->product_files[0]['url'] : asset('images/car-sample/car-placeholder.png') }}"
                                                    style=" width:100px; height:70px;" class="fit-image">
                                            </div>
                                            <div class="ps-3 text-start text-block mt-4">
                                                <p class="fs-lg fw-semibold mb-2 text-primary">
                                                    {{ $item_send->license_plate }}
                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fa fa-palette"></i>
                                                    {{ $item_send->color_name }}

                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fa-sharp fa-solid fa-car-battery"></i>
                                                    {{ $item_send->engine }}

                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fas fa-oil-can"></i>
                                                    {{ $item_send->chassis }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-sm-6">
                        <h4 class="grey-text">{{ __('short_term_rentals.product_return') }}</h4>
                        <hr>
                        @foreach ($product_transport_return_list as $index => $item_return)
                            <div class="form-group row push mb-5 mt-3">
                                <div class="col-sm-6">
                                    <div class="block block-rounded block-link-shadow block-car"
                                        href="javascript:void(0)">
                                        <div
                                            class="block-car block-content block-content-full d-flex justify-content-around block-car-content">
                                            <div class="item item-block ">
                                                <span class="fs-base text-dark">
                                                    {{ $item_return->brand_name_return }}
                                                </span>
                                                <span class="text-muted">
                                                    {{ $item_return->class_name_return }}
                                                </span>
                                                <img src="{{ count($item_return->product_files_return) > 0 ? $item_return->product_files_return[0]['url'] : asset('images/car-sample/car-placeholder.png') }}"
                                                    style=" width:100px; height:70px;" class="fit-image">
                                            </div>
                                            <div class="ps-3 text-start text-block mt-4">
                                                <p class="fs-lg fw-semibold mb-2 text-primary">
                                                    {{ $item_return->license_plate_return }}
                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fa fa-palette"></i>
                                                    {{ $item_return->color_name_return }}

                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fa-sharp fa-solid fa-car-battery"></i>
                                                    {{ $item_return->engine_return }}

                                                </p>
                                                <p class="fs-sm text-muted mb-0">
                                                    <i class="fas fa-oil-can"></i>
                                                    {{ $item_return->chassis_return }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
