<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.car_total') . '@{{face_sheet_list.length}}' . ' คัน',
        'block_option_id' => '_list',
        // 'is_toggle' => true,
    ])
    <div class="block-content" style="height:250px; overflow-y:auto;">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th> #</th>
                    <th> {{__('registers.lot_no')}}</th>
                    <th>{{__('registers.car_class') }}</th>
                    <th>{{ __('registers.chassis_no') }}</th>
                    <th>{{ __('registers.engine_no') }}</th>
                    <th>{{ __('registers.car_characteristic') }}</th>
                    <th>{{ __('registers.car_characteristic_transport') }}</th>
                    <th>{{ __('registers.color_registered') }}</th>
                    <th>{{ __('registers.receive_information_date') }}</th>
                    <th>{{ __('registers.license_plate') }}</th>
                    
                    {{-- <th></th> --}}
                </tr>
            </thead>
            <tbody v-if="face_sheet_list.length > 0">
              
                    <tr v-for="(item, index) in face_sheet_list">
                        <td>
                            @{{ index+1 }}
                        </td>
                        <td>
                            @{{ item.lot_no }}
                        </td>
                        <td>
                            @{{ item.car_class }}
                        </td>
                        <td>
                            @{{ item.chassis_no }}
                        </td>
                        <td>
                            @{{ item.engine_no }}
                        </td>
                        <td>
                            @{{ item.car_characteristic }}
                        </td>
                        <td>
                            @{{ item.car_characteristic_transport }}
                        </td>
                        <td>
                            @{{ item.color_registered }}
                        </td>
                        <td>
                            @{{ item.receive_information_date }}
                        </td>
                        <td>
                            @{{ item.license_plate }}
                        </td>
                        {{-- <td>
                            <a class="btn btn-light" v-on:click="removeCar(index)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        </td> --}}
                        <input type="hidden" v-bind:name="'register['+ index+ '][id]'" v-bind:value="item.id">
                        {{-- <input type="hidden" v-bind:name="'register['+ index+ '][id]'" v-bind:value="item.id"> --}}
                        <input type="hidden" v-bind:name="'register['+ index+ '][sale]'" v-bind:value="item.sale">
                        <input type="hidden" v-bind:name="'register['+ index+ '][tax]'" v-bind:value="item.tax">
                        <input type="hidden" v-bind:name="'register['+ index+ '][lot_no]'" v-bind:value="item.lot_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][engine_no]'" v-bind:value="item.engine_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][chassis_no]'" v-bind:value="item.chassis_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_class]'" v-bind:value="item.car_class">
                        <input type="hidden" v-bind:name="'register['+ index+ '][cc]'" v-bind:value="item.cc">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_color]'" v-bind:value="item.car_color">
                        <input type="hidden" v-bind:name="'register['+ index+ '][customer]'" v-bind:value="item.customer">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_characteristic]'" v-bind:value="item.car_characteristic">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_characteristic_transport]'" v-bind:value="item.car_characteristic_transport">
                        <input type="hidden" v-bind:name="'register['+ index+ '][color_registered]'" v-bind:value="item.color_registered">
                        <input type="hidden" v-bind:name="'register['+ index+ '][registered_date]'" v-bind:value="item.registered_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receive_information_date]'" v-bind:value="item.receive_information_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][license_plate]'" v-bind:value="item.license_plate">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_tax_exp_date]'" v-bind:value="item.car_tax_exp_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receipt_date]'" v-bind:value="item.receipt_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receipt_no]'" v-bind:value="item.receipt_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][tax]'" v-bind:value="item.tax">
                        <input type="hidden" v-bind:name="'register['+ index+ '][service_fee]'" v-bind:value="item.service_fee">
                        <input type="hidden" v-bind:name="'register['+ index+ '][link]'" v-bind:value="item.link">
                        <input type="hidden" v-bind:name="'register['+ index+ '][is_registration_book]'" v-bind:value="item.is_registration_book">
                        <input type="hidden" v-bind:name="'register['+ index+ '][is_license_plate]'" v-bind:value="item.is_license_plate">
                        <input type="hidden" v-bind:name="'register['+ index+ '][is_tax_sign]'" v-bind:value="item.is_tax_sign">




                    </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="6">"
                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
