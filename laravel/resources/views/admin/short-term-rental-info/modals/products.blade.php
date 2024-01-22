<div class="modal fade" id="modal-product" role="dialog" style="overflow:hidden;"
    aria-labelledby="modal-purchase-order-dealer">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.product_detail') }}</h4>
                <hr>
                <div class="row push mb-5">
                    <div class="col-sm-6">
                        <x-forms.select-option id="product_id" :value="null" :list="null" :label="__('short_term_rentals.product')"
                            :optionals="['select_class' => 'js-select2-custom', 'ajax' => true, 'required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="product_price" :value="null" :label="__('short_term_rentals.product_price')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addProduct()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
