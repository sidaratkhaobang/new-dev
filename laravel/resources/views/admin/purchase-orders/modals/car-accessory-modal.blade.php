<div class="modal fade" id="modal-car-accessory" tabindex="-1" aria-labelledby="modal-car-accessory" aria-hidden="false"
    data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-accessory-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('purchase_requisitions.data_car_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.select-option id="car_class_field" :value="null" :list="null"
                            :label="__('purchase_requisitions.car_class_code')" :optionals="['required' => true, 'select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-8">
                        <x-forms.select-option id="car_color_field" :value="null" :list="null"
                            :label="__('purchase_requisitions.car_color')" :optionals="['required' => true, 'select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount_car_field" :value="null" :label="__('purchase_requisitions.car_amount')"
                            :optionals="['required' => true, 'oninput' => true, 'type' => 'number']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="remark_car_field" :value="null" :label="__('purchase_requisitions.remark')" />
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('purchase_requisitions.accessories_table') }}</h4>
                <hr>
                <div class="row push">
                    <div class="col-sm-8">
                        <x-forms.select-option id="accessory_field" :value="null" :list="null"
                            :label="__('purchase_requisitions.accessories')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="amount_accessory_field" :value="null" :label="__('purchase_requisitions.car_amount')"
                            :optionals="['oninput' => true, 'type' => 'number']" />
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="remark_field" :value="null" :label="__('purchase_requisitions.remark')" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-primary" id="add-accessrory"
                            onclick="addAccessory()">{{ __('lang.add') }}</button>
                    </div>
                </div>
                <div class id="pr-accessory" v-cloak data-detail-uri="" data-title="">
                    <div class="table-wrap">
                        <table class="table table-striped">
                            <thead class="bg-body-dark">
                                <th>#</th>
                                <th>{{ __('car_classes.accessories') }}</th>
                                <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                                <th>{{ __('purchase_requisitions.remark') }}</th>
                                <th class="sticky-col text-center remove-accessory">{{ __('lang.tools') }}</th>
                            </thead>
                            <tbody v-if="car_accessories.length > 0">
                                <tr v-for="(item, index) in car_accessories">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.accessory_text }}</td>
                                    <td>@{{ item.amount_accessory }}</td>
                                    <td>@{{ item.remark_accessory }}</td>
                                    <td class="sticky-col text-center remove-accessory">
                                        <div class="btn-group">
                                            <div class="col-sm-12">
                                                <a class="dropdown-item btn-delete-row "
                                                    v-on:click="removeAccessory(index)"><i
                                                        class="fa fa-trash-alt me-1"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="6">
                                        " {{ __('lang.no_list') . __('purchase_requisitions.accessories_table') }} "
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" id="cancel-button"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="save-modal"
                    onclick="saveCarAccessory()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
