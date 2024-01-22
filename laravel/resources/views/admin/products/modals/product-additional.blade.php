<div class="modal fade" id="modal-product-additional" role="dialog" style="overflow:hidden;"
    aria-labelledby="modal-product-additional">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-additional-modal-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-8">
                        <x-forms.select-option id="product_additional_id_field" :value="null" :list="null"
                            :label="__('product_additionals.name')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="price_field" :value="null" :label="__('products.price')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="amount_field" :value="null" :label="__('products.amount')"
                            :optionals="['input_class' => 'number-format col-sm-4', 'required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="free_field" :value="null" :list="$yes_no_list" :label="__('products.free')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="addProductAdditional()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
