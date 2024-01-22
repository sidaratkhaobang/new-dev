@extends('admin.layouts.layout')
@section('page_title', __('gps.alert') . __('gps.page_title_data'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        {{--        <div class="block-header"> --}}
        {{--            <h3 class="block-title">{{ __('car_tires.total_items') }}</h3> --}}
        {{--            <div class="block-options"> --}}
        {{--                <div class="block-options-item"> --}}
        {{--                    @can(Actions::Manage . '_' . Resources::GPSHistoricalDataAlert) --}}
        {{--                        <x-btns.add-new btn-text="{{ __('gps.add_new') }}" --}}
        {{--                            route-create="{{ route('admin.gps-historical-data-alerts.create') }}" /> --}}
        {{--                    @endcan --}}
        {{--                </div> --}}
        {{--            </div> --}}
        {{--        </div> --}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_list" :label="__('gps.worksheet_no_data')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="request_user" :value="$request_user" :list="$request_user_list"
                                :label="__('gps.request_user')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="request_date" :value="$request_date" :label="__('gps.request_date')" :optionals="['placeholder' => __('lang.select_date')]" />
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
    @section('block_options_list')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::GPSHistoricalDataAlert)
                    <x-btns.add-new btn-text="{{ __('gps.add_new') }}"
                        route-create="{{ route('admin.gps-historical-data-alerts.create') }}" />
                @endcan
            </div>
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>@sortablelink('worksheet_no', __('gps.worksheet_no_data'))</th>
                        <th>@sortablelink('created_by', __('gps.request_user'))</th>
                        <th>@sortablelink('created_at', __('gps.request_date'))</th>
                        <th class="text-center">@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->createdBy ? $d->createdBy->name : null }}</td>
                                <td>{{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : null }}</td>
                                <td class="text-center">
                                    {!! badge_render(__('gps.data_class_' . $d->status), __('gps.data_text_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @if (strcmp($d->status, GPSHistoricalDataStatusEnum::DRAFT) == 0)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-historical-data-alerts.show', [
                                                'gps_historical_data_alert' => $d,
                                            ]),
                                            'edit_route' => route('admin.gps-historical-data-alerts.edit', [
                                                'gps_historical_data_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSHistoricalDataAlert,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::GPSHistoricalDataAlert,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-historical-data-alerts.show', [
                                                'gps_historical_data_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSHistoricalDataAlert,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">" {{ __('lang.no_list') }} "</td>
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
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
