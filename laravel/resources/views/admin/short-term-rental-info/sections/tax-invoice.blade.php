<div class="block {{ __('block.styles') }}" >
    @section('block_options_add_address_btn')
        @if(!isset($edit_rental))
            <button type="button" class="btn btn-primary btn-mini" 
                onclick="addTaxInvoice()" id="openModal"><i class="fa fa-circle-plus"></i>
            </button>
        @endif
    @endsection
    @include('admin.components.block-header', [
        'text' => 'ข้อมูลใบเสร็จและใบกำกับภาษี',
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_add_address_btn',
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-12">
                <div class="form-check form-check-inline mt-1">
                    <input type="checkbox" class="form-check-input" id="is_required_tax_invoice_0" name="is_required_tax_invoice[]" value="1" 
                        {{ ($is_required_tax_invoice ? 'checked' : '') }} 
                    >
                    <label class="custom-control-label" for="is_required_tax_invoice_0">{{ __('short_term_rentals.is_required_tax_invoice') }}</label>
                </div>
                <div class="form-check form-check-inline mt-1 check_customer_address-wrap">
                    <input type="checkbox" class="form-check-input" id="check_customer_address" name="check_customer_address" value="1"
                        {{ ($check_customer_address ? 'checked' : '') }} 
                    >
                    <label class="custom-control-label" for="check_customer_address">ข้อมูลเหมือนกับที่อยู่ลูกค้า</label>
                </div>
            </div>
        </div>
        <div id="toggle-tax-invoices" style="display: none;" >
            <div id="customer-billing-address" ></div>
        </div>

        <!-- same customer info -->
        <div id="toggle-inv-customer" style="display: none;" >
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_name_2" :value="$d->customer_billing_name" :label="__('short_term_rentals.customer')" :optionals="['readonly' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_tax_no_2" :value="$d->customer_billing_tax_no" :label="__('short_term_rentals.tax_no')" :optionals="['readonly' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_email_2" :value="$d->customer_billing_email" :label="__('short_term_rentals.email')" :optionals="['readonly' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_tel_2" :value="$d->customer_billing_tel" :label="__('short_term_rentals.tel')" :optionals="['readonly' => true]" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-12">
                    <x-forms.text-area-new-line id="customer_billing_address_2" :value="$d->customer_billing_address" :label="__('short_term_rentals.address')" :optionals="['readonly' => true]"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_province_name_2" :value="$customer_billing_province_name" :label="__('short_term_rentals.province')" :optionals="['readonly' => true]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_district_name_2" :value="$customer_billing_district_name" :label="__('short_term_rentals.amphure')" :optionals="['readonly' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_subdistrict_name_2" :value="$customer_billing_subdistrict_name" :label="__('short_term_rentals.tumbon')" :optionals="['readonly' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_zipcode_2" :value="$customer_billing_zipcode" :label="__('short_term_rentals.zipcode')" :optionals="['readonly' => true]" />
                </div>
            </div>
        </div>

        <!-- use billing address -->
        <div id="toggle-inv-customer-2" style="display: none;" >
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="customer_billing_address_id" :value="$d->customer_billing_address_id" :list="[]" :label="__('short_term_rentals.customer')" :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                        'default_option_label' => $d->customer_billing_name
                    ]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_tax_no" :value="$d->customer_billing_tax_no" :label="__('short_term_rentals.tax_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_email" :value="$d->customer_billing_email" :label="__('short_term_rentals.email')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_tel" :value="$d->customer_billing_tel" :label="__('short_term_rentals.tel')" />
                </div>
            </div>
            <div class="row push">
                <div class="col-sm-12">
                    <x-forms.text-area-new-line id="customer_billing_address" :value="$d->customer_billing_address" :label="__('short_term_rentals.address')" />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <x-forms.select-option id="customer_billing_province_id" :value="$d->customer_billing_province_id" :list="$province_list" :label="__('short_term_rentals.province')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="customer_billing_district_id" :value="$d->customer_billing_district_id" :list="[]" :label="__('short_term_rentals.amphure')" :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                        'default_option_label' => $customer_district_name
                    ]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="customer_billing_subdistrict_id" :value="$d->customer_billing_subdistrict_id" :list="[]" :label="__('short_term_rentals.tumbon')" :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                        'default_option_label' => $customer_subdistrict_name
                    ]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="customer_billing_zipcode" :value="$customer_billing_zipcode" :label="__('short_term_rentals.zipcode')" :optionals="['readonly' => true]" />
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.short-term-rental-info.modals.tax-invoice')