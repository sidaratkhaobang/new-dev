@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/invoice.css') }}">
@endpush
<div id="toggle-tax-invoices" style="display: none">
    <div id="tax-invoices" v-cloak data-detail-uri="" data-title="">
        <div class="row mt-2">
            <div v-for="(item, index) in tax_invoice_list" class="col-md-6 col-xl-6">
                <div class="block-customer" @click="billingById(item.id)">
                    <div class="block-content block-content-cunstom">
                        <div class="row">
                            <div class="col-12">
                                <p class="block-title-custom">@{{ item.tax_customer_name }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/user.png') }}" alt="user">
                                @{{ item.tax_customer_type_text }}
                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/usercode.png') }}" alt="user-code">
                                @{{ item.tax_tax_no }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/mail.png') }}" alt="mail">
                                @{{ item.tax_customer_email }}

                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/phone.png') }}" alt="phone">
                                @{{ item.tax_customer_tel }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/location.png') }}" alt="location">
                                @{{ item.tax_customer_province_text }}
                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/zipcode.png') }}" alt="zipcode">
                                @{{ item.tax_customer_zipcode }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <img class="me-1" src="{{ asset('images/icons/location.png') }}" alt="location">
                                @{{ item.tax_customer_address }}
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
