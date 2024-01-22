<x-blocks.block :title="__('registers.car_total') . '@{{face_sheet_list.length}}' . ' คัน'">
    <div class="block-content" style="height:250px; overflow-y:auto;">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th>{{__('registers.leasing') }}</th>
                    <th> {{__('ownership_transfers.must_last_payment_date')}}</th>     
                    <th>{{__('registers.license_plate') }}</th>
                    <th>{{__('registers.car_class') }}</th>
                    <th>{{ __('registers.chassis_no') }}</th>
                    <th>{{ __('registers.engine_no') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="face_sheet_list.length > 0">
                    <tr v-for="(item, index) in face_sheet_list">
                        <td>
                            @{{ index+1 }}
                        </td>
                        <td>
                            @{{ item.creditor_name }}
                        </td>
                        <td>
                            @{{ formatDate(item.actual_last_payment_date) }}
                        </td>
                        <td>
                            @{{ item.license_plate }}
                        </td>
                        <td>
                            @{{ item.full_name }}
                        </td>
                        <td>
                            @{{ item.chassis_no }}
                        </td>
                        <td>
                            @{{ item.engine_no }}
                        </td>
                        <td>
                            <a class="btn btn-light" v-on:click="removeCar(index)"><i class="fa-solid fa-trash-can"
                                    style="color:red"></i></a>
                        </td>
                        <input type="hidden" v-bind:name="'register['+ index+ '][car_id]'" v-bind:value="item.id">
                        <input type="hidden" v-bind:name="'register['+ index+ '][id]'" v-bind:value="item.id">
                        <input type="hidden" v-bind:name="'register['+ index+ '][chassis_no]'" v-bind:value="item.chassis_no">
                        <input type="hidden" v-bind:name="'register['+ index+ '][engine_no]'" v-bind:value="item.engine_no">

                    </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="8">"
                        {{ __('lang.no_list') . __('purchase_requisitions.data_car_table') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-blocks.block>
