@extends('admin.layouts.layout')
@section('page_title', $page_title)

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
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_no" :value="$invoice_no" :list="null" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('check_billings.invoice_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="credit_note_no" :value="$credit_note_no" :list="null" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('check_billings.credit_note_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="null" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('check_billings.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_name" :value="$customer_name" :list="null"
                                :optionals="['placeholder' => __('lang.search_placeholder')]" :label="__('check_billings.customer_name')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_date">{{ __('check_billings.check_billing_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="from_date"
                                        name="from_date" value="{{ $from_billing_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_date"
                                        name="to_date" value="{{ $to_billing_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="period_no" :value="$period_no" :label="__('check_billings.period_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="schedule_billing" :value="$schedule_billing" :list="null"
                                :optionals="['placeholder' => __('lang.search_placeholder')]" :label="__('check_billings.schedule_billing')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="null" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
    @section('block_options_btn')
        <a class="btn btn-primary" href="#">
            {{ __('check_billings.download_excel_bill') }}
        </a>
        <a class="btn btn-primary" href="#">
            {{ __('check_billings.upload_file') }}
        </a>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_btn',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th style="width: 20%;">{{ __('check_billings.all_no') }}</th>
                        <th>{{ __('check_billings.license_plate') }}</th>
                        <th>{{ __('check_billings.customer_name') }}</th>
                        <th>{{ __('check_billings.schedule_billing') }}</th>
                        <th>{{ __('check_billings.check_billing_date') }}</th>
                        <th>{{ __('check_billings.period_no') }}</th>
                        <th>{{ __('check_billings.amount') }}</th>
                        <th>{{ __('lang.status') }}</th>
                        <th class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (sizeof($list) > 0)
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d?->invoice_no ?? '-' }}</td>
                                <td>-</td>
                                <td>{{ $d?->customer_name ?? '-' }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ $d->sub_total ? number_format($d->sub_total, 2) : '-' }}</td>
                                <td>
                                    @if ($d->status)
                                        {!! badge_render(
                                            __('check_billings.status_' . $d->status . '_class'),
                                            __('check_billings.status_' . $d->status),
                                            null,
                                        ) !!}
                                    @endif
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.check-billings.show', [
                                            'check_billing' => $d,
                                        ]),
                                        'edit_route' => route('admin.check-billings.edit', [
                                            'check_billing' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CheckBillingDate,
                                        'manage_permission' => Actions::Manage . '_' . Resources::CheckBillingDate,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
