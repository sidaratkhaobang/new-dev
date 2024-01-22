@extends('admin.layouts.layout')
@section('page_title', __('litigations.approve_page_title'))
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="accuser_defendant" :value="$accuser_defendant" :label="__('litigations.plaintiff_defendent')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="null" :label="__('litigations.worksheet_no')"
                                :optionals="['ajax' => true, 'default_option_label' => $worksheet_no_text]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="title_id" :value="$title_id" :list="null" :label="__('litigations.charge')"
                                :optionals="['ajax' => true, 'default_option_label' => $title_text]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="case_type" :value="$case_type" :list="$case_type_list" :label="__('litigations.case_type')" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="due_date" :value="$due_date" :label="__('litigations.prescription_date')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="tls_type" :value="$tls_type" :list="$tls_type_list" :label="__('litigations.tls_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('locations.total_items') ,

        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        {{-- <th style="width: 25%;">@sortablelink('worksheet_no', __('litigations.worksheet_no'))</th> --}}
                        <th style="width: 15%;">{{ __('litigations.worksheet_no') }}</th>
                        <th style="width: 15%;">{{ __('litigations.charge') }}</th>
                        <th style="width: 15%;">{{ __('litigations.case_type') }}</th>
                        {{-- <th style="width: 25%;">@sortablelink('charge', __('litigations.charge'))</th> --}}
                        {{-- <th style="width: 15%;">@sortablelink('case_type', __('litigations.case_type'))</th> --}}
                        <th style="width: 15%;">{{ __('litigations.tls_type') }}</th>
                        <th style="width: 15%;">{{ __('litigations.plaintiff_defendent') }}</th>
                        <th style="width: 15%;">{{ __('litigations.prescription_date') }}</th>
                        <th  class="text-center" style="width: 15%;">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ $d->title }}</td>
                                    <td>{{ $d->case_type ? __('litigations.case_type_' . $d->case_type) : null }}</td>
                                    <td>{{ $d->tls_type ? __('litigations.tls_type_' . $d->tls_type) : null }}</td>
                                    <td>{{ $d->accuser_defendant }}</td>
                                    <td>{{ $d->due_date ? get_thai_date_format($d->due_date, 'd/m/Y') : null }}</td>
                                    <td class="text-center">
                                        {!! $d->status ? badge_render(__('litigations.class_' . $d->status), __('litigations.status_' . $d->status)) : null !!} </td>
                                    <td class="sticky-col text-center">
                                        <x-tables.dropdown 
                                            :view-route="route('admin.litigation-approves.show', ['litigation_approve' => $d])" 
                                            :view-permission="Actions::View . '_' . Resources::LitigationApprove" >
                                        </x-tables.dropdown>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
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
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'worksheet_no',
    'url' => route('admin.util.select2-litigation.worksheets'),
])

@include('admin.components.select2-ajax', [
    'id' => 'title_id',
    'url' => route('admin.util.select2-litigation.titles'),
])