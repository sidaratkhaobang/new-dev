<div class="modal fade" id="modal-lot" aria-labelledby="modal-edit-purchase" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">เลือกรถที่ต้องการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="closeModalLot()"></button>
            </div>
            <div class="modal-body pb-0">
                <div class="block {{ __('block.styles') }} border-0 shadow-none">
                    <div class="block-content pb-0">
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_po_no" :value="null" :list="[]"
                                                       :optionals="[
                                                               'placeholder' => __('lang.search_placeholder'),
                                                               'ajax' => true,
                                                               ]"
                                                       :label="__('purchase_orders.purchase_order_no')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_car" :value="null" :list="[]"
                                                       :optionals="[
                                                               'placeholder' => __('lang.search_placeholder'),
                                                               'ajax' => true,
                                                               'default_option_label' => null,
                                                               ]"
                                                       :label="__('purchase_orders.engine_no_and_chassie_no')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_delivery_date" :value="null"
                                                    :label="__('purchase_orders.delivery_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_status" :value="null" :list="$status_list"
                                                       :optionals="[
                                                               'placeholder' => __('lang.search_placeholder'),
                                                               ]"
                                                       :label="__('purchase_orders.status')"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal-lot-vue">
                <div class="modal-body">
                    <div class="block {{ __('block.styles') }} border-0 shadow-none pt-0">
                        <div class="block-content pt-0">
                            <div class="row">
                                <div class="col-sm-12 text-end">
                                    <button class="btn btn-primary" onclick="addModalCarDataLot()">
                                        เพิ่ม
                                    </button>
                                </div>
                                <div class="col-sm-12 text-start">
                                    <p>จำนวนรถทั้งหมด @{{ lot_car_data.length }} คัน</p>
                                </div>

                            </div>
                            <div class="table-wrap db-scroll">
                                <table class="table table-striped table-vcenter">
                                    <thead class="bg-body-dark">
                                    <tr>
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
                                            สถานะ
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="lot_car_data.length > 0">
                                    <tr v-for="(item,index) in lot_car_data">
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
                                            <span class="badge badge-custom"
                                                  :class="'badge-bg-'+item.badge_class">@{{ item.badge_status }}</span>
                                            {{--                                            @{{ item.status ?? '-' }}--}}
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-clear-search"
                            data-bs-dismiss="modal" onclick="closeModalLot()">{{ __('lang.back') }}</button>
                    <button type="button" class="btn btn-primary" :disabled="lot_car_data.length <= 0"
                            onclick="submitModalCarLot()">จัด Lot
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
