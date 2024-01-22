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
                        <x-forms.input-new-line id="remark_car_field" :value="null" :label="__('purchase_requisitions.remark')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveCarAccessory()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
