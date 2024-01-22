<div class="modal fade" id="excel-modal" aria-labelledby="excel-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="excel-modal-label">{{ __('install_equipments.excel') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="excel_supplier_id" :value="null"
                            :list="null"
                            :label="__('install_equipments.supplier_en')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="excel_install_equipment_id" :value="null"
                            :list="null"
                            :label="__('install_equipments.install_equipment_po_no')" />
                    </div>
                    <div class="col-sm-3 align-self-end">
                        <label for=""></label>
                        <button type="button" class="btn btn-primary" onclick="addExcel()">
                            <i class="fa fa-fw fa-plus me-1"></i> {{ __('lang.add') }}
                        </button>
                    </div>
                </div>
                <div class="mb-4" id="install-equipment-excel" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('install_equipments.install_equipment_po_no') }}</th>
                                <th>{{ __('install_equipments.worksheet_list') }}</th>
                                <th>{{ __('install_equipments.supplier_en') }}</th>
                                <th></th>
                            </thead>
                            <tbody v-if="excel_list.length > 0">
                                <tr v-for="(item, index) in excel_list">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.po_worksheet_no }}</td>
                                    <td>@{{ item.worksheet_no }}</td>
                                    <td>@{{ item.supplier_name }}</td>
                                    <td class="sticky-col text-center">
                                        <div class="btn-group ">
                                            <div class="col-sm-12">
                                                <a class="dropdown-item btn-delete-row btn-light"
                                                    v-on:click="remove(index)">
                                                    <i class="fa fa-trash-alt me-1 text-danger"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="5">" {{ __('lang.no_data') }} "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="exportExcel()">{{ __('lang.download') }}</button>
            </div>
        </div>
    </div>
</div>
