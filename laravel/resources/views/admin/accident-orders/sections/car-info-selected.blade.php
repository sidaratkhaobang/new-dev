<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center">
            <div class="">
                <h4><i class="fa fa-file-lines me-1"></i>
                    {{ __('repairs.car_table') }}</h4>
            </div>
            <div class="" id="modal-history" style="display: none;">
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openModalAccident()" href="javascript:void(0)">
                    <i class="fa fa-arrow-rotate-left opacity-50 me-1"></i> {{ __('repairs.accident_history') }}
                </a>
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openModalMaintain()"
                    href="javascript:void(0)">
                    <i class="fa fa-arrow-rotate-left opacity-50 me-1"></i> {{ __('repairs.maintain_history') }}
                </a>
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openModalCondition()"
                    href="javascript:void(0)">
                    <i class="fa fa-file-lines opacity-50 me-1"></i> {{ __('repairs.condition') }}
                </a>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="car_id" :list="null" :value="$d->car_id" :label="__('repairs.car_no')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $car_license,
                        'required' => true,
                    ]" />
            </div>
        </div>
        <div id="car-detail-section"
            @if ($car_data->id) style="display: block;" @else  style="display: none;" @endif>
            <div class="py-4 bg-body-extra-light d-flex flex-column flex-md-row ">
                <div class="car-border">
                    <div class="py-1 mb-4">
                        <p id="car_class" class="fs-6 fw-bolder mb-1">
                            {{ $car_data->car_class ?? '' }}
                        </p>
                    </div>
                    <div class="py-1 car-section mb-4">
                        {{-- @if (isset($car->image) && isset($car->image['url'])) --}}
                        {{-- <img id="img" class="img-fluid" src='{{ $car->image['url'] }}'
                                alt=""> --}}
                        {{-- @else --}}
                        <img id="img" class="img-fluid" src='{{ asset('images/car-sample/car-placeholder.png') }}'
                            alt="">
                        {{-- @endif --}}
                    </div>
                    <div class="py-1">
                        <p class="font-w600 mb-1">หมายเลขตัวถัง: <span
                                id="chassis_no">{{ $car_data->chassis_no ?? '' }}</span></p>
                        <p class="font-w600 mb-1">หมายเลขเครื่องยนต์: <span
                                id="engine_no">{{ $car_data->engine_no ?? '' }}</span></p>
                        <p class="font-w600 mb-1">ทะเบียนรถ: <span
                                id="license_plate">{{ $car_data->license_plate ?? '' }}</span></p>
                        <p class="font-w600 mb-1">ผู้ถือกรรมสิทธิ์: <span id="license_plate">{{ '' }}</span>
                        </p>
                        {{-- <p class="font-w600 mb-1">เข็มไมล์ล่าสุด: <span
                                id="current_mileage">{{ $car_data->current_mileage ?? '' }}</span></p> --}}
                    </div>
                    <div class="py-1" id="car_status"
                        @if ($car_data->car_status) style="display: block;" @else  style="display: none;" @endif>
                        {{-- {!! badge_render(__('cars.class_' . $car_data->car_status), __('cars.status_' . $car_data->car_status)) !!} --}}
                    </div>
                    {{-- @endif --}}
                </div>
                <div class="ms-4 me-5 flex-grow-1 text-center text-md-start my-3 my-md-0">
                    <div id="rental_show"
                        @if ($rental > 0) style="display: block;" @else  style="display: none;" @endif>
                        <div class="row">
                            <div class="col-sm-3 col-lg-2">
                                <h5 class="fw-bold">
                                    <i class="fa fa-file-contract me-2"></i> งานเช่า
                                </h5>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">เลขที่ใบขอเช่า</p>
                                <p class="size-text" id="rental_no">{{ $car_data->rental_worksheet_no ?? '' }}</p>
                                <x-forms.hidden id="rental_id" :value="$car_data->rental_id ?? ''" />
                                <x-forms.hidden id="rental_type" :value="$car_data->rental_type ?? ''" />
                            </div>
                            <div class="col-sm-4">
                                <p class="grey-text">ผู้เช่า</p>
                                <p class="size-text" id="rental_name">{{ $car_data->rental_customer_name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 col-lg-2">
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">เลขที่สัญญา</p>
                                <p class="size-text" id="contract_no">{{ $car_data->contract_worksheet_no ?? '' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">วันเริ่มสัญญา</p>
                                <p class="size-text" id="contract_start_date">
                                    {{ $car_data->contract_pick_up_date ?? '' }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">วันสิ้นสุดสัญญา</p>
                                <p class="size-text" id="contract_end_date">{{ $car_data->contract_return_date ?? '' }}
                                </p>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3 col-lg-2">
                            <h5 class="fw-bold">
                                <i class="fa fa-file-contract me-2"></i> ประกันภัย
                            </h5>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">กรมธรรม์ประกันภัยเลขที่</p>
                            <p class="size-text" id="insurance_no">1204-58108</p>
                        </div>
                        <div class="col-sm-4">
                            <p class="grey-text">บริษัท</p>
                            <p class="size-text" id="company">เอเชียประกันภัย 1950 จำกัด</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">วันเริ่มคุ้มครอง</p>
                            <p class="size-text" id="coverage_start_date">4/1/2022 16:30</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">วันสิ้นสุดคุ้มครอง</p>
                            <p class="size-text" id="coverage_end_date">4/1/2022 16:30</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">ทุนประกัน</p>
                            <p class="size-text" id="sum_insured">300,000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.repairs.modals.accident-history-modal')
    @include('admin.repairs.modals.maintain-history-modal')
    @include('admin.repairs.modals.condition-modal')
</div>
