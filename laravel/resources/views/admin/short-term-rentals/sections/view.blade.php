<div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('short_term_rentals.sheet_detail') }} : {{ $short_term_rental->worksheet_no }}
                &nbsp;
                {!! badge_render(
                    __('short_term_rentals.class_' . $short_term_rental->status),
                    __('short_term_rentals.status_' . $short_term_rental->status),
                ) !!}
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
                        <p>{{ $short_term_rental->branch_name }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.package') }}</p>
                        <p>{{ $short_term_rental->product_name }}</p>
                    </div>
                </div>

                <h4 class="grey-text">{{ __('short_term_rentals.work_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.pickup_datetime') }}</p>
                        <p>{{ get_thai_date_format($short_term_rental->pickup_date, 'd/m/Y H:i') }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.return_datetime') }}</p>
                        <p>{{ get_thai_date_format($short_term_rental->return_date, 'd/m/Y H:i') }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.origin') }}</p>
                        <p>{{ $short_term_rental->origin_name }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.destination') }}</p>
                        <p>{{ $short_term_rental->destination_name }}</p>
                    </div>
                </div>

                <h4 class="grey-text">{{ __('short_term_rentals.customer_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.customer_type') }}</p>
                        <p>{{ $short_term_rental->customer_type ? __('short_term_rentals.customer_type_' . $short_term_rental->customer_type) : '-' }}
                        </p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.customer_code') }}</p>
                        <p>{{ $short_term_rental->customer_code ? $short_term_rental->customer_code : '-' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.customer') }}</p>
                        <p>{{ $short_term_rental->customer_name ? $short_term_rental->customer_name : '-' }}</p>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.email') }}</p>
                        <p>{{ $short_term_rental->customer_email ? $short_term_rental->customer_email : '-' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.tel') }}</p>
                        <p>{{ $short_term_rental->customer_tel ? $short_term_rental->customer_tel : '-' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="grey-text size-text">{{ __('short_term_rentals.address') }}</p>
                        <p>{{ $short_term_rental->customer_address }}</p>
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <h4 class="grey-text">{{ __('short_term_rentals.car_info') }}</h4>
                        <hr>
                        <div class="form-group row push mb-5 mt-3">
                            <div class="col-sm-6">
                                <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                                    <div class="block-content block-content-full d-flex justify-content-around">
                                        <div class="item item-block">
                                            <img src="{{ $car_image ? $car_image['url'] : asset('images/car-sample/car-placeholder.png') }}"
                                                style=" width:100px; height:70px;">
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
                            <div class="col-sm-3">
                                <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                                    <div class="block-content block-content-full d-flex ">
                                        <div class="ps-3 text-start">
                                            <p class="fs-base mb-0 text-dark">
                                                ป้ายต้อนรับ
                                            </p>
                                            <p class="fs-lg fw-semibold mb-0 ">
                                                <i class="fa-solid fa-shopping-cart fa-2xs" style="color: #A3A3A3;"
                                                    aria-hidden="true">&nbsp; 1</i>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-3">
                                <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                                    <div class="block-content block-content-full d-flex ">
                                        <div class="ps-3 text-start">
                                            <p class="fs-base mb-0 text-dark">
                                                ที่นั่งเด็ก
                                            </p>
                                            <p class="fs-lg fw-semibold mb-0 ">
                                                <i class="fa-solid fa-shopping-cart fa-2xs" style="color: #A3A3A3;"
                                                    aria-hidden="true">&nbsp; 1</i>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <h4 class="grey-text">{{ __('short_term_rentals.description_other') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <p class="grey-text">{{ __('short_term_rentals.objective') }}</p>
                        <p>{{ $short_term_rental->remark ? $short_term_rental->remark : '-' }}</p>
                    </div>
                    <div class="col-sm-3">
                        <p class="grey-text">{{ __('short_term_rentals.remark') }}</p>
                        <p>{{ $short_term_rental->remark ? $short_term_rental->remark : '-' }}</p>
                    </div>
                </div>

                <div class="row push">
                    <div class="col-sm-6">
                        <h4 class="grey-text">{{ __('short_term_rentals.promotion') }}</h4>
                        <hr>
                        <div class="row push ">
                            <div class="form-group row push mb-5 mt-3">
                                <div class="col-sm-4">
                                    <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                                        <div class="block-content block-content-full d-flex justify-content-around">
                                            <div class="">
                                                <img src="https://www.ktc.co.th/pub/media/ktc_logo.png"
                                                    style=" width:80px; height:70px;">
                                            </div>
                                            <div class="ps-3 text-start">
                                                <p class="fs-md fw-semibold mb-0 text-dark">
                                                    ลดพิเศษ 20%
                                                </p>
                                                <p class="mb-0 text-secondary">
                                                    หมดเขตใน <span class="mb-0 text-primary">24 วัน</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <h4 class="grey-text">{{ __('short_term_rentals.voucher') }}</h4>
                        <hr>
                        <div class="row push ">
                            <div class="form-group row push mb-5 mt-3">
                                <div class="col-sm-6">
                                    <div class="block block-rounded block-link-shadow block-car"
                                        href="javascript:void(0)">
                                        <div class="block-content block-content-full d-flex justify-content-around">
                                            <div class="">
                                                <img src="https://passiondelivery.com/pub/media/catalog/product/cache/7e6e59e80a69ca81b40190dbfa9e211f/v/o/voucher-03.jpg"
                                                    style=" width:80px; height:70px;">
                                            </div>
                                            <div class="ps-3 text-start ">
                                                <p class="fs-md fw-semibold mb-0 text-dark">
                                                    บัตรกำนัล 10,000 บาท (พิเศษเฉพาะ...
                                                </p>
                                                <p class="mb-0 text-secondary">
                                                    หมดเขตใน <span class="mb-0 text-primary">24 วัน</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="grey-text">{{ __('short_term_rentals.summary') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <p class="grey-text size-text">{{ __('short_term_rentals.total_cost') }}</p>
                        <a href="#" class="text-primary">Q147/2565Rev.04</a>
                    </div>
                </div>

                <h4 class="grey-text">{{ __('short_term_rentals.history_log') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <a href="#" class="text-primary">ประวัติแก้ไขทั้งหมด</a>
                    </div>
                </div>

                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.short-term-rentals.index') }}">{{ __('lang.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

