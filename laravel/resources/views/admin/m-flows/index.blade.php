@extends('admin.layouts.layout')
@section('page_title', __('lang.list') . ' ' . __('m_flows.page_title'))
@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::MFlow)
            <x-btns.add-new btn-text="{{ __('m_flows.add_new') }}" route-create="{{ route('admin.m-flows.create') }}" />
        @endcan
    </div>
@endsection
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
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="m_flow_id" :value="$m_flow_id" :list="null" :label="__('m_flows.worksheet_no')"
                                :optionals="['ajax' => true, 'default_option_label' => $worksheet_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('m_flows.license_plate')"
                                :optionals="['ajax' => true, 'default_option_label' => $license_plate]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="expressway_id" :value="$expressway_id" :list="null" :label="__('m_flows.station_place')"
                                :optionals="['ajax' => true, 'default_option_label' => $m_flow_station_text]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="overdue_date" :value="$offense_date" :label="__('m_flows.overdue_date')" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('locations.total_items'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th style="width: 20%;">{{ __('m_flows.worksheet_no') }}</th>
                            <th style="width: 15%;">{{ __('m_flows.license_plate') }}</th>
                            <th style="width: 20%;">{{ __('m_flows.station_place') }}</th>
                            <th style="width: 20%;">{{ __('m_flows.overdue_date') }}</th>
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
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->expressway_name }}</td>
                                    <td>{{ $d->offensedate }}</td>
                                    <td class="text-center">{!! badge_render(__('m_flows.class_' . $d->status), __('m_flows.status_' . $d->status)) !!} </td>
                                    <td class="sticky-col text-center">
                                        @if (in_array($d->status, [MFlowStatusEnum::CLOSE, MFlowStatusEnum::COMPLETE]))
                                            <x-tables.dropdown :view-route="route('admin.m-flows.show', ['m_flow' => $d])" :view-permission="Actions::View . '_' . Resources::MFlow">
                                            </x-tables.dropdown>
                                        @else
                                            <x-tables.dropdown :view-route="route('admin.m-flows.show', ['m_flow' => $d])" :edit-route="route('admin.m-flows.edit', ['m_flow' => $d])" :view-permission="Actions::View . '_' . Resources::MFlow"
                                                :manage-permission="Actions::Manage . '_' . Resources::MFlow">
                                            </x-tables.dropdown>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
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
    'id' => 'm_flow_id',
    'url' => route('admin.util.select2-m-flow.worksheets'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-m-flow.cars'),
])

@include('admin.components.select2-ajax', [
    'id' => 'expressway_id',
    'url' => route('admin.util.select2-m-flow.m-flow-stations'),
])
