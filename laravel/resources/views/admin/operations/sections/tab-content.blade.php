<div class="row push mb-4 ">
    <div class="col-sm-1 mt-3">
        {{ __('operations.driver_name') }}
    </div>
    <div class="col-sm-2 mt-2">
        <input type="text" id="driver_name" name="data[{{ $index }}][driver_name]"
            value="{{ $operation_new[$index]->driver_name }}" class="form-control" disabled />
    </div>
    <div class="col-sm-1 mt-3">
        {{ __('operations.driver_sheet') }}
    </div>
    <div class="col-sm-2 mt-3">
        @if (isset($operation_new[$index]->driving_job_id))
            <a href="{{ route('admin.driving-jobs.show', ['driving_job' => $operation_new[$index]->driving_job_id]) }}"
                class="mt-1">{{ $operation_new[$index]->dj_worksheet ? $operation_new[$index]->dj_worksheet : '' }}</a>
        @else
            -
        @endif
    </div>
    <div class="col-sm-1 mt-3">
        {{ __('operations.driving_job_type') }}
    </div>
    <div class="col-sm-2 mt-2">
        <input type="text" value="{{ __('operations.drivig_type_' . $operation_new[$index]->driving_job_type) }}"
            class="form-control" disabled />
    </div>

    @if ($operation_new[$index]->remark)
        <div class="col-auto mt-3">
            {{ __('operations.remark') }}
        </div>
        <div class="col-sm-2 mt-2">
            <input type="text" value="{{ $operation_new[$index]->remark }}" class="form-control" disabled />
        </div>
    @endif
</div>
<div class="row push mb-4 ">
    <div class="col-sm-1 mt-3">
        {{ __('operations.atk') }}
    </div>
    <div class="col-sm-2 mt-3">
        <input type="radio" id="atk" class="form-check-input radio" name="data[{{ $index }}][atk]"
            value="1" @if ($operation_new[$index]->atk_check == 1) checked @endif> ผ่าน &emsp;
        <input type="radio" id="atk" class="form-check-input radio" name="data[{{ $index }}][atk]"
            value="0" @if ($operation_new[$index]->atk_check == 0) checked @endif> ไม่ผ่าน
    </div>
    <div class="col-sm-3 mt-3">
        {{ __('operations.alcohol') }}&emsp;&emsp;
        <input type="radio" id="alcohol_pass{{ $index }}" class="form-check-input radio"
            name="data[{{ $index }}][alcohol]" value="1" @if ($operation_new[$index]->alcohol_check == 1) checked @endif>
        ผ่าน &emsp;
        <input type="radio" id="alcohol_not_pass{{ $index }}" class="form-check-input radio"
            name="data[{{ $index }}][alcohol]" value="0" @if ($operation_new[$index]->alcohol_check == 0) checked @endif>
        ไม่ผ่าน
    </div>

    <div class="col-sm-1 mt-3" id="alcohol_check_radio{{ $index }}">
        {{ __('operations.alcohol_val') }}
    </div>
    <div class="col-sm-2 mt-2" id="alcohol_check{{ $index }}">
        <input type="text" id="alcohol_val" value="{{ $operation_new[$index]->alcohol }}"
            name="data[{{ $index }}][alcohol_val]" class="form-control" />
    </div>
</div>
<div class="row push mb-4">
</div>
<div class="row push">
    @if ($operation_new[$index]->operation)
        @foreach ($operation_new[$index]->operation as $index2 => $data)
            {{-- @if (!empty($operation_new[$index]->operation)) --}}
            <div class="col-sm-3 pr-2 mt-3">
                <div class="form-group row push ">
                    <div class="block block-rounded block-link-shadow block-car block-car-card "
                        href="javascript:void(0)">
                        <div class="block-content block-content-full d-flex justify-content-around">
                            <div class="item item-block ">
                                <img src="{{ isset($data['car_image']) ? $data['car_image'] : asset('images/car-sample/car-placeholder.png') }}"
                                    style=" width:100px; height:100px;" class="fit-image">
                            </div>
                            <div class="ps-3 text-start text-block">
                                <p class="fs-base mb-0 text-dark">
                                    {{ isset($data['car_class']) ? $data['car_class'] : '' }}
                                </p>
                                <p class="fs-lg fw-semibold mb-0 text-primary">
                                    {{ isset($data['license_plate']) ? $data['license_plate'] : '' }}
                                </p>
                                <p class="fs-lg fw-semibold mb-0 ">
                                    @if (isset($data['status']))
                                        @if ($data['status'] == InspectionStatusEnum::NOT_PASS)
                                            {!! badge_render(
                                                __('inspection_cars.class_' . $data['status']),
                                                __('inspection_cars.status_' . $data['status']) .
                                                    ' (' .
                                                    __('inspection_cars.remark_reason_' . $data['remark_reason']) .
                                                    ')',
                                            ) !!}
                                        @else
                                            {!! badge_render(
                                                __('inspection_cars.class_' . $data['status']),
                                                __('inspection_cars.status_' . $data['status']),
                                            ) !!}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto">
            </div>
            {{-- @endif --}}
        @endforeach
    @endif
    <div class="col-sm-3 pr-2 mt-3">
        <div class="form-group row push ">
            <div class="block block-rounded block-link-shadow block-car block-car-card" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex justify-content-around">
                    <div class="item item-block">
                        <img src="{{ $operation_new[$index]->car_image ? $operation_new[$index]->car_image : asset('images/car-sample/car-placeholder.png') }}"
                            style=" width:100px; height:100px;" class="fit-image">
                    </div>
                    <div class="ps-3 text-start text-block">
                        <p class="fs-base mb-0 text-dark">
                            {{ $car->class_full_name }}
                        </p>
                        <p class="fs-lg fw-semibold mb-0 text-primary">
                            {{ $car->license_plate }}
                        </p>
                        <p class="fs-lg fw-semibold mb-0 ">
                            @if (isset($operation_new[$index]->status_detail))
                                @foreach ($operation_new[$index]->status_detail as $index3 => $data2)
                                    @if ($data2['status_inspection'] == InspectionStatusEnum::NOT_PASS)
                                        {{-- <span style="font-size:15px;">{{__('operations.transfer_type_' . $data2['transfer_type'])}}</span> --}}
                                        {!! badge_render(
                                            __('inspection_cars.class_' . $data2['status_inspection']),
                                            __('operations.transfer_type_' . $data2['transfer_type']) .
                                                ' : ' .
                                                __('inspection_cars.status_' . $data2['status_inspection']) .
                                                ' (' .
                                                __('inspection_cars.remark_reason_' . $data2['remark_reason']) .
                                                ')',
                                        ) !!}
                                    @else
                                        {{-- <span style="font-size:15px;">{{__('operations.transfer_type_' . $data2['transfer_type'])}}</span> --}}
                                        {!! badge_render(
                                            __('inspection_cars.class_' . $data2['status_inspection_job']),
                                            __('operations.transfer_type_' . $data2['transfer_type']) .
                                                ' : ' .
                                                __('inspection_cars.status_' . $data2['status_inspection_job']),
                                        ) !!}
                                    @endif
                                    <br>
                                @endforeach
                            @else
                                @if ($operation_new[$index]->status_inspection == InspectionStatusEnum::NOT_PASS)
                                    {!! badge_render(
                                        __('inspection_cars.class_' . $operation_new[$index]->status_inspection),
                                        __('inspection_cars.status_' . $operation_new[$index]->status_inspection) .
                                            ' (' .
                                            __('inspection_cars.remark_reason_' . $operation_new[$index]->remark_reason) .
                                            ')',
                                    ) !!}
                                @else
                                    {!! badge_render(
                                        __('inspection_cars.class_' . $operation_new[$index]->status_inspection),
                                        __('inspection_cars.status_' . $operation_new[$index]->status_inspection),
                                    ) !!}
                                @endif
                            @endif
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-auto">
    </div>
    
    @if ($operation_new[$index]->operation && count($operation_new[$index]->operation) > 0)
    </div>
    <div class="row push">
    @endif
    <div class="col-sm-2">
        <x-forms.select-option id="data[{{ $index }}][key]" :value="$operation_new[$index]->pick_up_keys" :list="$key_lists"
            :label="__('operations.key')" />
        <div class="col-sm-12 mt-3">
            <span>{{ __('operations.car_transfer_sheet') }}</span><br>

            @if ($operation_new[$index]->car_park_transfer_id)
                <a href="{{ route('admin.car-park-transfers.show', ['car_park_transfer' => $operation_new[$index]->car_park_transfer_id]) }}"
                    class="mt-1">{{ $operation_new[$index]->car_park_transfer_no ? $operation_new[$index]->car_park_transfer_no : '' }}</a>
            @else
                -
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <x-forms.select-option id="data[{{ $index }}][key_address]" :value="$operation_new[$index]->keys_address" :list="$key_address_lists"
            :label="__('operations.key_address')" />
        @if ($operation_new[$index]->driving_job_type != DrivingJobTypeStatusEnum::SIDE_JOB)
            <div class="col-sm-12 mt-3">
                <span>{{ __('operations.inspection_sheet') }}</span><br>
                @foreach ($operation_new[$index]->inspection_sheet as $inspection_job)
                    @if ($inspection_job)
                        <a href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $inspection_job['id']]) }}"
                            class="mt-1">{{ $inspection_job['worksheet'] ? '(' . __('operations.transfer_type_' . $inspection_job['transfer_type']) . ') ' . $inspection_job['worksheet'] : '' }}</a><br>
                    @else
                        -
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    <div class="col-sm-2">
        <x-forms.date-input id="data[{{ $index }}][actual_prepare_date]" :value="$operation_new[$index]->actual_prepare_date" :label="__('operations.pickup_key_date')"
            :optionals="[
                'date_enable_time' => true,
            ]" />
        @if ($operation_new[$index]->driving_job_type != DrivingJobTypeStatusEnum::SIDE_JOB)
            <div class="col-sm-12 mt-3">
                <span>{{ __('operations.use_car_sheet') }}</span><br>
                <a href="#" class="mt-1"></a>
            </div>
        @endif
    </div>

    <div class="col-sm-2">
        <x-forms.date-input id="data[{{ $index }}][actual_end_date]" :value="$operation_new[$index]->actual_end_date" :label="__('operations.return_time')"
            :optionals="[
                'date_enable_time' => true,
                'input_class' => 'js-flatpickr form-control flatpickr-input',
            ]" />
    </div>
    <div class="col-sm-3">
        <div class="col-sm-2">
        </div>
    </div>
</div>
@if ($operation_new[$index]->driving_job_type != DrivingJobTypeStatusEnum::SIDE_JOB)
    <h4 class="grey-text">{{ __('operations.accessory') }}</h4>
    <hr>
    @if (!$operation_new[$index]->product->isEmpty())
        @if ($car->self_drive_type != SelfDriveTypeEnum::PICKUP)
            <h5 class="grey-text mt-2">ขาไป</h5>
            @foreach ($operation_new[$index]->product as $index2 => $product_add)
                <div class="row push">
                    <div class="col-sm-1 mt-1">
                        <input class="form-check-input form-check-input-each" type="checkbox"
                            name="data[{{ $index }}][product][{{ $product_add->id }}][check_out]"
                            id="data[{{ $index }}][product][{{ $product_add->id }}][check_out]"
                            value="{{ STATUS_ACTIVE }}" @if ($product_add->outbound_is_check == STATUS_ACTIVE) checked @endif>
                        {{ $product_add->name }}
                    </div>
                    <div class="col-auto mt-1">
                        จำนวน
                    </div>
                    <div class="col-sm-2">
                        <input type="number" class="form-control" value="{{ $product_add->amount }}" disabled />
                        <x-forms.hidden
                            id="data[{{ $index }}][product][{{ $product_add->id }}][rental_product_add_id]"
                            :value="$product_add->id" />
                    </div>
                </div>
            @endforeach
        @endif
        @if ($car->self_drive_type != SelfDriveTypeEnum::SEND)
            <h5 class="grey-text mt-2">ขากลับ</h5>
            @foreach ($operation_new[$index]->product as $index2 => $product_add)
                <div class="row push">
                    <div class="col-sm-1 mt-1">
                        {{ $product_add->name }}
                    </div>
                    <div class="col-auto mt-1">
                        <input type="radio"
                            id="data[{{ $index }}][product][{{ $product_add->id }}][check_in_pass]"
                            class="form-check-input radio"
                            name="data[{{ $index }}][product][{{ $product_add->id }}][check_in]"
                            @if ($product_add->inbound_approve == STATUS_ACTIVE) checked @endif value="1"> ผ่าน &emsp;
                        <input type="radio"
                            id="data[{{ $index }}][product][{{ $product_add->id }}][check_in_not_pass]"
                            class="form-check-input radio"
                            name="data[{{ $index }}][product][{{ $product_add->id }}][check_in]"
                            @if ($product_add->inbound_approve == STATUS_DEFAULT) checked @endif value="0"> ไม่ผ่าน

                    </div>
                    <div class="col-sm-2">
                        <input type="text"
                            id="data[{{ $index }}][product][{{ $product_add->id }}][inbound_remark]"
                            name="data[{{ $index }}][product][{{ $product_add->id }}][inbound_remark]"
                            class="form-control" value="{{ $product_add->inbound_remark }}" />
                    </div>
                </div>
            @endforeach
        @endif
    @else
        <div class="col-sm-1 mt-3 mb-3">
            -
        </div>
    @endif

    @if ($operation->serviceType->service_type == ServiceTypeEnum::SELF_DRIVE)
        <h4 class="grey-text">{{ __('operations.time_info') }}</h4>
        <hr>
        <h4 class="grey-text">{{ __('operations.estimate') }}</h4>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_prepare_date]" :value="$operation_new[$index]->estimate_prepare_date"
                    :label="__('operations.prepare_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_start_date]" :value="$operation_new[$index]->estimate_start_date"
                    :label="__('operations.start_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            @if ($car->self_drive_type != SelfDriveTypeEnum::PICKUP)
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][estimate_end_job_date]" :value="$operation_new[$index]->estimate_end_job_date"
                        :label="__('operations.delivery_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            @else
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][estimate_end_job_date]" :value="$operation_new[$index]->estimate_end_job_date"
                        :label="__('operations.pickup_customer_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            @endif
        </div>

        <h4 class="grey-text">{{ __('operations.actual') }}</h4>
        @if ($car->self_drive_type != SelfDriveTypeEnum::PICKUP)
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_prepare_date]" :value="$operation_new[$index]->actual_prepare_date"
                        :label="__('operations.prepare_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_start_date]" :value="$operation_new[$index]->actual_start_date"
                        :label="__('operations.start_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_end_job_date]" :value="$operation_new[$index]->actual_end_job_date"
                        :label="__('operations.delivery_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_arrive_date]" :value="$operation_new[$index]->actual_arrive_date"
                        :label="__('operations.return_tls_time')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            </div>

            <div class="row push">

                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_end_date]" :value="$operation_new[$index]->actual_end_date"
                        :label="__('operations.return_key_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            </div>
        @else
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_prepare_date]" :value="$operation_new[$index]->actual_prepare_date"
                        :label="__('operations.prepare_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_start_date]" :value="$operation_new[$index]->actual_start_date"
                        :label="__('operations.start_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][prepare_date]" :value="$operation_new[$index]->actual_end_job_date"
                        :label="__('operations.pickup_customer_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_arrive_date]" :value="$operation_new[$index]->actual_arrive_date"
                        :label="__('operations.return_tls_time')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            </div>

            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.date-input id="data[{{ $index }}][actual_end_date]" :value="$operation_new[$index]->actual_end_date"
                        :label="__('operations.return_key_date')" :optionals="[
                            'date_enable_time' => true,
                            'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                        ]" />
                </div>
            </div>
        @endif
    @else
        <h4 class="grey-text">{{ __('operations.time_info') }}</h4>
        <hr>
        <h4 class="grey-text">{{ __('operations.estimate') }}</h4>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_prepare_date]" :value="$operation_new[$index]->estimate_prepare_date"
                    :label="__('operations.prepare_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_start_date]" :value="$operation_new[$index]->estimate_prepare_date"
                    :label="__('operations.start_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_start_date]" :value="$operation_new[$index]->estimate_rented_date"
                    :label="__('operations.to_customer_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_end_job_date]" :value="$operation_new[$index]->estimate_end_job_date"
                    :label="__('operations.return_from_customer')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_arrive_date]" :value="$operation_new[$index]->estimate_arrive_date"
                    :label="__('operations.return_tls_time')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_end_date]" :value="$operation_new[$index]->estimate_end_date"
                    :label="__('operations.return_key_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
        </div>
        <h4 class="grey-text">{{ __('operations.actual') }}</h4>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][actual_prepare_date]" :value="$operation_new[$index]->actual_prepare_date"
                    :label="__('operations.prepare_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][actual_start_date]" :value="$operation_new[$index]->actual_start_date"
                    :label="__('operations.start_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][estimate_start_date]" :value="$operation_new[$index]->actual_rented_date"
                    :label="__('operations.to_customer_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][actual_end_job_date]" :value="$operation_new[$index]->actual_end_job_date"
                    :label="__('operations.return_from_customer')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>

        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][actual_arrive_date]" :value="$operation_new[$index]->actual_arrive_date"
                    :label="__('operations.return_tls_time')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="data[{{ $index }}][actual_end_date]" :value="$operation_new[$index]->actual_end_date"
                    :label="__('operations.return_key_date')" :optionals="[
                        'date_enable_time' => true,
                        'input_class' => 'js-flatpickr form-control flatpickr-input disable',
                    ]" />
            </div>
        </div>
    @endif
@endif
