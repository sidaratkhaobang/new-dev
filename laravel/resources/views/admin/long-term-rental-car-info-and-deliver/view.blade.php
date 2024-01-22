@extends('admin.layouts.layout')

@section('page_title', __('lang.edit') . __('long_term_rentals.page_title'))

@push('custom_styles')
    <style>
        .block-header {
            padding: 0;
        }

        .img-fluid {
            width: 250px;
            height: 100px;
            object-fit: cover;
            /* display: block; */
            /* margin: auto; */
        }

        .card-car-info {
            border: 1px solid #CBD4E1;
            border-radius: 6px;
        }

        .car-border {
            /*border: 1px solid #CBD4E1;*/
            width: 30%;
            color: #475569;
            padding: 1rem;
            height: fit-content;
        }
        p > .badge {
            width: 100%;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.long-term-rentals.sections.btn-group')
            <form id="save-form">
                <x-forms.hidden id="lt_rental_id" :value="$d->id"/>
                <h4>{{ __('ข้อมูลการส่งมอบ') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="contract_start_date" :value="$d->contract_start_date" :label="__('long_term_rentals.contract_start_date')"
                            :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="contract_end_date" :value="$d->contract_end_date" :label="__('long_term_rentals.contract_end_date')"
                            :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                </div>


                <div class="row push mb-5">
                    <div class="col-sm-3 mb-1">
                        <x-forms.date-input id="date_deliver" :value="$d->date_delivery" :label="__('วันที่ส่งมอบ')" :optionals="['placeholder' => __('lang.select_date')]"/>
                    </div>
                    <div class="col-sm-3 mb-1">
                        <x-forms.input-new-line id="location_deliver" :value="$d->location_delivery" :label="__('สถานที่ส่งมอบ')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                    </div>
                    <div class="col-sm-3 mb-1">
                        <x-forms.input-new-line id="user_receive" :value="$d->name_user_receive" :label="__('ผู้รับรถ')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                    </div>
                    <div class="col-sm-3 mb-1">
                        <x-forms.input-new-line id="phone_number" :value="$d->phone_user_receive" :label="__('เบอร์โทร')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                    </div>
                </div>
                <div class="block-header mb-3">
                    <h4><i class="fa fa-file-lines"></i> {{ __('ข้อมูลรถ') }}</h4>
                    @if(count($car_list) > 0)
                        <button class="btn btn-primary btn-custom-size btn-open-modal-create-contract" type="button">
                            {{ __('ขอจัดทำสัญญา') }}
                        </button>
                    @endif
                </div>

                @foreach($lt_pr_line_car as $item)
                    <div class="py-2 bg-body-extra-light d-flex flex-column flex-md-row card-car-info mb-4">
                        <div class="car-border">
                            <div class="py-1 mb-4">
                                <p class="fs-6 fw-bolder mb-1">{{$item->car?->carClass?->full_name}}</p>
                            </div>
                            <div class="py-1 car-section mb-4">
                                @if (isset($item->carImage['url']))
                                    <img class="img-fluid" src='{{ $item->carImage['url'] }}' alt="">
                                @else
                                    <img class="img-fluid" src='{{ asset('images/car-sample/car-placeholder.png') }}' alt="">
                                @endif
                            </div>
                            <div class="py-1">
                                <p class="font-w800 mb-1">หมายเลขตัวถัง: <span>{{$item->car?->chassis_no}}</span></p>
                                <p class="font-w800 mb-1">ทะเบียนรถ: <span>{{$item->car?->license_plate}}</span></p>
                                <p class="font-w800 mb-1">เข็มไมล์ล่าสุด: <span>{{$item->car?->mileage}}</span></p>
                                <p>{!! badge_render(__('cars.class_' . $item->car->status), __('cars.status_' . $item->car->status)) !!} </p>

                            </div>
                        </div>
                        <div class="ms-4 me-5 flex-grow-1 text-center text-md-start my-3 my-md-0">
                            <div class="row">
                                <div class="col-sm-4">
                                    <x-forms.hyper-link id="inspection_deliver"
                                                        :route="isset($item->contract_line) ? route('admin.contracts.show',['contract' => $item->contract_line?->contract_id]) : null"
                                                        :value="isset($item->contract_line) ? $item->contract_line?->worksheet_no : ''" :label="__('เลขที่สัญญา')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="car_user_{{$item->car_id}}" :value="$item->contract_line?->car_user" :label="__('ผู้ใช้รถ')"
                                                            :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.input-new-line id="car_tel_{{$item->car_id}}" :value="$item->contract_line?->tell" :label="__('เบอร์โทร')"
                                                            :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 mb-1">
                                    <x-forms.date-input id="pick_up_date_{{$item->car_id}}" :value="$item->contract_line?->pick_up_date" :label="__('วันที่รับรถ')"
                                                        :optionals="['placeholder' => __('lang.select_date')]"/>
                                </div>
                                <div class="col-sm-4 mb-1">
                                    <x-forms.date-input id="return_date_{{$item->car_id}}" :value="$item->contract_line?->return_date" :label="__('วันที่คืนรถ')"
                                                        :optionals="['placeholder' => __('lang.select_date')]"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <x-forms.hyper-link id="inspection_deliver"
                                                        :route="isset($item->inspection_job_deliver) ? route('admin.inspection-job-steps.show',['inspection_job_step' => $item->inspection_job_deliver->id]) : null"
                                                        :value="isset($item->inspection_job_deliver) ? $item->inspection_job_deliver->worksheet_no : ''" :label="__('ใบส่งตรวจรถ (ส่งมอบรถ)')"/>
                                </div>
                                <div class="col-sm-4">
                                    <x-forms.hyper-link id="inspection_receive"
                                                        :route="isset($item->inspection_job_receive) ? route('admin.inspection-job-steps.show',['inspection_job_step' => $item->inspection_job_receive->id]) : null"
                                                        :value="isset($item->inspection_job_receive) ? $item->inspection_job_receive->worksheet_no : ''" :label="__('ใบส่งตรวจรถ (รับรถเข้าคลัง)')"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="row push">
                    <div class="text-end">
                        <a class="btn btn-secondary" href="{{ route('admin.long-term-rentals.index') }}">{{ __('lang.back') }}</a>
                        <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('admin.long-term-rental-car-info-and-deliver.modals.form-create-contract-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')

@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rentals.car-info-and-deliver.store'),
])

@push('scripts')
    <script>
        // $('.form-control').prop('disabled' , true)
        const contract_start_date = '{{ $d->contract_start_date }}';
        const contract_end_date = '{{ $d->contract_end_date }}';
        $('#contract_start_date').prop('disabled' , contract_start_date);
        $('#contract_end_date').prop('disabled' , contract_end_date);


        const carList = @if(isset($lt_pr_line_car) && count($lt_pr_line_car) > 0) @json($lt_pr_line_car) @else [] @endif;
        const car_count = @if(isset($car_list) && count($car_list) > 0) @json(count($car_list)) @else 0 @endif;
        if (car_count == 0) {
            $('#date_deliver').prop('disabled' , true);
            $('#location_deliver').prop('disabled' , true);
            $('#user_receive').prop('disabled' , true);
            $('#phone_number').prop('disabled' , true);
        }
        carList.forEach((item) => {
            console.log(item.contract_line)
            if (item.contract_line !== null) {
                console.log('pass : ' + item.car_id)
                $('#car_user_' + item.car_id).prop('disabled' , true);
                $('#car_tel_' + item.car_id).prop('disabled' , true);
                $('#pick_up_date_' + item.car_id).prop('disabled' , true);
                $('#return_date_' + item.car_id).prop('disabled' , true);
            }
        });

        $('.btn-open-modal-create-contract').on('click' , function () {
            $('#modal-create-contract-contract').modal('show')
        });

        $('.btn-save-form-modal-create-contract').on('click' , function () {
            const car_list = $('#license_plate_and_chassis_no').val();
            const lt_rental_id = $('#lt_rental_id').val();
            const formData = [];
            if (!contract_start_date) {
                return warningAlert("กรุณาบันทึกวันที่เริ่มสัญญา");
            }
            if (!contract_end_date) {
                return warningAlert("กรุณาบันทึกวันที่สิ้นสุดสัญญา");
            }
            if (!car_list || car_list.length < 1) {
                return warningAlert("กรุณาเลือกรถที่ต้องการจัดทำสัญญา");
            }
            if (car_list && car_list.length > 0) {
                for (let i = 0; i < car_list.length; i++) {
                    id = car_list[i];
                    let car_user = $('#car_user_' + id).val();
                    let car_tel = $('#car_tel_' + id).val();
                    let pick_up_date = $('#pick_up_date_' + id).val();
                    let return_date = $('#return_date_' + id).val();

                    if (!car_user || !car_tel || !pick_up_date || !return_date) {
                        return warningAlert("{{ __('lang.required_field_inform') }}");
                    }

                    if (isNaN(car_tel)) {
                        return warningAlert("{{  __('contract.customer_mobile_number') . __('lang.only_number') }}");
                    }

                    if (car_user.length > 255) {
                        return warningAlert("{{  __('contract.car_user') . __('ต้องมีตัวอักษรไม่เกิน 255 ตัว') }}");
                    }

                    if (car_tel.length > 10) {
                        return warningAlert("{{  __('contract.customer_mobile_number') . __('ต้องมีตัวเลขไม่เกิน 10 ตัว') }}");
                    }

                    formData.push({
                        car_id: id,
                        car_user: car_user,
                        car_tel: car_tel,
                        pick_up_date: pick_up_date,
                        return_date: return_date,
                        contract_start_date: contract_start_date,
                        contract_end_date: contract_end_date,
                    })
                }
            }

            $.ajax({
                type: 'POST' ,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                } ,
                url: "{{ route('admin.long-term-rentals.car-info-and-deliver.create-contract') }}" ,
                data: {
                    lt_rental_id: lt_rental_id ,
                    carList: formData
                } ,
                success: function (response) {
                    $('#modal-create-contract-contract').modal('hide')
                    swal.close();
                    if (response.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}" ,
                            text: "{{ __('lang.store_success_message') }}" ,
                            icon: 'success' ,
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                            else {
                                window.location.reload();
                            }
                        });
                    }
                } ,
                error: function (data) {
                    $('#modal-create-contract-contract').modal('hide')
                    swal.close();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}" ,
                        html: 'กรุณาลองใหม่อีกครั้งภายหลัง' ,
                        icon: 'warning' ,
                        confirmButtonText: "{{ __('lang.ok') }}" ,
                    });
                }
            });

        });
    </script>
@endpush
