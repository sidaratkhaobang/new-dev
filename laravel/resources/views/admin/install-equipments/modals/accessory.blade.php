<div class="modal fade" id="install-equipment-modal" aria-labelledby="install-equipment-modal-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="install-equipment-modal-label">{{ __('install_equipments.add_accessory') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <h4 class="fw-light text-gray-darker">{{ __('install_equipments.add_accessory') }}</h4>
                <hr> --}}
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
                            :optionals="['input_class' => 'number-format']" />    
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_price_field" :value="null" :label="__('install_equipments.price_per_unit')"
                            :optionals="[ 'input_class' => 'number-format']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="accessory_supplier_field" :value="null" :list="null"
                            :label="__('install_equipments.supplier_en')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="accessory_remark_field" :value="null" :label="__('install_equipments.remark')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveInstallEquipment()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
