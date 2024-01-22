<div class="modal fade" id="modal-billing-address" tabindex="-1" aria-labelledby="modal-billing-address"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="billing-address-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('customers.billing_address_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name_field" :value="null" :label="__('customers.name_all')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tax_no_field" :value="null" :label="__('customers.tax_no')" :optionals="['maxlength' => 20, 'required' => true]"/>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="address_field" :value="null" :label="__('customers.address')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.select-option id="province_field" :value="null" :list="null" :label="__('customers.province')"
                            :optionals="[
                                'ajax' => true,
                                'select_class' => 'js-select2 js-select2-custom',
                            ]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="district_field" :value="null" :list="null" :label="__('customers.district')"
                            :optionals="[
                                'ajax' => true,
                                'select_class' => 'js-select2 js-select2-custom',
                            ]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="subdistrict_field" :value="null" :list="null" :label="__('customers.subdistrict')"
                            :optionals="[
                                'ajax' => true,
                                'select_class' => 'js-select2 js-select2-custom',
                            ]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="email_field" :value="null" :label="__('customers.email')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="tel_field" :value="null" :label="__('customers.tel_driver')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="saveBillingAddress()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
