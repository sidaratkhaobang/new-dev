<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('cmi_cars.coverage_info')
    ])
    {{-- <div class="block-content"> --}}
        {{--       ความรับผิดต่อบุคคลภายนอก     --}}
        <div class="block-content overflow-auto">
            <table class="table table-bordered ">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.liability')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr class="table-background-gray">
                    <td rowspan="2" class="text-start align-middle" style="width: 33.33%;">
                        <span>1. {{__('request_premium.bodily_harm')}}</span>
                    </td>
                    <td style="width: 33.33%;">
                        <x-forms.input-new-line id="tpbi_person" :value="$d->tpbi_person" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td style="width: 33.33%;" class="text-center align-middle">{{__('request_premium.bath_person')}}</td>
                </tr>
                <tr class="table-background-gray">
                    <td>
                        <x-forms.input-new-line id="tpbi_aggregate" :value="$d->tpbi_aggregate" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.bath_total')}}</td>
                </tr>
                <tr>
                    <td class="text-start align-middle"
                        style="width: 33.33%;">
                        <span>2. {{__('request_premium.property_damage')}}</span>
                    </td>
                    <td class="">
                        <x-forms.input-new-line id="tppd_aggregate" :value="$d->tppd_aggregate" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle ">{{__('request_premium.bath_person')}}</td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>3. {{__('request_premium.first_party_damage')}}</span>
                    </td>
                    <td>
                        <x-forms.input-new-line id="deductible" :value="$d->deductible" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.bath_total')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--       ความรับผิดต่อรถยนต์     --}}
        <div class="block-header overflow-auto">
            <table class="table table-bordered ">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.car_damage')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr class="table-background-gray">
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>1. {{__('request_premium.car_damage')}}</span>
                    </td>
                    <td style="width: 33.33%;">
                        <x-forms.input-new-line id="own_damage" :value="$d->own_damage" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td style="width: 33.33%;" class="text-center align-middle">{{__('request_premium.insurance_capital')}}</td>
                </tr>
                <tr>
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>2. {{__('request_premium.car_accident')}}</span>
                    </td>
                    <td>
                        <x-forms.input-new-line id="fire_and_theft" :value="$d->fire_and_theft" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.insurance_capital')}}</td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-start" style="width: 33.33%;vertical-align : middle;text-align:center;">
                        <span>3. {{__('request_premium.insurance_car_body')}}</span>
                    </td>
                    <td>
                        <x-forms.input-new-line id="deductible_car" :value="$d->deductible_car" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle">{{__('request_premium.per_total')}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--       อุบัติเหตุส่วนบุคคล     --}}
        <div class="block-header overflow-auto">

            <table class="table table-bordered ">
                <thead class="bg-body-dark" style="border:1px solid #CBD4E1 ">
                <tr>
                    <th colspan="12" class="text-center" style="width: 100px">
                        {{__('request_premium.personal_damage')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr class="table-background-gray">
                    <td rowspan="2" class="text-start align-middle" style="width: 16.66%;">
                        <span>1. {{__('request_premium.loss_of_life_and_bodily_injury')}}</span>
                    </td>
                    <td class="text-center align-middle" style="width: 16.66%;">{{__('request_premium.drive')}}</td>
                    <td style="width: 33.33%;">
                        <x-forms.input-new-line id="pa_driver" :value="$d->pa_driver" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle" style="width: 33.33%;"></td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-center align-middle" style="width: 16.66%;">{{__('request_premium.passenger')}}</td>
                    <td>
                        <x-forms.input-new-line id="pa_passenger" :value="$d->pa_passenger" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle"></td>
                </tr>
                <tr>
                    <td class="text-start " colspan="2"
                        style="vertical-align : middle;text-align:center;">
                        <span>2. {{__('request_premium.medical_expenses')}}</span>
                    </td>
                    <td class="">
                        <x-forms.input-new-line id="medical_exp" :value="$d->medical_exp" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle "></td>
                </tr>
                <tr class="table-background-gray">
                    <td class="text-start align-middle" colspan="2">
                        <span>3. {{__('request_premium.driver_insurance')}}</span>
                    </td>
                    <td class="align-middle" >
                        <x-forms.input-new-line id="bail_bond" :value="$d->bail_bond" :label="null" 
                            :optionals="['input_class' => 'number-format']" />
                    </td>
                    <td class="text-center align-middle"></td>
                </tr>
                </tbody>
            </table>
        </div>
    {{-- </div> --}}
</div>
