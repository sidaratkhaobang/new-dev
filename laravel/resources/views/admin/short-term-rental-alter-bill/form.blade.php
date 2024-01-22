@extends('admin.layouts.layout')

@if (isset($view))
    @section('page_title', __('lang.view') . __('short_term_rentals.sheet'))
@else
    @section('page_title', __('lang.edit') . __('short_term_rentals.sheet'))
@endif

@push('custom_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/invoice.css') }}">
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div id="rental-lines" v-cloak data-detail-uri="" data-title="">
                    {{-- @include('admin.components.progress-bar', [
                        'active' => [1, 1, 1, 1, 1, 0],
                    ]) --}}
                    <h4>{{ __('short_term_rentals.summary') }}</h4>
                    <hr>
                    <div class="form-group row push mb-5">
                        @include('admin.short-term-rental-alter-bill.sections.rental-line')
                    </div>
                    <div class="form-group row push mb-5">
                        <h4>{{ __('short_term_rentals.tax_invoice_detail') }}</h4>
                        <hr>
                        <div class="row push mb-5">
                            <div class="col-sm-12">
                                <x-forms.checkbox-inline id="check_customer_address" :list="[
                                    [
                                        'id' => 1,
                                        'name' => __('short_term_rentals.check_customer_address'),
                                        'value' => 1,
                                    ],
                                ]" :label="null"
                                    :value="[$check_customer_address]" />
                            </div>
                        </div>
                        <div id="toggle-tax-invoices"
                            @if ($check_customer_address === BOOL_FALSE) style="display: block" @else style="display: none" @endif>
                            <div class="row mt-2">
                                @foreach ($tax_invoice_list as $item)
                                    <div class="col-md-4 col-xl-4">
                                        <div class="block-customer">
                                            <div class="block-content block-content-cunstom">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="block-title-custom">{{ $item['tax_customer_name'] }}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/user.png') }}"
                                                            alt="user">
                                                        {{ $item['tax_customer_type_text'] }}
                                                    </div>
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/usercode.png') }}"
                                                            alt="user-code">
                                                        {{ $item['tax_tax_no'] }}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/mail.png') }}"
                                                            alt="mail">
                                                        {{ $item['tax_customer_email'] }}

                                                    </div>
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/phone.png') }}"
                                                            alt="phone">
                                                        {{ $item['tax_customer_tel'] }}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/location.png') }}"
                                                            alt="location">
                                                        {{ $item['tax_customer_province_text'] }}
                                                    </div>
                                                    <div class="col-6">
                                                        <img class="me-1" src="{{ asset('images/icons/zipcode.png') }}"
                                                            alt="zipcode">
                                                        {{ $item['tax_customer_zipcode'] }}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <img class="me-1" src="{{ asset('images/icons/location.png') }}"
                                                            alt="location">
                                                        {{ $item['tax_customer_address'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row push mb-5 mt-5">
                        <div class="col-sm-8">
                            @include('admin.short-term-rental-alter-bill.sections.payment')
                        </div>
                        <div class="col-sm-4">
                            <div class="row mb-3">
                                <div class="col-sm-6">รวมเป็นเงิน</div>
                                <div class="col-sm-6 text-end">@{{ summary.subtotal_text }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6">ส่วนลด Promotion</div>
                                <div class="col-sm-6 text-end">@{{ summary.discount_text }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6">ส่วนลด Voucher</div>
                                <div class="col-sm-6 text-end">@{{ summary.coupon_discount_text }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6">VAT</div>
                                <div class="col-sm-6 text-end">@{{ summary.vat_text }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6">ภาษี ณ ที่จ่าย</div>
                                <div class="col-sm-6 text-end">@{{ summary.withholding_tax_text }}</div>
                            </div>
                            <div class="row mb-3 mt-4">
                                <div class="col-sm-6 fs-lg">จำนวนเงินรวมทั้งสิ้น</div>
                                <div class="col-sm-6 fs-lg text-end">@{{ summary.total_text }}</div>
                            </div>
                            <hr>

                            <div class="form-check form-check-inline mt-2">
                                <input type="checkbox" class="form-check-input" 
                                    name="active_tax" id="active_tax" value="1" 
                                    @if ($rental_bill->check_withholding_tax) checked @endif
                                >หักภาษี ณ ที่จ่าย
                                <span id="active_sub" @if (!$rental_bill->check_withholding_tax) style="display: none;" @endif class="row push mb-2">
                                    <div class="col-sm-12">
                                        <x-forms.checkbox-inline id="withholding_tax" :list="$withholding_tax_list" :label="null"
                                            :value="[$rental_bill->withholding_tax_value]" :optionals="['label_class' => '', 'input_class' => 'checkbox-tax']" />
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <x-forms.hidden id="rental_id" :value="$rental_id" />
                    <x-forms.hidden id="rental_bill_id" :value="$rental_bill_id" />
                </div>
                @include('admin.short-term-rental-summary.modals.rental-line')
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        {{-- <a class="btn btn-danger btn-cancel-status">{{ __('lang.cancel') }}</a> --}}
                        @if (!isset($view))
                            @can(Actions::Manage . '_' . Resources::ShortTermRental)
                                <a class="btn btn-secondary"
                                    href="{{ route('admin.short-term-rental.alter.edit-bill', ['rental_id' => $rental_id]) }}">{{ __('lang.back') }}</a>
                                <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save') }}</button>
                            @endcan
                        @else
                            @if (isset($redirect))
                                <a class="btn btn-secondary" href="{{ $redirect }}">{{ __('lang.back') }}</a>
                            @else
                                @can(Actions::View . '_' . Resources::ShortTermRental)
                                    <a class="btn btn-secondary"
                                        href="{{ route('admin.short-term-rental.alter.view-bill', ['rental_id' => $rental_id]) }}">{{ __('lang.back') }}</a>
                                @endcan
                            @endif
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.summary.store'),
])
@include('admin.short-term-rental-summary.scripts.rental-line-script')
@include('admin.short-term-rentals.scripts.update-cancel-status')

@include('admin.components.select2-ajax', [
    'id' => 'payment_gateway',
    'url' => route('admin.util.select2-rental.payment-gateways'),
])
{{-- @include('admin.components.select2-ajax', [
    'id' => 'payment_gateway',
    'url' => route('admin.util.select2-rental.payment-gateways'),
]) --}}
@include('admin.components.select2-ajax', [
    'id' => 'payment_status',
    'url' => route('admin.util.select2-rental.payment-statuses'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'ref_sheet_image',
    'max_files' => 100,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $ref_sheet_image,
])

@include('admin.short-term-rental-summary.scripts.gen-2c2p-payment-script')
@push('scripts')
    <script>
        rental_bill_status = '{{ $d->status }}';
        if (rental_bill_status == '{{ RentalStatusEnum::PAID }}') {
            $('#payment_gateway').prop('disabled', true);
        }

        $status = '{{ isset($view) }}';
        if ($status) {
            $('#payment_remark').prop('disabled', true);
            $('#payment_gateway').prop('disabled', true);
            $('#payment_status').prop('disabled', true);
            $('#active_tax').prop('disabled', true);
            $('input[name="withholding_tax[]"]').prop('disabled', true);
            $('#payment_date').prop('disabled', true);
        }

        $check_customer_address = '{{ $check_customer_address }}';
        $('[name="check_customer_address[]"]').prop('disabled', true);

        if ($check_customer_address === '{{ BOOL_FALSE }}') {
            $('#toggle-tax-invoices').show();
        }

        document.getElementById('active_tax').onclick = function() {
        if (!this.checked) {
            $('[name="withholding_tax[]"]').prop('checked', false);
        }
        toggleSub(this, 'active_sub');
    };

    function toggleSub(box, id) {
        var el = document.getElementById(id);
        if (box.checked) {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }

    $('input[name="withholding_tax[]"]').change(function() {
        var checked = this.checked;
        $('[name="withholding_tax[]"]').prop('checked', false);
        $(this).prop('checked', checked);

        var val = this.checked ? this.value : 0;
        total = addRentalVue.getAllofSubtotal();
        addRentalVue.setWithHoldingTax(val);
        addRentalVue.calculateWithHoldingTax(val, total);
    });

    $('#active_tax').change(function() {
        is_check_withholding_tax = $(this).is(':checked');
        addRentalVue.isWithHoldingTax(is_check_withholding_tax);
        if (!this.checked) {
            var total = addRentalVue.getAllofSubtotal();
            addRentalVue.calculateWithHoldingTax(0, total);
        }
    });
    </script>
@endpush
