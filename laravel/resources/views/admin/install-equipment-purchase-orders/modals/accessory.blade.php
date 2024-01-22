<div class="modal fade" id="install-equipment-po-line-modal" aria-labelledby="install-equipment-modal-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="install-equipment-po-line-modal-label">{{ __('install_equipments.add_accessory') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="accessory_field" :value="null" :list="null"
                            :label="__('install_equipments.accessory')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_class_field" :value="null" :list="null"
                            :label="__('install_equipments.class')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_amount_field" :value="null" :label="__('install_equipments.amount_per_unit')"
                            :optionals="['type' => 'text', 'input_class' => 'number-format']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_price_field" :value="null" :label="__('install_equipments.price_per_unit')"
                            :optionals="['type' => 'text', 'input_class' => 'number-format']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_discount_field" :value="null" :label="__('install_equipments.discount')"
                        :optionals="['type' => 'text', 'input_class' => 'number-format']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.hidden id="accessory_supplier_field" :value="$d->supplier_id" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveInstallEquipmentPOLIne()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
