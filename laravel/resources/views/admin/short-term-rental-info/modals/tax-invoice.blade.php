<div class="modal fade" id="modal-tax-invoice" tabindex="-1" aria-labelledby="modal-tax-invoice" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout modal-dialog-scrollable" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tax-invoice-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.address_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="tax_customer_type" :value="null" :list="[]" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => null]" :label="__('customers.customer_type')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_tax_no" :value="null" :label="__('short_term_rentals.tax_no')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4 toggle-div">
                    <div class="col-sm-6">
                        <x-forms.select-option id="tax_branch_office" :value="null" :list="$branch_office_list" :label="__('short_term_rentals.branch_office')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_branch_name" :value="null" :label="__('short_term_rentals.branch_name')" />
                    </div>
                </div>
                <div class="row push mb-4 toggle-div">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_branch_no" :value="null" :label="__('short_term_rentals.branch_no')" />
                    </div>
                </div>

                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="tax_customer_name" :value="null" :label="__('short_term_rentals.customer')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_customer_email" :value="null" :label="__('short_term_rentals.email')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_customer_tel" :value="null" :label="__('short_term_rentals.tel')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="tax_customer_province_id" :value="null" :list="[]" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => null]" :label="__('short_term_rentals.province')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_customer_zipcode" :value="null" :label="__('short_term_rentals.zipcode')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="tax_customer_address" :value="null" :label="__('short_term_rentals.address')" :optionals="['required' => true]" />
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveTaxInvoice()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>