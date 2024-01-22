<div class="block {{ __('block.styles') }}">
    <div class="block-content box-padding-bottom block-car-coverage">
        <div class="mb-3 block-header">
            <h4>
                <i class="fa fa-file-lines"></i> {{ __('request_premium.coverage_information') }}</h4>
            <button  class="btn-apply-all btn-coverage-apply-all" type="button">
               {{__('request_premium.apply_all')}}
            </button>
        </div>
        {{--       ความรับผิดต่อบุคคลภายนอก     --}}
        <div class="block-header overflow-auto">
            <table class="table table-bordered table-striped">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.liability')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td rowspan="2" class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>1.{{__('request_premium.bodily_harm')}}</span>
                    </td>
                    <td style="width: 33.33%;">
                        <input name="car[data][{{$key_car_list}}][tpbi_person]" class="w-100 form-control input-insurance-life-person number-format" value="{{$value_car_list?->getRequestCarClassLine?->tpbi_person}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td style="width: 33.33%;" class="text-center align-middle">{{__('request_premium.bath_person')}}</td>
                </tr>
                <tr class="table-background-gray">
                    <td>
                        <input name="car[data][{{$key_car_list}}][tpbi_aggregate]" class="w-100 form-control input-insurance-life-total number-format" value="{{$value_car_list?->getRequestCarClassLine?->tpbi_aggregate}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.bath_total')}}</td>
                </tr>
                <tr>
                    <td class="text-start table-background-white"
                        style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>2.{{__('request_premium.property_damage')}}</span>
                    </td>
                    <td class="table-background-white">
                        <input name="car[data][{{$key_car_list}}][tppd_aggregate]" class="w-100 form-control input-insurance-property number-format" value="{{$value_car_list?->getRequestCarClassLine?->tppd_aggregate}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle table-background-white">{{__('request_premium.bath_person')}}</td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>3.{{__('request_premium.first_party_damage')}}</span>
                    </td>
                    <td>
                        <input name="car[data][{{$key_car_list}}][deductible]" class="w-100 form-control input-insurance-first number-format" value="{{$value_car_list?->getRequestCarClassLine?->deductible}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.bath_total')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--       ความรับผิดต่อรถยนต์     --}}
        <div class="block-header overflow-auto">
            <table class="table table-bordered table-striped">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.liability_towards_third_parties')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>1.{{__('request_premium.car_damage')}}</span>
                    </td>
                    <td style="width: 33.33%;">
                        <input name="car[data][{{$key_car_list}}][own_damage]" class="w-100 form-control input-insurance-car-damage number-format" value="{{$value_car_list?->getRequestCarClassLine?->own_damage}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td style="width: 33.33%;" class="text-center align-middle">{{__('request_premium.insurance_capital')}}</td>
                </tr>
                <tr>
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>2.{{__('request_premium.car_accident')}}</span>
                    </td>
                    <td>
                        <input name="car[data][{{$key_car_list}}][fire_and_theft]" class="w-100 form-control input-insurance-car-accident number-format" value="{{$value_car_list?->getRequestCarClassLine?->fire_and_theft}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.insurance_capital')}}</td>
                </tr>
                <tr>
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>3.{{__('request_premium.insurance_car_body')}}</span>
                    </td>
                    <td>
                        <input name="car[data][{{$key_car_list}}][deductible_car]" class="w-100 form-control input-insurance-car-body number-format" value="{{$value_car_list?->getRequestCarClassLine?->deductible_car}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.per_total')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--       อุบัติเหตุส่วนบุคคล     --}}
        <div class="block-header overflow-auto">

            <table class="table table-bordered table-striped">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.liability_to_third_parties')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td rowspan="2" class="text-start" style="width: 16.66%;vertical-align : middle;text-align:center;">
                        <span>1.{{__('request_premium.loss_of_life_and_bodily_injury')}}</span>
                    </td>
                    <td class="text-center align-middle" style="width: 16.66%;">{{__('request_premium.drive')}}</td>
                    <td style="width: 33.33%;">
                        <input name="car[data][{{$key_car_list}}][pa_driver]" class="w-100 form-control input-insurance-driver number-format" value="{{$value_car_list?->getRequestCarClassLine?->pa_driver}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle" style="width: 33.33%;"></td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-center align-middle" style="width: 16.66%;">{{__('request_premium.passenger')}}</td>
                    <td>
                        <input name="car[data][{{$key_car_list}}][pa_passenger]" class="w-100 form-control input-insurance-passenger number-format" value="{{$value_car_list?->getRequestCarClassLine?->pa_passenger}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle"></td>
                </tr>
                <tr>
                    <td class="text-start table-background-white" colspan="2"
                        style="vertical-align : middle;text-align:center;">
                        <span>2.{{__('request_premium.medical_expenses')}}</span>
                    </td>
                    <td class="table-background-white">
                        <input name="car[data][{{$key_car_list}}][medical_exp]" class="w-100 form-control input-insurance-healthcare number-format" value="{{$value_car_list?->getRequestCarClassLine?->medical_exp}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle table-background-white"></td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-start" colspan="2" style="vertical-align : middle;text-align:center;">
                        <span>3.{{__('request_premium.driver_insurance')}}}</span>
                    </td>
                    <td>
                        <input name="car[data][{{$key_car_list}}][bailbond]" class="w-100 form-control input-insurance-bail number-format" value="{{$value_car_list?->getRequestCarClassLine?->bailbond}}" placeholder="กรุณาใส่ข้อมูล">
                    </td>
                    <td class="text-center align-middle"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
