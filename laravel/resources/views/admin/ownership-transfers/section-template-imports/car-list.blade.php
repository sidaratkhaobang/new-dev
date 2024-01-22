<x-blocks.block :title="__('registers.car_total') . '@{{face_sheet_list.length}}' . ' คัน'">
    <div class="block-content" style="height:250px; overflow-y:auto;">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th> #</th>
                    <th> {{__('registers.leasing')}}</th>
                    <th>{{__('registers.car_class') }}</th>
                    <th>{{__('registers.cc') }}</th>
                    <th>{{__('registers.car_color') }}</th>
                    <th>{{ __('registers.chassis_no') }}</th>
                    <th>{{ __('registers.engine_no') }}</th>
                    <th>{{ __('registers.license_plate') }}</th>
                    <th>{{ __('ownership_transfers.receive_registration_book_date') }}</th>
                    <th>{{ __('ownership_transfers.car_ownership_date') }}</th>
                    <th>{{ __('ownership_transfers.return_registration_book_date') }}</th>
                    <th>{{ __('ownership_transfers.receipt_date') }}</th>
                    <th>{{ __('ownership_transfers.receipt_no') }}</th>
                    <th>{{ __('ownership_transfers.receipt_avance') }}</th>
                    <th>{{ __('ownership_transfers.service_fee') }}</th>
                    
                    {{-- <th></th> --}}
                </tr>
            </thead>
            <tbody v-if="face_sheet_list.length > 0">
              
                    <tr v-for="(item, index) in face_sheet_list">
                        <td>
                            @{{ index+1 }}
                        </td>
                        <td>
                            @{{ item.leasing_name }}
                        </td>
                        <td>
                            @{{ item.car_class }}
                        </td>
                        <td>
                            @{{ item.cc }}
                        </td>
                        <td>
                            @{{ item.car_color }}
                        </td>
                        <td>
                            @{{ item.chassis_no }}
                        </td>
                        <td>
                            @{{ item.engine_no }}
                        </td>
                        <td>
                            @{{ item.license_plate }}
                        </td>
                        <td>
                            @{{ formatDate(item.receive_registration_book_date) }}
                        </td>
                        <td>
                            @{{ formatDate(item.car_ownership_date) }}
                        </td>
                        <td>
                            @{{ formatDate(item.return_registration_book_date) }}
                        </td>
                        <td>
                            @{{ formatDate(item.receipt_date) }}
                        </td>
                        <td>
                            @{{ item.receipt_no }}
                        </td>
                        <td>
                            @{{ item.receipt_fee }}
                        </td>
                        <td>
                            @{{ item.service_fee }}
                        </td>
                        {{-- <td>
                            <a class="btn btn-light" v-on:click="removeCar(index)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        </td> --}}
                        <input type="hidden" v-bind:name="'register['+ index+ '][id]'" v-bind:value="item.id">
                        {{-- <input type="hidden" v-bind:name="'register['+ index+ '][id]'" v-bind:value="item.id"> --}}
                        <input type="hidden" v-bind:name="'register['+ index+ '][leasing_name]'" v-bind:value="item.leasing_name">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_class]'" v-bind:value="item.car_class">
                        <input type="hidden" v-bind:name="'register['+ index+ '][cc]'" v-bind:value="item.cc">
                        <input type="hidden" v-bind:name="'register['+ index+ '][engine_no]'" v-bind:value="item.engine_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][chassis_no]'" v-bind:value="item.chassis_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_class]'" v-bind:value="item.car_class">
                        {{-- <input type="hidden" v-bind:name="'register['+ index+ '][cc]'" v-bind:value="item.cc"> --}}
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_color]'" v-bind:value="item.car_color">
                        <input type="hidden" v-bind:name="'register['+ index+ '][license_plate]'" v-bind:value="item.license_plate">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receive_registration_book_date]'" v-bind:value="item.receive_registration_book_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_ownership_date]'" v-bind:value="item.car_ownership_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][return_registration_book_date]'" v-bind:value="item.return_registration_book_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receipt_date]'" v-bind:value="item.receipt_date">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receipt_no]'" v-bind:value="item.receipt_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][receipt_fee]'" v-bind:value="item.receipt_fee">
                        <input type="hidden" v-bind:name="'register['+ index+ '][service_fee]'" v-bind:value="item.service_fee">
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
</x-blocks.block>
