<x-modal id="export-excel" :title="__('finance_request.modal_export_excel_title')">
    @include('admin.components.block-header', [
                        'text' => __('lang.search'),
                        'block_icon_class' => 'icon-search',
                        'is_toggle' => false,
                    ])
    <div class="block-content pb-0 pt-0">
        <div class="justify-content-between mb-4">
            <div class="form-group row push mb-4">
                <div class="col-sm-3">
                    <x-forms.select-option id="modal_lot_no" :value="null" :list="[]"
                                           :optionals="[
                                                       'placeholder' => __('lang.search_placeholder'),
                                                       'ajax' => true,
                                                   ]"
                                           :label="__('finance_request.search_lot_no')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="modal_rental" :value="null" :list="[]"
                                           :optionals="[
                                                       'placeholder' => __('lang.search_placeholder'),
                                                       'ajax' => true,
                                                   ]"
                                           :label="__('finance_request.search_rental')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="modal_car" :value="null" :list="[]"
                                           :optionals="[
                                                       'placeholder' => __('lang.search_placeholder'),
                                                       'ajax' => true,
                                                   ]"
                                           :label="__('finance_request.license_plate_engine_chassis')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="modal_contract_no" :value="null" :list="[]"
                                           :optionals="[
                                                       'placeholder' => __('lang.search_placeholder'),
                                                       'ajax' => true,
                                                   ]"
                                           :label="__('finance_request.search_contract_no')"/>
                </div>

                <div class="col-sm-3">
                    <x-forms.date-input id="modal_date_create" :value="null"
                                        :label="__('finance_request.search_finance_date')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="modal_contract_start_date" :value="null"
                                        :label="__('finance_request.search_contract_start_date')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="modal_contract_end_date" :value="null"
                                        :label="__('finance_request.search_contract_end_date')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="modal_status" :value="null" :list="$status_list"
                                           :optionals="['placeholder' => __('lang.search_placeholder')]"
                                           :label="__('finance_request.search_status')"/>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-12 text-end">
                    <a class="btn btn-outline-secondary btn-clear-search btn-custom-size me-1"
                       onclick="clearModalFilter()"><i
                                class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</a>
                    <button type="button" class="btn btn-primary btn-custom-size"
                            onclick="addModalFinanceCarData()"><i
                                class="fa fa-magnifying-glass"></i> เพิ่มรถ
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-export-excel-vue" class="modal-body pb-1 pt-0">
        <div class="block-content pt-0">
            <p>
                จำนวนรถทั้งหมด <span>5</span> คัน
            </p>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ __('finance_request.search_lot_no') }}
                        </th>
                        <th>
                            {{__('finance_request.search_rental')}}
                        </th>
                        <th>
                            {{__('cars.license_plate')}}
                        </th>
                        <th>
                            {{__('cars.chassis_no')}}
                        </th>
                        <th>
                            {{__('cars.engine_no')}}
                        </th>
                        <th>
                            {{__('finance_request.search_contract_no')}}
                        </th>
                        <th>
                            {{__('finance_request.search_finance_date')}}
                        </th>
                        <th>
                            {{__('finance_request.search_status')}}
                        </th>
                        <th>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="modal_export_car_data.length > 0">
                        <tr v-for="(item,index) in modal_export_car_data">
                            <td>
                                @{{ index+1 }}
                            </td>
                            <td>
                                @{{ item.lot_no ?? '-'}}
                            </td>
                            <td>
                                @{{ item.leasing ?? '-'}}
                            </td>
                            <td>
                                @{{ item.license_plate ?? '-' }}
                            </td>
                            <td>
                                @{{ item.engine_no ?? '-' }}
                            </td>
                            <td>
                                @{{ item.chassis_no ?? '-' }}
                            </td>
                            <td>
                                @{{ item.contract_no ?? '-' }}
                            </td>
                            <td>
                                @{{ item.finance_date ?? '-' }}
                            </td>
                            <td>
                                <span class="badge badge-custom  " :class="'badge-bg-'+item.badge_class">@{{ item.badge_status }}</span>
                            </td>
                            <td>
                                <button type="button" href="javascript:void(0)"
                                        @click="removeModalFinanceCarData(index)"
                                        class="border-0 bg-transparent"><i
                                            class="fa-solid fa-trash-can pe-none"
                                            style="color: red;"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-slot name="footer">
        <div class="row">
            <div class="text-end">
                <button type="button" class="btn btn-outline-secondary btn-custom-size me-2"
                        data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <a class="btn btn-primary" onclick="exportExcel()">
                    <i class="icon-document-download"></i>
                    ดาวน์โหลด Excel
                </a>
            </div>
        </div>
    </x-slot>
</x-modal>