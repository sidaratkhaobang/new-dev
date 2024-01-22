<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center">
            <div class="">
                <h3 class="h4 mb-1"><i class="fa fa-file-lines me-1"></i>
                    {{ __('replacement_cars.car_detail_' . $car_type) }}</h3>
            </div>
            <div class="">
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openAccessoryModal('{{ $car_type }}')"
                    href="javascript:void(0)">
                    <i class="fa fa-wrench opacity-50 me-1"></i> ข้อมูลอุปกรณ์เสริม
                </a>
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openAccidentModal('{{ $car_type }}')"
                    href="javascript:void(0)">
                    <i class="fa fa-arrow-rotate-left opacity-50 me-1"></i> ประวัติอุบัติเหตุ
                </a>
                <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openRepairModal('{{ $car_type }}')"
                    href="javascript:void(0)">
                    <i class="fa fa-arrow-rotate-left opacity-50 me-1"></i> ประวัติซ่อมบำรุง
                </a>
                @if (strcmp($car_type, 'main') == 0)
                    <a class="btn btn-sm btn-primary px-3 me-1 my-1" onclick="openConditionModal('{{ $car_type }}')"
                        href="javascript:void(0)">
                        <i class="fa fa-file-lines opacity-50 me-1"></i> เงื่อนไขบริการ
                    </a>
                @endif
            </div>
        </div>
        @if (strcmp($car_type, 'replace') == 0)
            <div class="car-select-section">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="replacement_car_id" :list="$available_replacement_car" :value="null"
                            :label="__('replacement_cars.replace_license_plate')" :optionals="['required' => true]" />
                        @if (!$required_lower_spec)
                            <x-forms.hidden id="replacement_car_id" name="replacement_car_id" :value="$car->id" />
                        @endif

                    </div>
                    @if ($required_lower_spec || $d->is_spec_low)
                        <div class="col-sm-2 text-end align-self-end">
                            <x-forms.checkbox-inline id="is_spec_low" :list="[
                                [
                                    'id' => STATUS_ACTIVE,
                                    'name' => __('replacement_cars.spec_lower'),
                                    'value' => STATUS_ACTIVE,
                                ],
                            ]" :label="null"
                                :value="[$d->is_spec_low]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.input-new-line id="spec_low_reason" :value="$d->spec_low_reason" :label="__('replacement_cars.spec_low_reason')" />
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <div class="{{ count((array) $car) > 0 ? 'show' : 'hide' }}" id="{{ $car_type }}-car-detail-section">
            <div class="py-4 bg-body-extra-light d-flex flex-column flex-md-row ">
                <div class="car-border">
                    <div class="py-1 mb-4">
                        <p id="{{ $car_type }}-class-name" class="fs-6 fw-bolder mb-1">{{ $car->class_name ?? '' }}
                        </p>
                    </div>
                    <div class="py-1 car-section mb-4">
                        @if (isset($car->image) && isset($car->image['url']))
                            <img id="{{ $car_type }}-img" class="img-fluid" src='{{ $car->image['url'] }}'
                                alt="">
                        @else
                            <img id="{{ $car_type }}-img" class="img-fluid"
                                src='{{ asset('images/car-sample/car-placeholder.png') }}' alt="">
                        @endif
                    </div>
                    <div class="py-1">
                        <p class="font-w800 mb-1">หมายเลขตัวถัง: <span
                                id="{{ $car_type }}-chassis-no">{{ $car->chassis_no ?? '' }}</span></p>
                        <p class="font-w800 mb-1">ทะเบียนรถ: <span
                                id="{{ $car_type }}-license-plate">{{ $car->license_plate ?? '' }}</span></p>
                    </div>
                    {{-- @endif --}}
                </div>
                <div class="ms-4 me-5 flex-grow-1 text-center text-md-start my-3 my-md-0">
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                            <h5 class="fw-bold">
                                <i class="fa fa-file-contract me-2"></i> พรบ.
                            </h5>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">เลขกรมธรรม์ พรบ.</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold " id="{{ $car_type }}-policy-number">CMT/00840734</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">วันเริ่มคุ้มครอง (พรบ.)</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-policy-start-date">4/1/2022 16:30</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">วันสิ้นสุดคุ้มครอง (พรบ.)</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-policy-end-date">4/1/2025 16:30</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-sm-3 col-lg-2">
                            <h5 class="fw-bold">
                                <i class="fa fa-file-contract me-2"></i> ประกันภัย
                            </h5>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">กรมธรรม์ประกันภัยเลขที่</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-insurance-no">1204-58108</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">บริษัท</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-insurance-company">เอเชียประกันภัย 1950
                                จำกัด</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">วันเริ่มคุ้มครอง</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-insurance-start-date">4/1/2022 16:30</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-lg-2">
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary">วันสิ้นสุดคุ้มครอง</p>
                        </div>
                        <div class="col-sm-3 col-lg-4">
                            <p class="fw-semibold" id="{{ $car_type }}-insurance-end-date">4/1/2025 16:30</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-sm-3 col-lg-2">
                            <h5 class="fw-bold">
                                <i class="fa fa-file-contract me-2"></i> ใบตรวจรถ
                            </h5>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary"></p>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="fw-semibold"></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-sm-3 col-lg-2">
                            <h5 class="fw-bold">
                                <i class="fa fa-file-contract me-2"></i> ใบงานผู้ขับรถ
                            </h5>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="me-3 text-secondary"></p>
                        </div>
                        <div class="col-sm-3 col-lg-2">
                            <p class="fw-semibold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
