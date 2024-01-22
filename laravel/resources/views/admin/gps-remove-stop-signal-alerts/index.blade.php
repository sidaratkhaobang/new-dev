@extends('admin.layouts.layout')
@section('page_title', __('gps.alert') . __('gps.page_title_stop'))

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
        {{--                    @can(Actions::Manage . '_' . Resources::GPSRemoveStopSignalAlert) --}}
        {{--                        <x-btns.add-new btn-text="{{ __('gps.add_new') }}" --}}
        {{--                            route-create="{{ route('admin.gps-remove-stop-signal-alerts.create') }}" /> --}}
        {{--                    @endcan --}}
        {{--                </div> --}}
        {{--            </div> --}}
        {{--        </div> --}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_list" :label="__('gps.worksheet_no_gps')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$job_type" :list="$job_type_list" :label="__('gps.stop_job_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" :label="__('gps.chassis_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="vid" :value="$vid" :list="$vid_list" :label="__('gps.vid')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="remove_date" :value="null" :label="__('gps.remove_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="remove_status" :value="$remove_status" :list="$remove_list"
                                :label="__('gps.remove_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="stop_date" :value="null" :label="__('gps.stop_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="stop_status" :value="$stop_status" :list="$stop_list"
                                :label="__('gps.stop_status')" />
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
                @can(Actions::Manage . '_' . Resources::GPSRemoveStopSignalAlert)
                    <x-btns.add-new btn-text="{{ __('gps.add_new') }}"
                        route-create="{{ route('admin.gps-remove-stop-signal-alerts.create') }}" />
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
                        <th class="text-center" style="width: 70px;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                    name="selectAll">
                                <label class="form-check-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th style="width: 1px;">#</th>
                        <th style="width: 10%;">@sortablelink('worksheet_no', __('gps.worksheet_no_gps'))</th>
                        <th style="width: 10%;">@sortablelink('job_type', __('gps.stop_job_type'))</th>
                        <th style="width: 10%;">@sortablelink('license_plate', __('gps.license_plate'))</th>
                        <th style="width: 15%;">@sortablelink('chassis_no', __('gps.chassis_no'))</th>
                        <th style="width: 10%;">@sortablelink('vid', __('gps.vid'))</th>
                        <th class="text-center" style="width: 10%;">@sortablelink('remove_status', __('gps.remove_status'))</th>
                        <th style="width: 10%;">@sortablelink('remove_date', __('gps.remove_date'))</th>
                        <th class="text-center" style="width: 10%;">@sortablelink('stop_status', __('gps.stop_status'))</th>
                        <th style="width: 8%;">@sortablelink('stop_date', __('gps.stop_date'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox"
                                            value="" id="row_{{ $d->id }}" name="row_{{ $d->id }}">
                                        <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ __('gps.stop_job_type_' . $d->job_type) }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td>{{ $d->vid }}</td>
                                <td>
                                    @if (isset($d->remove_status))
                                        {!! badge_render(
                                            __('gps.remove_status_class_' . $d->remove_status),
                                            __('gps.remove_status_text_' . $d->remove_status),
                                        ) !!}
                                    @endif
                                </td>
                                <td>{{ $d->remove_date ? get_thai_date_format($d->remove_date, 'd/m/Y') : null }}</td>
                                <td>
                                    @if (isset($d->stop_status))
                                        {!! badge_render(__('gps.stop_status_class_' . $d->stop_status), __('gps.stop_status_text_' . $d->stop_status)) !!}
                                    @endif
                                </td>
                                <td>{{ $d->stop_date ? get_thai_date_format($d->stop_date, 'd/m/Y') : null }}</td>

                                <td class="sticky-col text-center">
                                    @if (strcmp($d->remove_status, GPSStopStatusEnum::WAIT_REMOVE_GPS) == 0 ||
                                            strcmp($d->stop_status, GPSStopStatusEnum::WAIT_STOP_SIGNAL) == 0)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-remove-stop-signal-alerts.show', [
                                                'gps_remove_stop_signal_alert' => $d,
                                            ]),
                                            'edit_route' => route('admin.gps-remove-stop-signal-alerts.edit', [
                                                'gps_remove_stop_signal_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::GPSRemoveStopSignalAlert,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-remove-stop-signal-alerts.show', [
                                                'gps_remove_stop_signal_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12" class="text-center">" {{ __('lang.no_list') }} "</td>
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

@push('scripts')
<script>
    $(document).ready(function() {
        var $selectAll = $('#selectAll');
        var $table = $('.table');
        var $tdCheckbox = $table.find('tbody input:checkbox');
        var tdCheckboxChecked = 0;

        $selectAll.on('click', function() {
            $tdCheckbox.prop('checked', this.checked);
        });

        $tdCheckbox.on('change', function(e) {
            tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
            $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
        })
    });
</script>
@endpush
