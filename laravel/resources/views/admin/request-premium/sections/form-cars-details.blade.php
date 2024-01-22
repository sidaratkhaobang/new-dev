@foreach($car_list as $key_car_list => $value_car_list)
    @section('block_options_'.$key_car_list)
        <div class="d-flex">
            <button type="button" class="btn-accessory ms-1 me-1" data-bs-toggle="modal"
                    data-bs-target="#exampleModal" onclick="ModalAccessory('{{$value_car_list?->id}}')">
                <i class="icon-menu-tools"></i>
                ข้อมูลอุปกรณ์เสริม
            </button>
            <button type="button" class="btn-nav-accordion collapsed ms-1 me-1"
                    style="background: none;border: none;" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample{{$key_car_list}}"
                    aria-expanded="false" aria-controls="collapseExample">
                <i class="si si-arrow-up fa-chevron-up"></i>
            </button>
        </div>
    @endsection
    <input type="hidden" name="car[data][{{$key_car_list}}][id]"
           value="{{$value_car_list?->getRequestCarClassLine?->id}}">
    <input type="hidden" name="car[data][{{$key_car_list}}][lt_rental_line_id]" value="{{$value_car_list?->id}}">
    <div class="block {{ __('block.styles') }} block-car">
        <div class="block-content box-padding-bottom">
            @include('admin.components.block-header',[
     'text' =>   __('request_premium.car_detail')   ,
    'block_icon_class' => 'icon-document',
    'block_option_id' => '_'.$key_car_list,
])


            <div class="block-header overflow-auto">
                <div class="row car-border overflow-auto" style="border-radius: 6px;">
                    <div class="col-sm-3 border-right box-car-pic ps-0 pe-0">
                        @php
                            $car = $value_car_list;
                        @endphp
                        @include('admin.cmi-components.car-section')
                    </div>
                    <div class="col-sm-9">
                        <table class="table table-responsive h-100">
                            <tbody>
                            <tr class="car-table-header">
                                <th style="width: 16.66%">
                                </th>
                                <th style="width: 16.66%">
                                </th>
                                <th style="width: 16.66%">
                                </th>
                                <th style="width: 16.66%">
                                </th>
                                <th style="width: 16.66%">
                                </th>
                                <th style="width: 16.66%">
                                </th>
                            </tr>
                            <tr>
                                <td colspan="1">
                                    <x-forms.label id="recipient_staff_name"
                                                   :value="number_format($value_car_list?->carClass?->engine_size).' CC'"
                                                   :label="__('request_premium.cc')"/>
                                </td>
                                <td colspan="1">
                                    <x-forms.label id="recipient_staff_name"
                                                   :value="number_format($value_car_list?->amount).' คัน'"
                                                   :label="__('request_premium.total')"/>
                                </td>
                                <td colspan="1">
                                    <x-forms.label id="recipient_staff_name"
                                                   :value="number_format($value_car_list?->showroom_price).' บาท'"
                                                   :label="__('request_premium.car_buy_price')"/>
                                </td>
                                <td colspan="1">
                                    <x-forms.label id="recipient_staff_name"
                                                   :value="number_format($value_car_list?->accessories_price).' บาท'"
                                                   :label="__('request_premium.accessory_total_price')"/>
                                </td>
                                <td colspan="2">
                                    <x-forms.label id="recipient_staff_name" :value="'รถนั่งโดยสาร 5 ปี'"
                                                   :label="__('request_premium.registration_type')"/>
                                </td>
                            </tr>
                            <tr id="car-table-input">
                                <td colspan="2">
                                    <x-forms.input-new-line id="car[data][{{$key_car_list}}][sum_insured_car]"
                                                            :value="$value_car_list?->getRequestCarClassLine?->sum_insured_car"
                                                            :name="'car_insurance_premium_wow'"
                                                            :label="__('request_premium.car_insurance_premium')"
                                                            :optionals="[
                                                            'required' => false,
                                                            'input_class' => 'number-format com-sm-4 sum_insured_car'
                                                            ]"/>
                                </td>
                                <td colspan="2">
                                    <x-forms.input-new-line id="car[data][{{$key_car_list}}][sum_insured_accessories]"
                                                            :value="$value_car_list?->getRequestCarClassLine?->sum_insured_accessories"
                                                            :label="__('request_premium.car_insurance_accessory')"
                                                            :optionals="[
                                                            'required' => false,
                                                            'input_class' => 'number-format com-sm-4 sum_insured_accessories'
                                                            ]"/>
                                </td>
                                <td colspan="2">
                                    <x-forms.input-new-line id="car[data][{{$key_car_list}}][sum_insured]"
                                                            :value="$value_car_list?->getRequestCarClassLine?->sum_insured"
                                                            :label="__('request_premium.car_insurance_aggregate')"
                                                            :optionals="[
                                                            'required' => false,
                                                            'input_class' => 'number-format com-sm-4 sum_insured']"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="collapse" id="collapseExample{{$key_car_list}}">
                <div class="block-header mb-4">
                    <div class="row w-100">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="car[data][{{$key_car_list}}][request_premium_date]"
                                                    :value="$value_car_list?->created_date"
                                                    :label="__('request_premium.request_premium_date')"
                                                    :type="'date'"
                                                    :optionals="['required' => false,
                                                    'input_class' => 'request_premium_date'
                                                    ]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car[data][{{$key_car_list}}][insurer_id]"
                                                   :value="$value_car_list?->getRequestCarClassLine?->insurer_id"
                                                   :list="$insurance_companies"
                                                   :label="__('request_premium.insurance_companies')"
                                                   :optionals="['required' => false,
                                                    'input_class' => 'insurer_id'
                                                    ]"/>
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="car[data][{{$key_car_list}}][insurance_package_id]"
                                                   :value="$value_car_list?->getRequestCarClassLine?->insurance_package_id"
                                                   :list="$insurance_package"
                                                   :label="__('request_premium.insurance_package')"
                                                   :optionals="['required' => false]"/>
                        </div>
                    </div>
                </div>
                @include('admin.request-premium.sections.form-cars-premium-details')
                @include('admin.request-premium.sections.form-cars-coverage-details')
            </div>
        </div>
    </div>
@endforeach




