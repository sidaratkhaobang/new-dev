@extends('admin.layouts.layout')
@section('page_title', __('compensations.page_title'))

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
                            <x-forms.select-option id="compensation_id" :value="$compensation_id" :list="null" :label="__('compensations.worksheet_no')"
                                :optionals="['ajax' => true, 'default_option_label' => $worksheet_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="accident_id" :value="$accident_id" :list="null" :label="__('compensations.accident_worksheet_no')" 
                            :optionals="['ajax' => true, 'default_option_label' => $accident_worksheet_no]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="accident_date" :value="$accident_date" :label="__('compensations.date')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="end_date" :value="$end_date" :label="__('compensations.end_date')" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="complaint_type" :value="$complaint_type" :list="$complaint_type_list" :label="__('compensations.complaint_type')" />
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
                        <th style="width: 20%;">{{ __('compensations.worksheet_no') }}</th>
                        <th style="width: 15%;">{{ __('compensations.accident_worksheet_no') }}</th>
                        <th style="width: 20%;">{{ __('compensations.date') }}</th>
                        <th style="width: 20%;">{{ __('compensations.end_date') }}</th>
                        <th style="width: 20%;">{{ __('compensations.complaint_type') }}</th>
                        <th class="text-center" style="width: 20%;">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ $d->accident?->worksheet_no }}</td>
                                    <td>{{ $d->accident?->accident_date ? get_thai_date_format($d->accident->accident_date, 'd/m/Y') : '-' }}</td>
                                    <td>{{ $d->verdict_date ? get_thai_date_format($d->verdict_date, 'd/m/Y') : '-'  }}</td>
                                    <td>{{ $d->type ? __('compensations.complaint_type_' . $d->type) : null }}</td>
                                    <td class="text-center">{!! badge_render(__('compensations.class_' . $d->status), __('compensations.status_' . $d->status)) !!} </td>
                                    <td class="sticky-col text-center">
                                        <x-tables.dropdown 
                                            :view-route="route('admin.compensation-approves.show', ['compensation_approve' => $d])"
                                            :view-permission="Actions::View . '_' . Resources::Compensation" 
                                            :manage-permission="Actions::Manage . '_' . Resources::Compensation">
                                        </x-tables.dropdown>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
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
    'id' => 'compensation_id',
    'url' => route('admin.util.select2-compensation.worksheets'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accident_id',
    'url' => route('admin.util.select2-compensation.accidents'),
])
