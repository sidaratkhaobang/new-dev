<div class="modal fade" id="avance-modal" aria-labelledby="avance-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avance-modal-label"> <i class="icon-menu-money"></i>ข้อมูลบันทึกการเบิกเงิน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="save-form-modal">
                    {{-- <h4 class="fw-light text-gray-darker">{{ __('install_equipments.add_accessory') }}</h4>
                <hr> --}}
                    <div class="block-content">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                                <tr>
                                    <th>#</th>
                                    <th> {{ __('ownership_transfers.leasing_th') }}</th>
                                    <th>{{ __('registers.car_class') }}</th>
                                    <th>{{ __('ownership_transfers.engine_size') }}</th>
                                    <th>{{ __('registers.chassis_no') }}</th>
                                    <th>{{ __('registers.engine_no') }}</th>
                                    <th>{{ __('registers.memo') }}</th>
                                    <th>{{ __('registers.receipt_avance') }}</th>
                                    <th>{{ __('registers.operation_fee_avance') }}</th>
                                    <th>{{ __('registers.total') }}</th>
                                </tr>
                            </thead>
                            <tbody v-if="face_sheet_list.length > 0">
                                {{-- @{{ face_sheet_list.length }} --}}
                                <tr v-for="(item, index) in face_sheet_list">
                                    <td>
                                        @{{ index + 1 }}
                                    </td>
                                    <td>
                                        @{{ item.creditor_name }}
                                    </td>
                                    <td>
                                        @{{ item.full_name }}
                                    </td>
                                    <td>
                                        @{{ item.engine_size }}
                                    </td>
                                    <td>
                                        @{{ item.chassis_no }}
                                    </td>
                                    <td>
                                        @{{ item.engine_no }}
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" :id="'memo'.item"
                                            v-bind:name="'register['+ index+ '][memo_no]'" v-model="item.memo_no">
                                    </td>
                                    <td>
                                        <input class="form-control number-format" type="text"
                                            :id="'receipt_avance'.item"
                                            v-bind:name="'register['+ index+ '][receipt_avance]'"
                                            v-model="item.receipt_avance" @change="sumTotal(index)">
                                    </td>
                                    <td>
                                        <input class="form-control number-format" type="text"
                                            :id="'operation_fee_avance'.item"
                                            v-bind:name="'register['+ index+ '][operation_fee_avance]'"
                                            v-model="item.operation_fee_avance" @change="sumTotal(index)">
                                    </td>
                                    <td>
                                        <input class="form-control number-format" type="text" :id="'total'.item"
                                            v-bind:name="'register['+ index+ '][total]'" v-model="item.total" disabled>
                                    </td>
                                    {{-- <td>
                                        <a class="btn btn-light" v-on:click="removeCar(index)"><i class="fa-solid fa-trash-can"
                                                style="color:red"></i></a>
                                    </td> --}}
                                    <input type="hidden" :id="'memo'.item"
                                        v-bind:name="'register['+ index+ '][memo_no]'" v-model="item.memo_no">
                                    <input type="hidden" :id="'operation_fee_avance'.item"
                                        v-bind:name="'register['+ index+ '][operation_fee_avance]'"
                                        v-model="item.operation_fee_avance">
                                    <input type="hidden" :id="'receipt_avance'.item"
                                        v-bind:name="'register['+ index+ '][receipt_avance]'"
                                        v-model="item.receipt_avance">
                                    <input type="hidden" :id="'total'.item"
                                        v-bind:name="'register['+ index+ '][total]'" v-model="item.total">
                                    <input type="hidden" :id="'id'.item"
                                        v-bind:name="'register['+ index+ '][id]'" v-model="item.id">

                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="9">"
                                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom-size"
                    onclick="BackToModalAvanceSelectCar()">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary btn-save-form-avance-modal"><i class="icon-printer"></i>
                    {{ __('registers.select_car_face_sheet') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>
