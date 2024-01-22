@include('admin.contracts.sections.btn-tap-group')
<div class="block block-rounded">
    @include('admin.components.block-header',[
        'text' =>   __('lang.search')    ,
        'block_icon_class' => 'icon-search',
        'is_toggle' => true
    ])
    <div class="block-content">
        <div class="justify-content-between">
            <form action="" method="GET" id="form-search">
                <div class="mb-4 form-group row">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_license_plate" :value="null" :list="$license_plate_list" :label="__('ทะเบียนรถ')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_chassis_no" :value="null" :list="$chassis_no_list" :label="__('หมายเลขตัวถัง')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_engine_no" :value="null" :list="$engine_no_list" :label="__('เลขเครื่องยนต์')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_class_id" :value="null" :list="$car_class_list" :label="__('รุ่น')"/>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="button" onclick="resetForm()" class="btn btn-outline-secondary btn-clear-search btn-custom-size"><i class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}
                        </button>
                        <button type="button" onclick="search_car()" class="btn btn-primary btn-custom-size"><i class="fa fa-magnifying-glass"></i> {{ __('lang.search') }}</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@foreach($data->contractline as $_index => $item)
    @php
        $car_type = $item->car?->license_plate;
    @endphp
    <div class="block block-rounded car-search" data-car-id="{{ $item->car?->id ?? '' }}">
        @section('block_options_sub' . $_index)
            <button class="btn btn-sm btn-primary px-3 me-1 my-1">
                <i class="fa fa-arrow-rotate-left me-1"></i> ประวัติค่าปรับ
            </button>
            <button class="btn btn-sm btn-primary px-3 me-1 my-1 btn-show-modal-history-accident" type="button">
                <i class="fa fa-arrow-rotate-left me-1"></i> ประวัติอุบัติเหตุ
            </button>
            <button class="btn btn-sm btn-primary px-3 me-1 my-1 btn-show-modal-history-maintenance" type="button">
                <i class="fa fa-arrow-rotate-left me-1"></i> ประวัติซ่อมบำรุง
            </button>
        @endsection
        @include('admin.components.block-header',[
            'text' =>   __('ข้อมูลรถ #') .$_index + 1,
            'block_icon_class' => 'icon-document',
            'is_toggle' => true,
            'block_option_id' => '_sub' . $_index,
        ])
        <div class="block-content">
            <div>
                <div class="py-4 bg-body-extra-light d-flex flex-column flex-md-row ">
                    <div class="car-border">
                        <div class="py-1 mb-4">
                            <p id="{{ $car_type }}-class-name" class="fs-6 fw-bolder mb-1"></p>
                        </div>
                        <div class="py-1 car-section mb-4">
                            @if (isset($car->image) && isset($car->image['url']))
                                <img id="{{ $car_type }}-img" class="img-fluid" src='{{ $car->image['url'] }}' alt="">
                            @else
                                <img id="{{ $car_type }}-img" class="img-fluid" src='{{ asset('images/car-sample/car-placeholder.png') }}' alt="">
                            @endif
                        </div>
                        <div class="py-1">
                            <p class="font-w800 mb-1">หมายเลขตัวถัง: <span id="{{ $car_type }}-chassis-no">{{ $item->car->chassis_no ?? '' }}</span></p>
                            <p class="font-w800 mb-1">ทะเบียนรถ: <span id="{{ $car_type }}-license-plate">{{ $item->car->license_plate ?? '' }}</span></p>
                        </div>
                    </div>
                    <div class="ms-4 me-5 flex-grow-1 text-center text-md-start my-3 my-md-0">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <x-forms.label id="recipient_staff_name" :value="isset($item->inspection_job_deliver) ? $item->inspection_job_deliver->recipient_staff_name : null" :label="__('ผู้รับรถ')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="$item->car_user" :label="__('ผู้ใช้รถ')"/>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="$item->cmi?->policy_reference_cmi" :label="__('เลขกรมธรรม์ พรบ.')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" 
                                :value="$item->cmi?->term_start_date ? get_thai_date_format($item->cmi->term_start_date, 'd/m/Y H:i') : null" :label="__('วันเริ่มคุ้มครอง พรบ.')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" 
                                :value="$item->cmi?->term_end_date ? get_thai_date_format($item->cmi->term_end_date, 'd/m/Y H:i') : null" :label="__('วันที่สิ้นสุดความคุ้มครอง พรบ.')"/>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="$item->vmi?->policy_reference_vmi" :label="__('กรมธรรม์ประกันภัยเลขที่')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" 
                                :value="$item->vmi?->term_start_date ? get_thai_date_format($item->vmi->term_start_date, 'd/m/Y H:i') : null" :label="__('วันเริ่มคุ้มครอง')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" 
                                :value="$item->vmi?->term_end_date ? get_thai_date_format($item->vmi->term_end_date, 'd/m/Y H:i') : null" :label="__('วันที่สิ้นสุดความคุ้มครอง')"/>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="$item->vmi?->insurer?->insurance_name_th" :label="__('บริษัทประกัน')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="$item->vmi?->insurer?->insurance_tel" :label="__('เบอร์โทร')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="number_format($item->purchase_option_preice, 2, '.', ',')" :label="__('ค่าซาก')"/>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <x-forms.label id="" :value="null" :label="__('ใบส่งมอบรถ')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.hyper-link id="inspection_deliver" :route="isset($item->inspection_job_deliver) ? route('admin.inspection-job-steps.show',['inspection_job_step' => $item->inspection_job_deliver->id]) : '#'"
                                    :value="isset($item->inspection_job_deliver) ? $item->inspection_job_deliver->worksheet_no : ''" :label="__('ใบส่งตรวจรถ (ส่งมอบรถ)')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.hyper-link id="inspection_receive" :route="isset($item->inspection_job_receive) ? route('admin.inspection-job-steps.show',['inspection_job_step' => $item->inspection_job_receive->id]) : '#'"
                                    :value="isset($item->inspection_job_receive) ? $item->inspection_job_receive->worksheet_no : ''" :label="__('ใบส่งตรวจรถ (รับรถเข้าคลัง)')"/>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 mb-1">
                                <x-forms.date-input id="contract_cars[{{ $item->id }}][pick_up_date]"
                                :value="$item->pick_up_date" :label="__('วันที่รับรถ')" :optionals="['placeholder' => __('lang.select_date')]"/>
                            </div>
                            <div class="col-sm-4 mb-1">
                                <x-forms.date-input id="contract_cars[{{ $item->id }}][expected_return_date]" 
                                    :value="$item->expected_return_date" :label="__('วันที่คาดว่าจะคืนรถ')" :optionals="['placeholder' => __('lang.select_date')]"/>
                            </div>
                            <div class="col-sm-4 mb-1">
                                <x-forms.date-input id="contract_cars[{{ $item->id }}][return_date]" 
                                    name="{{ $item->car?->id }}_return_date" :value="$item->return_date" :label="__('วันที่คืนรถจริง')" :optionals="['placeholder' => __('lang.select_date')]"/>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-sm-4 align-self-end {{ $item->return_date > $data->contract_end_date ? '' : 'hide' }}" id="{{ $item->id }}_is_fine" >
                                <x-forms.radio-inline id="contract_cars[{{ $item->id }}][is_fine]" :value="$item->is_fine" :list="$have_fine_list" :label="__('!เกินกำหนดสัญญา มีค่าปรับหรือไม่')"
                                    :optionals="['label_class' => 'text-danger']"/>
                            </div>
                            <div class="col-sm-4 {{ $item->is_fine ? '' : 'hide' }}" id="{{ $item->id }}_percent_fine">
                                <x-forms.input-new-line id="contract_cars[{{ $item->id }}][percent_fine]" :value="$item->percent_fine" :label="__('จำนวน % (ตามค่าเช่า)')"
                                    :optionals="['input_class' => 'number-format']" />
                            </div>
                            <div class="col-sm-4 {{ $item->is_fine ? '' : 'hide' }}" id="{{ $item->id }}_fine">
                                <x-forms.input-new-line id="contract_cars[{{ $item->id }}][fine]" :optionals="['input_class' => 'number-format']" :value="$item->fine" :label="__('ค่าปรับ')"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
{{-- 
<div class="block block-rounded">
    <div class="block-content"> --}}
        @include('admin.contracts.sections.submit')
    {{-- </div>
</div> --}}

@push('scripts')
    <script>

        function resetForm() {
            $('#car_license_plate').val(null).trigger('change');
            $('#car_engine_no').val(null).trigger('change');
            $('#car_chassis_no').val(null).trigger('change');
            $('#car_class_id').val(null).trigger('change');

            $('.car-search').show();
        }

        function search_car() {
            const car_license_plate = $('#car_license_plate').val();
            const car_engine_no = $('#car_engine_no').val();
            const car_chassis_no = $('#car_chassis_no').val();
            const car_class_id = $('#car_class_id').val();

            console.log(car_license_plate)
            console.log(car_engine_no)
            console.log(car_chassis_no)
            console.log(car_class_id)

            if (car_license_plate === '' && car_engine_no === '' && car_chassis_no === '' && car_class_id === '') {
                $('.car-search').show();
            }
            else {
                $('.car-search').hide();
            }

            if (car_license_plate !== '') {
                $('div[data-car-id=' + car_license_plate + ']').show();
            }
            if (car_engine_no !== '') {
                $('div[data-car-id=' + car_engine_no + ']').show();
            }
            if (car_chassis_no !== '') {
                $('div[data-car-id=' + car_chassis_no + ']').show();
            }
            if (car_class_id !== '') {
                $('div[data-car-id=' + car_class_id + ']').show();
            }
        }
    </script>
@endpush
