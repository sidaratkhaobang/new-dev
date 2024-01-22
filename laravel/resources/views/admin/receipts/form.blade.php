@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('btn-nav')
    @if (strcmp($d->status, ReceiptStatusEnum::ACTIVE) == 0)
        @can(Actions::Manage . '_' . Resources::Receipt)
            <nav class="flex-sm-00-auto ml-sm-3">
                <a target="_blank" href="{{ route('admin.receipts.pdf', ['receipt' => $d]) }}" class="btn btn-primary">
                    {{ __('receipts.print_receipt') }}
                </a>
            </nav>
        @endcan
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/invoice.css') }}">
    <style>
        .table-vcenter th {
            /*border-radius: 10px;*/
            /*border-color: red;*/
            /*border-width: 2px;*/
            /*border-style: solid;*/
        }

        .table-vcenter td {
            border-bottom: 1px solid #C2C2C2 !important;
        }
        /*tr:last-child td {*/
        /*    border-bottom: 1px solid #C2C2C2 !important;*/
        /*}*/
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <h4>{{ __('receipts.customer_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line :value="$d->customer_code" id="customer_code"
                                                :label="__('receipts.customer_code')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="customer_name" :value="$d->customer_name"
                                                :label="__('receipts.customer_name')"/>
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="customer_tax" :value="$d->customer_tax_no"
                                                :label="__('receipts.customer_tax')"/>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="customer_address" :value="$d->customer_address"
                                                    :label="__('receipts.customer_address')"/>
                    </div>
                </div>
                <h4>{{ __('receipts.list_table') }}</h4>
                <hr>
                <table class="table table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th class="text-start">{{ __('lang.seq') }}</th>
                        <th class="text-start">{{ __('receipts.reference_no') }}</th>
                        <th class="text-start">{{ __('receipts.date') }}</th>
                        <th class="text-start" colspan="2">{{ __('receipts.list') }}</th>
                        <th class="text-end">{{ __('receipts.amount') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="text-start">1</td>
                        <td class="text-start">{{ $d->parent ? $d->parent->worksheet_no : null }}</td>
                        <td class="text-start">
                            {{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : '-' }}</td>
                        <td class="text-start" colspan="2">{{ __('receipts.receipt_type_' . $d->receipt_type) }}
                        </td>
                        <td class="text-end">{{ number_format($d->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" class="text-start">
                            รวมมูลค่า
                        </td>
                        <td class="text-end">
                            {{ number_format($d->subtotal, 2) }}
                        </td>
                    </tr>
                                            <tr>
                                                <td rowspan="4" colspan="4">
                                                </td>
                                                <td class="text-start">รวมมูลค่า</td>
                                                <td class="text-end">{{ number_format($d->subtotal, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="text-start">ภาษีมูลค่าเพิ่ม 7%</td>
                                                <td class="text-end">{{ number_format($d->vat, 2) }}</td>
                                            </tr>
                                            <tr>

                                                <td class="text-start">ภาษีหัก ณ ที่จ่าย</td>
                                                <td class="text-end">{{ number_format($d->withholding_tax, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td class="text-start">จำนวนเงินรวม</td>
                                                <td class="text-end">{{ number_format($d->total, 2) }}</td>
                                            </tr>
                    </tbody>
                </table>
                @if (strcmp($d->status, ReceiptStatusEnum::ACTIVE) == 0)
                    <h4 class="mt-4">{{ __('receipts.edit_address') }}</h4>
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
                                                     :value="[$check_customer_address]"/>
                        </div>
                    </div>
                    @include('admin.receipts.tax-invoice')
                @endif
                <x-forms.hidden id="id" :value="$d->id"/>
                <x-forms.hidden id="check_customer_address" :value="$check_customer_address"/>
                <x-forms.hidden id="customer_billing_address_id" :value="$customer_billing_address_bill"/>
                <x-forms.hidden id="customer_id" :value="$d->customer_id"/>
                <x-forms.submit-group
                    :optionals="['url' => 'admin.receipts.index', 'view' => empty($view) ? null : $view]"/>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.receipts.store'),
])

@include('admin.short-term-rental-info.scripts.tax-invoice-script')

@push('scripts')
    <script>
        $('#customer_code').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#customer_tax').prop('disabled', true);
        $('#customer_address').prop('disabled', true);

        var check_customer_address = '{{ $check_customer_address }}';
        var customer_billing_address_bill = '{{ $customer_billing_address_bill }}';
        var tax_invoice_list = @json($tax_invoice_list);
        $status = '{{ $view }}';

        if ($status) {
            $('[name="check_customer_address[]"]').prop('disabled', true);
        }
        var customer_id = $('#customer_id').val();
        if (!customer_id) {
            $('[name="check_customer_address[]"]').prop('disabled', true);
        }

        var tax_invoice = tax_invoice_list.filter(obj => obj.id == customer_billing_address_bill);
        tax_invoice.forEach((e) => {
            $('#check-customer-' + e.id).removeClass('block-customer').addClass('customer-active');
        });

        $('#toggle-tax-invoices').hide();
        if (check_customer_address === '{{ BOOL_FALSE }}') {
            $('#toggle-tax-invoices').show();
        }

        $('input[name="check_customer_address[]"]').on("click", function () {
            if (!$('input[name="check_customer_address[]"]').prop('checked')) {
                $("#check_customer_address").val('0');
                var customer_id = $('#customer_id').val();
                getDataCustomerBillingAddress(customer_id);
                $('#toggle-tax-invoices').show('slow');

            } else {
                $("#check_customer_address").val('1');
                $("#customer_billing_address_id").val('');
                $('#toggle-tax-invoices').hide('slow');
                addTaxInvoiceVue.resetData();
            }
        });
    </script>
@endpush
