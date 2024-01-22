<div class="modal fade" id="rental-line-modal" role="dialog" style="overflow:hidden;"
    aria-labelledby="rental-line-modal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rental-line-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.product_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_name" :value="null" :label="__('products.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="rental_line_description" :value="null" :label="__('short_term_rentals.description')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_amount" :value="null" :label="__('short_term_rentals.amount')" 
                        :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_subtotal" :value="null" :label="__('short_term_rentals.price_per_unit')" 
                        :optionals="['required' => true, 'type' => 'number']"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addRentalLine()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
