<div class="modal fade" id="modal-lot-detail" aria-labelledby="modal-edit-purchase" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">รายการจัดทำLot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="closeModalLotDetail()"></button>
            </div>
            <div class="modal-body pb-0">
                <div class="block {{ __('block.styles') }} border-0 shadow-none pb-0">
                    <div class="block-content pb-0">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <x-forms.input-new-line id="_lot_no" :value="null"
                                                        :label="__('import_cars.lot_no')"/>
                            </div>
                            <div class="col-sm-4">
                                <x-forms.hidden id="_insure_year" :value="1"/>
                                <x-forms.select-option id="leasing_id" :value="null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => null,
                            ]"
                                                       :label="__('import_cars.leasing')"/>
                                {{--                        <x-forms.input-new-line id="_insure_year" :value="null" :label="__('import_cars.insure_year')"/>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal-lot-detail-vue">
                <div class="modal-body pt-0">
                    <div class="block-content pt-0">
                        <div class="table-wrap db-scroll">
                            <table class="table table-striped table-vcenter">
                                <thead class="bg-body-dark">
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        เลขที่ใบสั่งซื้อ
                                    </th>
                                    <th>
                                        หมายเลขเครื่องยนต์
                                    </th>
                                    <th>
                                        เลขตัวถัง
                                    </th>
                                    <th>
                                        วันที่ส่งมอบรถ
                                    </th>
                                    <th>
                                        ลักษณะรถ
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="car_data.length > 0">
                                <tr v-for="(item,index) in car_data">
                                    <td>
                                        @{{ index+1}}
                                    </td>
                                    <td>
                                        @{{ item.po_no ?? '-'}}
                                    </td>
                                    <td>
                                        @{{ item.engine_no ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.chassis_no ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.delivery_date ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.car_characteristics ?? '-' }}
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-clear-search"
                            data-bs-dismiss="modal"
                            onclick="backLotDetailModal()">{{ __('lang.back') }}</button>
                    <button type="button" class="btn btn-primary"
                            onclick="submitModalLotDetail()">{{ __('lang.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
