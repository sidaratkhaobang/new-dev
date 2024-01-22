@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('short_term_rentals.class_' . $d->status),
            __('short_term_rentals.status_' . $d->status),
            null,
        ) !!}
    @endif
@endsection

@section('content')
    @include('admin.components.creator')
    <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <x-short-term-rental.step-channel :rentalid="$rental_id" :success="true" :show="true" :istoggle="true"/>
    <div class="block {{ __('block.styles') }}">
        <x-blocks.block-header-step :title="__('short_term_rentals.step_title.info')" :step="3"
                                    :optionals="['block_icon_class' => __('short_term_rentals.step_icon.info')]"/>
        <div class="block-content pt-0">
            <form id="save-form">
                @include('admin.short-term-rental-info.sections.branch')
                @include('admin.short-term-rental-info.sections.rental-detail')
                @include('admin.short-term-rental-info.sections.customer')
                @include('admin.short-term-rental-info.sections.tax-invoice')
                <x-forms.hidden id="rental_id" :value="$rental_id"/>
                <x-forms.hidden id="service_type_id" :value="$service_type_id"/>
                <x-forms.hidden id="customer_billing_address_id_selected" :value="$d->customer_billing_address_id"/>
                <x-forms.hidden id="customer_billing_address_name_selected" :value="$d->customer_billing_name"/>
                <x-forms.hidden id="type_package" :value="$d?->type_package"/>

                <x-short-term-rental.submit-group :rentalid="$rental_id" :step="2" :optionals="[
                    'btn_name' => __('short_term_rentals.save_and_next'),
                    'icon_class_name' => 'fa fa-arrow-circle-right',
                ]"/>
            </form>
        </div>
    </div>
    <x-short-term-rental.step-asset :rentalid="null" :success="false"/>
    <x-short-term-rental.step-driver :rentalid="null" :success="false"/>
    <x-short-term-rental.step-promotion :rentalid="null" :success="false"/>
    <x-short-term-rental.step-summary :rentalid="null" :success="false"/>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.info.store'),
])
@include('admin.short-term-rentals.scripts.update-cancel-status')
@include('admin.components.date-input-script')
@include('admin.short-term-rental-info.scripts.customer-script')
@include('admin.short-term-rental-info.scripts.tax-invoice-script')
@include('admin.short-term-rental-info.scripts.origin-script')
@include('admin.short-term-rental-info.scripts.destination-script')
@include('admin.short-term-rental-info.scripts.origin-google-map')
@include('admin.short-term-rental-info.scripts.destination-google-map')
@include('admin.short-term-rental-info.scripts.products-script')

@include('admin.components.select2-ajax', [
    'id' => 'product_id_filter',
    'parent_id' => 'branch_id',
    'parent_id_2' => 'service_type_id',
    'parent_id_3' => 'type_package',
    'url' => route('admin.util.select2.products-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'origin_id',
    'parent_id' => 'branch_id',
    'url' => route('admin.util.select2.origins-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'destination_id',
    'parent_id' => 'branch_id',
    'url' => route('admin.util.select2.destinations-by-branch'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_id',
    'parent_id' => 'customer_type',
    'url' => route('admin.util.select2-customer.customer-codes'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_type_temp',
    'modal' => '#modal-customer',
    'url' => route('admin.util.select2-short-term-rental.get-customer-type'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_province_id_temp',
    'modal' => '#modal-customer',
    'url' => route('admin.util.select2-short-term-rental.get-province'),
])

@include('admin.components.select2-ajax', [
    'id' => 'tax_customer_type',
    'modal' => '#modal-tax-invoice',
    'url' => route('admin.util.select2-short-term-rental.get-customer-type'),
])

@include('admin.components.select2-ajax', [
    'id' => 'tax_customer_province_id',
    'modal' => '#modal-tax-invoice',
    'url' => route('admin.util.select2-short-term-rental.get-province'),
])

<!-- same customer info -->
@include('admin.components.select2-ajax', [
    'id' => 'customer_province_id',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_district_id',
    'parent_id' => 'customer_province_id',
    'url' => route('admin.util.select2.districts'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_subdistrict_id',
    'parent_id' => 'customer_district_id',
    'url' => route('admin.util.select2.subdistricts'),
])

<!-- use billing address -->
@include('admin.components.select2-ajax', [
    'id' => 'customer_billing_province_id',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_billing_district_id',
    'parent_id' => 'customer_billing_province_id',
    'url' => route('admin.util.select2.districts'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_billing_subdistrict_id',
    'parent_id' => 'customer_billing_district_id',
    'url' => route('admin.util.select2.subdistricts'),
])

@include('admin.components.select2-ajax', [
    'id' => 'customer_billing_address_id',
    'parent_id' => 'customer_id',
    'url' => route('admin.util.select2-customer.customer-billing-address'),
])

@include('admin.short-term-rental-info.scripts.update-datetime-script')
@push('scripts')
    <script>
        $(document).ready(function () {
            getProductData();
            $('#is_required_tax_invoice_0').trigger('change');
        });
        eventSelect = $('#customer_id');
        // eventSelect.on('change', function (e) {
        //     clearCustomerDetail();
        // });

        eventSelect.on('select2:select', function (e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-customer.customer-detail') }}", {
                params: {
                    customer_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    addCustomerDetail(response.data.data);
                    getDataCustomerBillingAddress(data.id, function(){
                        copyCustomerAddressToBilling();
                    });
                }
            });
        });

        $('#customer_province_id, #customer_district_id').on('select2:select', function(e) {
            setZipCode('');
        });

        $('#customer_subdistrict_id').on('select2:select', function(e) {
            var data = e.params.data;
            setZipCode(data.zip_code);
        }).on('select2:unselect', function(e) {
            setZipCode('');
        }).on('select2:clear', function(e) {
            setZipCode('');
        });

        function setZipCode(zip_code){
            $('#customer_zipcode').val(zip_code);
            $('#customer_billing_zipcode').val(zip_code);
            $('#customer_billing_zipcode_2').val(zip_code);
        }

        function addCustomerDetail(data) {
            //$('#customer_type').val(data.customer_type).trigger('change');
            $('#customer_name').val(data.name);
            $('#customer_tax_no').val(data.tax_no);
            $('#customer_email').val(data.email);
            $('#customer_tel').val(data.tel);
            $('#customer_zipcode').val(data.zip_code);
            $('#customer_address').val(data.address);

            if(data.province_id){
                set_select2($('#customer_province_id'), data.province_id, data.province_name);
            } else {
                $('#customer_province_id').val(null).trigger('change');
            }
            if(data.district_id){
                set_select2($('#customer_district_id'), data.district_id, data.district_name);
            } else {
                $('#customer_district_id').val(null).trigger('change');
            }
            if(data.subdistrict_id){
                set_select2($('#customer_subdistrict_id'), data.subdistrict_id, data.subdistrict_name);
            } else {
                $('#customer_subdistrict_id').val(null).trigger('change');
            }
        }

        function clearCustomerDetail() {
            //$('#customer_type').val(null).trigger('change');
            $('#customer_name').val('');
            $('#customer_email').val('');
            $('#customer_tel').val('');
            $('#customer_zipcode').val('');
            $('#customer_address').val('');

            $('#customer_province_id').val(null).trigger('change');
            $('#customer_district_id').val(null).trigger('change');
            $('#customer_subdistrict_id').val(null).trigger('change');
        }

        // billing address
        billingAddressSelect = $('#customer_billing_address_id');

        billingAddressSelect.on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.util.select2-customer.customer-billing-detail') }}", {
                params: {
                    customer_billing_address_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    addCustomerBillingDetail(response.data.data);
                }
            });
        });

        function addCustomerBillingDetail(data) {
            set_select2($('#customer_billing_address_id'), data.id, data.name);
            //$('#customer_type').val(data.customer_type).trigger('change');
            $('#customer_billing_name').val(data.name);
            $('#customer_billing_tax_no').val(data.tax_no);
            $('#customer_billing_email').val(data.email);
            $('#customer_billing_tel').val(data.tel);
            //$('#customer_billing_province_id').val(data.province_id).trigger('change');
            $('#customer_billing_zipcode').val(data.zip_code);
            $('#customer_billing_address').val(data.address);

            set_select2($('#customer_billing_province_id'), data.province_id, data.province_name);
            set_select2($('#customer_billing_district_id'), data.district_id, data.district_name);
            set_select2($('#customer_billing_subdistrict_id'), data.subdistrict_id, data.subdistrict_name);
        }

        $('#customer_billing_province_id, #customer_billing_district_id').on('select2:select', function(e) {
            $('#customer_billing_zipcode_2').val('');
        });

        $('#customer_billing_subdistrict_id').on('select2:select', function(e) {
            var data = e.params.data;
            $('#customer_billing_zipcode_2').val(data.zip_code);
        }).on('select2:unselect', function(e) {
            $('#customer_billing_zipcode_2').val('');
        }).on('select2:clear', function(e) {
            $('#customer_billing_zipcode_2').val('');
        });


        $('#toggle-tax-invoices').hide();

        function copyCustomerAddressToBilling() {
            console.log('copyCustomerAddressToBilling');
            var customer_type = $('#customer_type').val();
            var customer_type_name = null;
            if (customer_type != '') {
                customer_type_name = $('#customer_type').find(":selected").text().trim();
            }

            var customer_id = $('#customer_id').val();
            var customer_code_name = null;
            if (customer_id != '') {
                customer_code_name = $('#customer_id').find(":selected").text().trim();
            }

            var customer_name = $('#customer_name').val();
            var customer_tax_no = $('#customer_tax_no').val();
            var customer_email = $('#customer_email').val();
            var customer_tel = $('#customer_tel').val();

            var province_id = $('#customer_province_id').val();
            var province_name = null;
            if (province_id != '') {
                province_name = $('#customer_province_id').find(":selected").text().trim();
            }

            var district_id = $('#customer_district_id').val();
            var district_name = null;
            if (district_id != '') {
                district_name = $('#customer_district_id').find(":selected").text().trim();
            }

            var subdistrict_id = $('#customer_subdistrict_id').val();
            var subdistrict_name = null;
            if (subdistrict_id != '') {
                subdistrict_name = $('#customer_subdistrict_id').find(":selected").text().trim();
            }

            var zipcode = $('#customer_zipcode').val();
            var customer_address = $('#customer_address').val();

            $('#customer_billing_name_2').val(customer_name);
            $('#customer_billing_tax_no_2').val(customer_tax_no);
            $('#customer_billing_email_2').val(customer_email);
            $('#customer_billing_tel_2').val(customer_tel);
            $('#customer_billing_province_name_2').val(province_name);
            $('#customer_billing_district_name_2').val(district_name);
            $('#customer_billing_subdistrict_name_2').val(subdistrict_name);
            $('#customer_billing_zipcode_2').val(zipcode);
            $('#customer_billing_address_2').val(customer_address);
        }

        function clearCustomerBillingAddress() {
            $('#customer_billing_name').val('');
            $('#customer_billing_tax_no').val('');
            $('#customer_billing_email').val('');
            $('#customer_billing_tel').val('');
            $('#customer_billing_province_name').val('');
            $('#customer_billing_district_name').val('');
            $('#customer_billing_subdistrict_name').val('');
            $('#customer_billing_zipcode').val('');
            $('#customer_billing_address').val('');
        }

        function clearCustomerBillingAddress2() {
            $('#customer_billing_name_2').val('');
            $('#customer_billing_tax_no_2').val('');
            $('#customer_billing_email_2').val('');
            $('#customer_billing_tel_2').val('');
            $('#customer_billing_province_name_2').val('');
            $('#customer_billing_district_name_2').val('');
            $('#customer_billing_subdistrict_name_2').val('');
            $('#customer_billing_zipcode_2').val('');
            $('#customer_billing_address_2').val('');
        }

        // tax invoice
        function is_required_tax_invoice(){
            return $('#is_required_tax_invoice_0').prop('checked');
        }

        function is_billing_same_customer_address(){
            return $('#check_customer_address').prop('checked');
        }

        $('#is_required_tax_invoice_0').on("change", function() {
            if (is_required_tax_invoice()) {
                $('.check_customer_address-wrap').show();
                $('#check_customer_address').trigger('change');
            } else {
                $('.check_customer_address-wrap').hide();
                $('#toggle-tax-invoices').hide();
                $('#toggle-inv-customer').hide();
            }
        });

        $('#check_customer_address').on("change", function() {
            if (is_required_tax_invoice()) {
                if (is_billing_same_customer_address()) {
                    copyCustomerAddressToBilling();
                    $('#toggle-inv-customer').show();
                    $('#toggle-inv-customer-2').hide();
                    clearCustomerBillingAddress();
                } else {
                    clearCustomerBillingAddress2();
                    $('#toggle-inv-customer').hide();
                    $('#toggle-inv-customer-2').show();
                }
            } else {
                $('#toggle-tax-invoices').hide();
                $('#toggle-inv-customer').hide();
            }
        });

        // detect customer change
        $('#customer_name, #customer_tax_no, #customer_email, #customer_tel, #customer_address, #customer_province_id, #customer_district_id, #customer_subdistrict_id').on("change", function() {
            if (is_required_tax_invoice()) {
                if (is_billing_same_customer_address()) {
                    copyCustomerAddressToBilling();
                }
            }
        });
    </script>
@endpush
