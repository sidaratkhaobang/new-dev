<div id="toggle-tax-invoices"
    @if ($check_customer_address === BOOL_FALSE) style="display: block" @else style="display: none" @endif>
    <div id="tax-invoices" v-cloak data-detail-uri="" data-title="">
        <div class="row mt-2">
            <div v-for="(item, index) in tax_invoice_list" class="col-md-6 col-xl-4">
                <div class="block-customer"
                    @if ($view) @click="" @else  @click="billingById(item.id)" @endif :id="'check-customer-' + item.id"  >
                    <div class="block-content block-content-cunstom">
                        <div class="row">
                            <div class="col-12">
                                <p class="block-title-custom">@{{ item.tax_customer_name }}</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/user.png') }}" alt="user">
                                @{{ item.tax_customer_type_text }}
                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/usercode.png') }}" alt="user-code">
                                @{{ item.tax_tax_no }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/mail.png') }}" alt="mail">
                                @{{ item.tax_customer_email }}

                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/phone.png') }}" alt="phone">
                                @{{ item.tax_customer_tel }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/location.png') }}" alt="location">
                                @{{ item.tax_customer_province_text }}
                            </div>
                            <div class="col-6">
                                <img class="me-1" src="{{ asset('images/icons/zipcode.png') }}" alt="zipcode">
                                @{{ item.tax_customer_zipcode }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <img class="me-1" src="{{ asset('images/icons/location.png') }}" alt="location">
                                @{{ item.tax_customer_address }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 align-self-center mt-5">
            <button type="button" class="btn btn-primary" onclick="addTaxInvoice()" @if ($view) disabled  @endif id="openModal"><i
                    class="fa fa-lg fa-circle-plus m-2"></i></button>
        </div>
    </div>
    @include('admin.short-term-rental-info.modals.tax-invoice')
</div>
