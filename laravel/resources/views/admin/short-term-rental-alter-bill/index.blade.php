@extends('admin.layouts.layout')
@if (isset($view))
    @section('page_title', __('lang.view') . __('short_term_rentals.sheet'))
@else
    @section('page_title', __('lang.edit') . __('short_term_rentals.sheet'))
@endif

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.short-term-rentals.sections.rental-btn-group', [
                    'rental_id' => $rental_id,
                    'page' => RentalStateEnum::SUMMARY_EDIT,
                ])
                <h4>{{ __('short_term_rentals.summary') }}</h4>
                <hr>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th style="width: 1%;">#</th>
                                <th>{{ __('short_term_rentals.summary') }}</th>
                                <th style="width: 100px;">{{ __('lang.status') }}</th>
                                <th class="text-end" style="width: 20%;">{{ __('short_term_rentals.total') }}</th>
                                <th class="text-center">{{ __('short_term_rentals.quotation') }}</th>
                                <th class="text-center">{{ __('quotations.bill_payment') }}</th>
                                <th class="text-center">{{ __('short_term_rentals.2c2p_link') }}</th>
                                <th style="width: 100px;" class="sticky-col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sub_index = 1;
                            @endphp
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ __('short_term_rentals.bill_' . $d->bill_type) }}</td>
                                    <td>
                                        {!! badge_render(__('short_term_rentals.class_' . $d->status), __('short_term_rentals.status_' . $d->status)) !!}
                                    </td>
                                    {{-- <td>{{ __('short_term_rentals.status_' . $d->status) }}</td> --}}
                                    <td class="text-end">{{ number_format($d->total, 2) }}</td>
                                    <td class="text-center">
                                        @if (!in_array($d->status, [RentalStatusEnum::DRAFT]))
                                            <a href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $d->id]) }}"
                                                target="_blank">
                                                {{ $d->quotation ? $d->quotation->qt_no : '' }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (!in_array($d->status, [RentalStatusEnum::DRAFT]))
                                            <a target="_blank" href="{{ route('admin.quotations.short-term-rental-payment-pdf', ['rental_bill_id' => $d->id]) }}" >
                                                <i class="icon-document-download text-primary"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($d->payment_url && !in_array($d->status, [RentalStatusEnum::PAID]))
                                            <a href="{{ $d->payment_url }}" target="_blank">
                                                <i class="fa fa-arrow-up-right-from-square"></i> Link
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (isset($view))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route(
                                                    'admin.short-term-rental.alter.view-bill-summary',
                                                    ['rental_bill_id' => $d->id]),
                                                'view_permission' =>
                                                    Actions::View . '_' . Resources::ShortTermRental,
                                            ])
                                        @else
                                            @include('admin.components.dropdown-action', [
                                                // 'view_route' => route('admin.short-term-rentals.show', ['short_term_rental' => $d]),
                                                'edit_route' => route(
                                                    'admin.short-term-rental.alter.bill-summary',
                                                    [
                                                        'rental_bill_id' => $d->id,
                                                    ]),
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::ShortTermRental,
                                            ])
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if (!isset($view))
                    @can(Actions::Manage . '_' . Resources::ShortTermRental)
                        <div class="row mb-6" id="rental-bill">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="openRentalBillModal()">{{ __('lang.add') }}</button>
                            </div>
                        </div>
                    @endcan
                @endif
                @include('admin.short-term-rental-alter-bill.modals.rental-bill')

                <x-forms.hidden id="rental_id" :value="$rental_id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        @if (!isset($view))
                            @can(Actions::Manage . '_' . Resources::ShortTermRental)
                                <a class="btn btn-secondary"
                                    href="{{ route('admin.short-term-rental.alter.edit-driver', ['rental_id' => $rental_id]) }}">{{ __('lang.back') }}</a>
                                <button type="button" class="btn btn-info btn-save-form">{{ __('lang.save') }}</button>
                            @endcan
                        @else
                            @can(Actions::View . '_' . Resources::ShortTermRental)
                                <a class="btn btn-secondary"
                                    href="{{ route('admin.short-term-rental.alter.view-driver', ['rental_id' => $rental_id]) }}">{{ __('lang.back') }}</a>
                            @endcan
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.short-term-rental.alter.store-bill'),
])
@include('admin.short-term-rental-alter-bill.scripts.rental-bill-script')
@include('admin.short-term-rental-summary.scripts.rental-line-script')
@include('admin.short-term-rental-info.scripts.tax-invoice-script')



@push('scripts')
    <script>
        $('input[name="check_customer_address[]"]').on("click", function() {
            if (!$('input[name="check_customer_address[]"]').prop('checked')) {
                $("#is_customer_address").val('0');
                var customer_id = '{{ $customer_id }}';
                getDataCustomerBillingAddress(customer_id);
                $('#toggle-tax-invoices').show('slow');

            } else {
                $("#is_customer_address").val('1');
                $("#customer_billing_address_id").val('');
                $('#toggle-tax-invoices').hide('slow');
                addTaxInvoiceVue.resetData();
            }
        });
    </script>
@endpush
