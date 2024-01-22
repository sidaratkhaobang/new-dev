@extends('admin.layouts.layout')
@section('page_title', __('invoices.other_page_title'))

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
                            <x-forms.select-option id="branch_id" :value="$branch_id" :list="$branch_list" :label="__('invoices.branch')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_type" :value="$invoice_type" :list="$invoice_type_list" :label="__('invoices.invoice_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="$customer_id" :list="null" :label="__('invoices.buyer')"
                            :optionals="['ajax' => true, 'default_option_label' => $customer_text]" />
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
                            <th>{{ __('invoices.branch') }}</th>
                            <th>{{ __('invoices.invoice_type') }}</th>
                            <th>{{ __('invoices.buyer') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->invoice_no }}</td>
                                    <td>{{ $d->branch }}</td>
                                    <td>{{ $d->invoice_type }}</td>
                                    <td>{{ $d->customer_name }}</td>
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
