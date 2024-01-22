<div class="modal fade" id="modal-customer" role="dialog" style="overflow:hidden;" aria-labelledby="modal-customer">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customer-label">{{ __('lang.add_data') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.customer_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="customer_type_temp" :value="null" :list="[]" :label="__('customers.customer_type')" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => null]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="customer_code_temp" :value="null" :label="__('short_term_rentals.customer_code')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="customer_name_temp" :value="null" :label="__('short_term_rentals.customer')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="customer_email_temp" :value="null" :label="__('short_term_rentals.email')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="customer_tel_temp" :value="null" :label="__('short_term_rentals.tel')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="customer_province_id_temp" :value="null" :list="[]" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => null]" :label="__('short_term_rentals.province')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="customer_zipcode_temp" :value="null" :label="__('short_term_rentals.zipcode')" />
                    </div>
                </div>
                <div class="row push mb-3">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="customer_address_temp" :value="null" :label="__('short_term_rentals.address')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-customer">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>