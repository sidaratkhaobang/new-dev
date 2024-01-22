<div class="modal fade" id="car-modal" role="dialog" style="overflow:hidden;" aria-labelledby="car-modal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="car-label">รถ/เรือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4 car_detail">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_name" :value="null" :label="__('short_term_rentals.package_name')" :optionals="['readonly' => true]" />
                    </div>
                    <div class="col-sm-8">
                        <x-forms.input-new-line id="rental_line_description" :value="null" :label="__('short_term_rentals.license_plate')" :optionals="['readonly' => true]" />
                    </div>
                </div>
                <div class="row push mb-4 car_detail">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_amount" :value="null" :label="__('short_term_rentals.amount')"
                            :optionals="['type' => 'number', 'readonly' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="rental_line_subtotal" :value="null" :label="__('short_term_rentals.price_per_unit')"
                            :optionals="['required' => true, 'type' => 'number']" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary add-car"
                    onclick="saveCar()"><i class="icon-save me-1"></i> {{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
