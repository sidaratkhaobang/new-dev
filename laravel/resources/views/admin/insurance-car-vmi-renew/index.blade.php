@extends('admin.layouts.layout')
@section('page_title', $page_title)

@push('styles')
    <style>
        .seperator {
            font-size: xx-large;
            color: #CBD4E1;
            vertical-align: sub;
            font-weight: 100;
        }
    </style>
@endpush
@section('block_options_2')
    @can(Actions::View . '_' . Resources::InsuranceCarVmiRenew)
        <div class="block-options-item">
            <button class="btn btn-success" onclick=" exportVMIList()">
                <i class="icon-document-download me-1"></i> {{ __('cmi_cars.download') }}</button>
        </div>
        <div class="block-options-item seperator">|
        </div>
    @endcan

    @can(Actions::Manage . '_' . Resources::InsuranceCarVmiRenew)
        <div class="block-options-item">
            <div class="file btn btn-primary">
                <i class="icon-document-upload me-1"></i>
                {{ __('cmi_cars.excel_upload') }}
                <input id="upload" type=file name="file[]" />
            </div>
        </div>
    @endcan

{{--    @can(Actions::Manage . '_' . Resources::VMI)--}}
{{--        <div class="block-options-item">--}}
{{--            <button class="btn btn-primary" onclick="opencreateVMIModal()">--}}
{{--                <i class="icon-clipboard-text me-1"></i>--}}
{{--                {{ __('vmi_cars.make_vmi') }}</button>--}}
{{--        </div>--}}
{{--    @endcan--}}
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'block_option_id' => '_1',
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <x-forms.hidden id="renew" :value="$type"/>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$vmi_id" id="vmi_id" :list="null"
                                :label="__('vmi_cars.worksheet_no')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $vmi_worksheet_no,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$license_plate" id="license_plate" :list="null"
                                :label="__('cmi_cars.license_plate')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $license_plate_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$insurer_id" id="insurer_id" :list="null"
                                :label="__('cmi_cars.insurance_company')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $insurer_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$status" id="status" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$po_id" id="po_id" :list="null"
                                :label="__('cmi_cars.po_no')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $po_no,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$lot" id="lot" :list="null"
                                :label="__('cmi_cars.lot')" :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $lot,
                                ]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
        @include('admin.insurance-car-vmi-renew.modals.start-vmi')
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_2',
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th class="text-center" style="width: 70px;">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                        name="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th>@sortablelink('lot_number', __('cmi_cars.lot'))</th>
                            <th>@sortablelink('worksheet_no', __('vmi_cars.worksheet_no'))</th>
                            <th>{{ __('cmi_cars.vmi_type') }}</th>
                            <th>{{ __('cmi_cars.year_insurance') }}</th>
                            <th>{{ __('cmi_cars.po_no') }}</th>
                            <th>{{ __('cmi_cars.license_plate_chassis') }}</th>
                            <th>{{ __('cmi_cars.insurance_company') }}</th>
                            <th>{{ __('cmi_cars.renter') }}</th>
                            <th>{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox" value="{{ $d->id }}"
                                            id="row[{{ $d->id }}]" name="row_checkbox">
                                    </div>
                                </td>
                                <td>{{ $d->lot_number }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ __('cmi_cars.type_' . $d->type) }}</td>
                                <td>{{ $d->year }}</td>
                                <td>{{ $d->job?->po_no }}</td>
                                <td>
                                    {{ $d->car?->license_plate ?? '-' }} /
                                    {{ $d->car?->chassis_no ?? '-' }} 
                                </td>
                                <td>{{ $d->insurer?->insurance_name_th }}</td>
                                <td>{{ $d->rental_customer }}</td>
                                <td>{!! badge_render(
                                    __('cmi_cars.class_' . $d->status),
                                    __('cmi_cars.status_' . $d->status),
                                ) !!}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.insurance-car-vmi-renew.sections.dropdown-actions')
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11" class="text-center">" {{ __('lang.no_list') }} "</td>
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
@include('admin.insurance-car-vmi-renew.scripts.start-vmi-script')
@include('admin.insurance-car-vmi-renew.scripts.export-vmi-script')
@include('admin.insurance-car-vmi-renew.scripts.import-vmi-script')

@include('admin.components.select2-ajax', [
    'id' => 'vmi_id',
    'url' => route('admin.util.select2-vmi.vmi-worksheets'),
     'parent_id' => 'renew',
])

@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2-vmi.vmi-license-plates'),
    'parent_id' => 'renew',
])

@include('admin.components.select2-ajax', [
    'id' => 'insurer_id',
    'url' => route('admin.util.select2-vmi.vmi-insurers'),
    'parent_id' => 'renew',
])

@include('admin.components.select2-ajax', [
    'id' => 'po_id',
    'url' => route('admin.util.select2-vmi.vmi-pos'),
    'parent_id' => 'renew',
])

@include('admin.components.select2-ajax', [
    'id' => 'lot',
    'url' => route('admin.util.select2-vmi.vmi-lots'),
    'parent_id' => 'renew',
])

