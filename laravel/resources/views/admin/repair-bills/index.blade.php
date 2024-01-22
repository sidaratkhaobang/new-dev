@extends('admin.layouts.layout')
@section('page_title', $page_title)
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
                            <x-forms.select-option id="bill_no" :value="$bill_no" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $bill_no_name,
                            ]"
                                                   :label="__('repair_bills.search_bill_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="bill_recipient" :value="$bill_recipient" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $bill_recipient_name,
                            ]"
                                                   :label="__('repair_bills.search_bill_recipient')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="center" :value="$center" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $center_name,
                            ]"
                                                   :label="__('repair_bills.search_center')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="department" :value="$department" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $department_name,
                            ]"
                                                   :label="__('repair_bills.search_department')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="billing_date" :value="$billing_date"
                                                :label="__('repair_bills.search_billing_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('repair_bills.search_status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    {{--    Table Section --}}
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <x-btns.add-new btn-text="{{ __('lang.add_data') }}"
                            route-create="{{ route('admin.repair-bills.create') }}"/>
        @endsection
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>{{ __('repair_bills.search_bill_no') }}</th>
                        <th>{{ __('repair_bills.search_bill_recipient') }}</th>
                        <th>{{ __('repair_bills.search_center') }}</th>
                        <th>{{ __('repair_bills.search_department') }}</th>
                        <th>{{ __('repair_bills.search_billing_date') }}</th>
                        <th>{{ __('repair_bills.bill') }}</th>
                        <th class="text-center">{{ __('repair_bills.search_status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (!$list->isEmpty())
                        @foreach ($list as $key => $d)
                            <tr>
                                <td>
                                    {{ $list->currentPage() * $list->perPage() - $list->perPage() + 1 + $key }}
                                </td>
                                <td>
                                    {{$d?->worksheet_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->user?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->creditor?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->geographie_name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->billing_date ?? '-'}}
                                </td>
                                <td>
                                    <a target="_blank"
                                       href="{{route('admin.repair-bills.print-pdf',['repair_bill_id' => $d])}}">เอกสารใบวางบิล</a>
                                </td>
                                <td class="text-center">
                                    {!! badge_render(
                                    __('finance_contract.status_' . $d?->status . '_class'),
                                    __('finance_contract.status_' . $d?->status),
                                    null,
                                    ) !!}
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.repair-bills.show', ['repair_bill' => $d->id]),
                                        'edit_route' => route('admin.repair-bills.edit', ['repair_bill' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::RepairBill,
                                        'manage_permission' => Actions::Manage . '_' . Resources::RepairBill,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
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
@include('admin.components.select2-ajax', [
    'id' => 'bill_recipient',
    'url' => route('admin.util.select2-repair.bill-recipient'),
])
@include('admin.components.select2-ajax', [
    'id' => 'department',
    'url' => route('admin.util.select2-repair.geographie'),
])
@include('admin.components.select2-ajax', [
    'id' => 'center',
    'url' => route('admin.util.select2-repair.creditor-services'),
])
@include('admin.components.select2-ajax', [
    'id' => 'bill_no',
    'url' => route('admin.util.select2-repair.repair-bill-no'),
])


