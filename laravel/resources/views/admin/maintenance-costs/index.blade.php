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
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no ?? null" :list="[]"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $worksheet_no_name,
                            ]"
                                                   :label="__('maintenance_costs.search_worksheet_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="center" :value="$center ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $center_name,
                            ]"
                                                   :label="__('maintenance_costs.search_center')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="geographie" :value="$geographie ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $geographie_name,
                            ]"
                                                   :label="__('maintenance_costs.search_geographie')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car" :value="$car ?? null" :list="[]" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $car_name,
                            ]"
                                                   :label="__('maintenance_costs.search_car')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_no" :value="$invoice_no ?? null"
                                                   :list="[]"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $invoice_no_name ?? null,
                            ]"
                                                   :label="__('maintenance_costs.table_invoice_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="in_center_date" :value="$in_center_date ?? null"
                                                :label="__('maintenance_costs.search_in_center_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="end_date" :value="$end_date ?? null"
                                                :label="__('maintenance_costs.search_end_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status ?? null" :list="$status_list ?? []"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('maintenance_costs.search_status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-export-excel">
                <i class="icon-document-download"></i>
                ดาวน์โหลด Excel
            </a>
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
                        <th>{{ __('maintenance_costs.search_worksheet_no') }}</th>
                        <th>{{ __('maintenance_costs.search_center') }}</th>
                        <th>{{ __('maintenance_costs.search_geographie') }}</th>
                        <th>{{ __('maintenance_costs.table_license_plate') }}</th>
                        <th>{{ __('maintenance_costs.table_chassis_no') }}</th>
                        <th>{{ __('maintenance_costs.table_engine_no') }}</th>
                        <th class="text-center">{{ __('maintenance_costs.table_invoice_no') }}</th>
                        <th>{{__('maintenance_costs.search_in_center_date')}}</th>
                        <th>{{__('lang.status')}}</th>
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
                                    {{$d?->repair?->worksheet_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->creditor?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->geographie_name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->repair?->car?->license_plate ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->repair?->car?->engine_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->repair?->car?->chassis_no ?? '-'}}
                                </td>
                                <td class="text-center">
                                    {{$d?->invoice_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->created_at ?? '-'}}
                                </td>
                                <td>
                                    {!! badge_render(
                                    __('maintenance_costs.status_' . MaintenanceStatusEnum::PENDING . '_class'),
                                    __('finance_contract.status_' . MaintenanceStatusEnum::PENDING),
                                    null,
                                    ) !!}
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.maintenance-cost.show', ['maintenance_cost' => $d->id]),
                                        'edit_route' => route('admin.maintenance-cost.edit', ['maintenance_cost' => $d]),
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
            {{--            {!! $list->appends(\Request::except('page'))->render() !!}--}}
        </div>
    </div>
    @include('admin.maintenance-costs.modals.modal-export-excel')
@endsection
@include('admin.maintenance-costs.scripts.script-export-excel')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'center',
    'url' => route('admin.util.select2-repair.creditor-services'),
])
@include('admin.components.select2-ajax', [
    'id' => 'geographie',
    'url' => route('admin.util.select2-repair.geographie'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car',
    'url' => route('admin.util.select2-repair.repair-car-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'worksheet_no',
    'url' => route('admin.util.select2-repair.repair-worksheet-no-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'invoice_no',
    'url' => route('admin.util.select2-repair.repair-invoice-no'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_center',
    'url' => route('admin.util.select2-repair.creditor-services'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_geographie',
    'url' => route('admin.util.select2-repair.geographie'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_car',
    'url' => route('admin.util.select2-repair.repair-car-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_worksheet_no',
    'url' => route('admin.util.select2-repair.repair-worksheet-no-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_invoice_no',
    'url' => route('admin.util.select2-repair.repair-invoice-no'),
])


