@extends('admin.layouts.layout')
@section('page_title', __('invoices.st_rental_page_title'))

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
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_id" :value="$invoice_id" :list="null" :label="__('invoices.invoice_no')"
                                :optionals="['ajax' => true, 'default_option_label' => $invoice_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="$customer_id" :list="null" :label="__('invoices.customer_code_name')"
                                :optionals="['ajax' => true, 'default_option_label' => $invoice_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('invoices.license_plate')"
                                :optionals="['ajax' => true, 'default_option_label' => $license_plate]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')"/>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-6">
                            <label class="text-start col-form-label"
                                for="from_date">{{ __('invoices.contract_start_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_contract_start_date" name="from_contract_start_date"
                                        value="{{ $from_contract_start_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_contract_start_date" name="to_contract_start_date"
                                        value="{{ $to_contract_start_date }}" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-start col-form-label"
                                for="from_date">{{ __('invoices.contract_end_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_contract_end_date" name="from_contract_end_date"
                                        value="{{ $from_contract_end_date }}" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_contract_end_date" name="to_contract_end_date"
                                        value="{{ $to_contract_end_date }}" placeholder="{{ __('lang.select_date') }}"
                                        data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th width="1px">#</th>
                            <th>{{ __('invoices.invoice_no') }}</th>
                            <th>{{ __('invoices.customer_code_name') }}</th>
                            <th>{{ __('invoices.contract_start_date') }}</th>
                            <th>{{ __('invoices.contract_end_date') }}</th>
                            <th>{{ __('invoices.license_plate') }}</th>
                            <th>{{ __('invoices.instalment') }}</th>
                            <th class="text-center">{{ __('lang.status') }} </th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->customer_name }}</td>
                                    <td>{{ $d->contract_start_date }}</td>
                                    <td>{{ $d->contract_end_date }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->instalment }}</td>
                                    <td class="text-center">
                                        {!! $d->status
                                            ? badge_render(__('invoices.class_' . $d->status), __('invoices.status_' . $d->status))
                                            : null !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route(),
                                            'view_permission' => true,
                                        ])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>

@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')
