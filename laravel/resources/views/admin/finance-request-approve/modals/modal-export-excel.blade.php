<div class="modal fade" id="modal-export-excel" tabindex="-1" aria-labelledby="modal-complete" style="display: none;"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-start">{{__('finance_request.modal_export_excel_title')}}</h5>
            </div>
            <div class="modal-body pb-1">
                @include('admin.components.block-header', [
                        'text' => __('lang.search'),
                        'block_icon_class' => 'icon-search',
                        'is_toggle' => true,
                    ])
                <div class="block-content">
                    <div class="justify-content-between mb-4">
                        <div class="form-group row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_lot_no" :value="null" :list="$lot_no_list"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('finance_request.search_lot_no')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_rental" :value="null" :list="$rental_list"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('finance_request.search_rental')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.date-input id="modal_date_create" :value="null"
                                                    :label="__('finance_request.search_date_create')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="modal_status" :value="null" :list="$status_list"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('finance_request.search_status')"/>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <a class="btn btn-outline-secondary btn-clear-search btn-custom-size me-1"><i
                                        class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</a>
                                <button type="button" class="btn btn-primary btn-custom-size"><i
                                        class="fa fa-magnifying-glass"></i> {{ __('lang.search') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-content">
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
                                    {{__('finance_request.car_total')}}
                                </th>
                                <th>
                                    {{__('finance_request.search_status')}}
                                </th>
                                <th>

                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="modal_export_car_data.length != 0">
                                <tr v-for="(value,index) in modal_export_car_data">
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                </tr>
                            </template>
                            <template>
                                <tr>
                                    <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
