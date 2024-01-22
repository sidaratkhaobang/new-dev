@extends('admin.layouts.layout')
@section('page_title', __('gps.job') . __('gps.page_title_stop'))
@push('custom_styles')
    <style>
        .btn-purple {
            color: #fff;
            background-color: #824DF3;
            border-color: #824DF3;
        }

        .btn-purple:hover {
            color: #fff;
            background-color: #824DF3;
            border-color: #824DF3;
        }
    </style>
@endpush
@section('content')
@include('admin.gps-remove-stop-signal-jobs.sections.btn-group')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
'text' =>   __('lang.search')    ,
'block_icon_class' => 'icon-search',
'is_toggle' => true
])
        <div class="block-content">
{{--                        <div class="block-header">--}}
{{--                            <h4>{{ __('car_tires.total_items') }}</h4>--}}
{{--                            <div class="block-options">--}}
{{--                                <div class="block-options-item">--}}
{{--                                    @can(Actions::Manage . '_' . Resources::GPSStopSignalJob)--}}
{{--                                        <button class="btn btn-purple" onclick="openModalUpdateStop()"><i class="fa fa-broadcast-tower"></i>--}}
{{--                                            {{ __('gps.update_stop_gps') }}</button>--}}
{{--                                    @endcan--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_list"
                                                   :label="__('gps.worksheet_no_gps')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$job_type" :list="$job_type_list"
                                                   :label="__('gps.stop_job_type')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list"
                                                   :label="__('gps.chassis_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="vid" :value="$vid" :list="$vid_list" :label="__('gps.vid')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="stop_date" :value="null" :label="__('gps.stop_date')"
                                                :optionals="['placeholder' => __('lang.select_date')]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="stop_status_id" :value="$stop_status_id" :list="$stop_list"
                                                   :label="__('gps.stop_status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>


        </div>
        @include('admin.gps-stop-signal-jobs.modals.update-stop')
    </div>

    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::GPSStopSignalJob)
                        <button class="btn btn-purple" onclick="openModalUpdateStop()"><i class="fa fa-broadcast-tower"></i>
                            {{ __('gps.update_stop_gps') }}</button>
                    @endcan
                </div>
        @endsection
        @include('admin.components.block-header', [
         'text' => __('lang.total_list'),
         'block_option_id' => '_list'
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
                        <th class="text-center" style="width: 10%;">@sortablelink('stop_status', __('gps.stop_status'))</th>
                        <th style="width: 8%;">@sortablelink('stop_date', __('gps.stop_date'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox" value=""
                                               id="row_{{ $d->id }}" name="row_{{ $d->id }}">
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
                                    @if (isset($d->stop_status))
                                        {!! badge_render(__('gps.stop_status_class_' . $d->stop_status), __('gps.stop_status_text_' . $d->stop_status)) !!}
                                    @endif
                                </td>
                                <td>{{ $d->stop_date ? get_thai_date_format($d->stop_date, 'd/m/Y') : null }}</td>

                                <td class="sticky-col text-center">
                                    @if (strcmp($d->stop_status, GPSStopStatusEnum::ALERT_STOP_SIGNAL) == 0)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-stop-signal-jobs.show', [
                                                'gps_stop_signal_job' => $d,
                                            ]),
                                            'edit_route' => route('admin.gps-stop-signal-jobs.edit', [
                                                'gps_stop_signal_job' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::GPSStopSignalJob,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::GPSStopSignalJob,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-stop-signal-jobs.show', [
                                                'gps_stop_signal_job' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::GPSStopSignalJob,
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
        $(document).ready(function () {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function () {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function (e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });

        function openModalUpdateStop() {
            var enum_alert_stop = '{{ \App\Enums\GPSStopStatusEnum::ALERT_STOP_SIGNAL }}';
            var check_list = @json($list);
            var arr_check = [];
            updateStopVue.removeAll();
            if (check_list.data.length > 0) {
                check_list.data.forEach(function (item, index) {
                    this_checkbox = $('input[name="row_' + item.id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    if (is_check) {
                        if (item.stop_status == enum_alert_stop) {
                            updateStopVue.addByDefault(item);
                        }
                    }
                });
            }
            $('#modal-update-stop').modal('show');
        }
    </script>
@endpush