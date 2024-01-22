<div class="modal fade" id="modal-print-rental" tabindex="-1" aria-labelledby="modal-print-rental" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="_temp_id" value="">
                <h5 class="modal-title" id="car-accessory-modal-label">{{ __('long_term_rentals.requisition_pdf') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('long_term_rentals.rental_car_info') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="lt_rental_car_class" :value="null" :list="null" :label="__('long_term_rentals.car_class_and_color')"
                            :optionals="['ajax' => true, 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="lt_rental_car_class_amount" :value="null" :label="__('long_term_rentals.car_amount_unit')" 
                        :optionals="['type' => 'number', 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="lt_months" :value="null" :list="null" :label="__('long_term_rentals.rental_duration')"
                            :optionals="['ajax' => true, 'required' => true]" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="printRentalRequisition()">{{ __('long_term_rentals.print') }}</button>
            </div>
        </div>
    </div>
</div>
