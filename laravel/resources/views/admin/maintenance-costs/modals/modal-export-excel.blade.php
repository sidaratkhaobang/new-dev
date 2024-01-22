<div class="modal fade" id="modal-export-excel" tabindex="-1" aria-labelledby="modal-complete" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start">{{__('maintenance_costs.title_modal_export_excel')}}</h5>
            </div>
            <div class="modal-body pb-1">
                <div class="block {{ __('block.styles') }} border-0 shadow-none">
                    <div block {{ __('block.styles') }}>
                        <div class="form-group row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_worksheet_no" :value="$worksheet_no ?? null" :list="[]"
                                                       :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $worksheet_no_name,
                            ]"
                                                       :label="__('maintenance_costs.search_worksheet_no')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_center" :value="$center ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $center_name,
                            ]"
                                                       :label="__('maintenance_costs.search_center')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_geographie" :value="$geographie ?? null" :list="[]"
                                                       :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $geographie_name,
                            ]"
                                                       :label="__('maintenance_costs.search_geographie')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_car" :value="$car ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $car_name,
                            ]"
                                                       :label="__('maintenance_costs.search_car')"/>
                            </div>
                        </div>
                        <div class="form-group row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_invoice_no" :value="$invoice_no ?? null"
                                                       :list="[]"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('maintenance_costs.table_invoice_no')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_in_center_date" :value="$in_center_date ?? null"
                                                    :label="__('maintenance_costs.search_in_center_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_end_date" :value="$end_date ?? null"
                                                    :label="__('maintenance_costs.search_end_date')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_status" :value="$status ?? null"
                                                       :list="$status_list ?? []"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('maintenance_costs.search_status')"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="export-excel" class="block {{ __('block.styles') }} border-0 shadow-none">
                    <div class="row">
                        <div class="col-6">
                            จำนวนรถทั้งหมด 6 คัน
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="text-end">
                                    <button onclick="clearModalExportSearch()" type="button"
                                            class="btn btn-outline-secondary btn-custom-size me-2">
                                        {{ __('lang.clear_search') }}
                                    </button>
                                    <a class="btn btn-primary" onclick="addExcelExportData()">
                                        <i class="icon-add-circle"></i>
                                        {{ __('lang.add_data') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="table-wrap db-scroll">
                            <table class="table table-striped table-vcenter">
                                <thead class="bg-body-dark">
                                <tr>
                                    <th style="width: 1px;">#</th>
                                    <th>{{ __('maintenance_costs.search_worksheet_no') }}</th>
                                    <th>{{ __('maintenance_costs.search_center') }}</th>
                                    <th>{{ __('maintenance_costs.search_geographie') }}</th>
                                    <th>{{ __('maintenance_costs.table_license_plate') }}</th>
                                    <th>{{ __('maintenance_costs.table_chassis_no') }}</th>
                                    <th>{{ __('maintenance_costs.table_engine_no') }}</th>
                                    <th class="text-center">{{ __('maintenance_costs.table_invoice_no') }}</th>
                                    <th>{{__('maintenance_costs.search_in_center_date')}}</th>
                                    <th>{{__('lang.status')}}</th>
                                    <th style="width: 100px;" class="sticky-col"></th>
                                </tr>
                                </thead>
                                <tbody v-if="export_excel_list.length > 0">
                                <tr v-for="(item,index) in export_excel_list">
                                    <td>
                                        @{{ index+1 }}
                                    </td>
                                    <td>
                                        @{{ item.worksheet_no ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.creditor_name ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.geographie_name ?? '-' }}
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
                                        @{{ item.invoice_no ?? '-' }}
                                    </td>
                                    <td>
                                        @{{ item.created_at ?? '-' }}
                                    </td>
                                    <td>
                                        <span
                                                class="badge badge-custom badge-bg-primary">@{{ item.status }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-mini" @click="removeRepairList(index)"><i
                                                    class="icon-bin"></i></button>
                                    </td>

                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{--            {!! $list->appends(\Request::except('page'))->render() !!}--}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-custom-size me-2"
                                data-bs-dismiss="modal"
                                onclick="clearModalExportSearch()">{{ __('lang.back') }}</button>
                        <a class="btn btn-primary" onclick="exportExcel()">
                            <i class="icon-document-download"></i>
                            ดาวน์โหลด Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
