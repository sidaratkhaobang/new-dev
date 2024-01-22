<div class="modal fade" id="modal-bom" tabindex="-1" aria-labelledby="modal-bom" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bom-modal-label">เพิ่มข้อมูลอุปกรณ์เสริม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="bom_id" :value="null" :list="null" :label="'ชื่อ/เลขที่ Bom'"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                </div>
                <div id="bom-car-line" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th style="width: 2%;">#</th>
                                <th>{{ __('install_equipments.accessory') }}</th>
                                <th>{{ __('install_equipments.class') }}</th>
                                <th class="text-end">{{ __('install_equipments.amount_per_unit') }}</th>
                                <th class="text-end">{{ __('install_equipments.price_per_unit') }}</th>
                                <th>{{ __('install_equipments.supplier_en') }}</th>
                                <th>{{ __('lang.remark') }}</th>
                            </thead>
                            <tbody v-if="bom_list.length > 0">
                                <tr v-for="(item, index) in bom_list">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.accessory_text }}</td>
                                    <td>@{{ item.accessory_class }}</td>
                                    <td class="text-end">@{{ getNumberWithCommas(item.amount) }}</td>
                                    <td class="text-end">@{{ getNumberWithCommas(item.price) }}</td>
                                    <td>@{{ item.supplier_text }}</td>
                                    <td><input type="text" class="form-control" v-model="item.remark"></td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="7">"
                                        {{ __('lang.no_list') . __('install_equipments.accessory') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" v-on:click="importBOMAccessories()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
