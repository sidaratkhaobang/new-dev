<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.receipt_address_detail'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="name_receipt" :value="$d?->name_receipt"
                                        :label="__('change_registrations.name_contact')"
                                        :optionals="['required' => false]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tax_no_receipt" :value="$d?->tax_no_receipt"
                                        :label="__('change_registrations.tax_no_receipt')"
                                        :optionals="['required' => false]"/>

            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="tel_receipt" :value="$d?->tel_receipt"
                                        :label="__('change_registrations.tel_contact')"
                                        :optionals="['required' => false]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="email_receipt" :value="$d?->email_receipt"
                                        :label="__('change_registrations.email_contact')"
                                        :optionals="['required' => false]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <x-forms.text-area-new-line id="address_receipt" :value="$d?->address_receipt"
                                            :label="__('change_registrations.address_contact')"
                                            :optionals="['required' => false]"/>
            </div>
        </div>
    </div>
</div>
