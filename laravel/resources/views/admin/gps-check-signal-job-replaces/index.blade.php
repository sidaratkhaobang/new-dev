@extends('admin.layouts.layout')
@section('page_title', __('gps.job') . __('gps.page_title_check'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::GPSCheckSignalReplacement)
            <button class="btn btn-primary send-check" onclick="openModalSendCheck()"><i class="fa fa-signal"></i>
                {{ __('gps.send_check_signal') }}</button>
        @endcan
    </div>
@endsection
@section('content')
    @include('admin.gps-check-signal-jobs.sections.btn-group')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" :label="__('gps.chassis_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list"
                                :label="__('gps.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="vid" :value="$vid" :list="$vid_list" :label="__('gps.vid')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="must_check_date" :value="$must_check_date" :label="__('gps.must_check_date')"
                                :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="check_date" :value="$check_date" :label="__('gps.check_date')" :optionals="['placeholder' => __('lang.select_date')]" />
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
                            <th style="width: 10%;">@sortablelink('chassis_no', __('gps.chassis_no'))</th>
                            <th style="width: 10%;">@sortablelink('license_plate', __('gps.license_plate'))</th>
                            <th style="width: 10%;">@sortablelink('vid', __('gps.vid'))</th>
                            <th style="width: 10%;">@sortablelink('rental_date', 'วันที่รับรถ')</th>
                            <th style="width: 10%;">@sortablelink('must_check_date', __('gps.must_check_date'))</th>
                            <th style="width: 10%;">@sortablelink('check_date', __('gps.check_date'))</th>
                            <th style="width: 8%;">@sortablelink('repair_date', __('gps.repair_date'))</th>
                            <th style="width: 8%;">@sortablelink('remark', __('gps.remark'))</th>
                            <th class="text-center" style="width: 8%;">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input type="checkbox" class="form-check-input form-check-input-each"
                                                name="row_{{ $d->id }}" id="row_{{ $d->id }}">
                                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->vid }}</td>
                                    <td>{{ $d->replacement_date }}</td>
                                    <td>{{ $d->must_check_date ? get_thai_date_format($d->must_check_date, 'd/m/Y') : null }}
                                    </td>
                                    <td>{{ $d->check_date ? get_thai_date_format($d->check_date, 'd/m/Y') : null }}</td>
                                    <td>{{ $d->repair_date ? get_thai_date_format($d->repair_date, 'd/m/Y') : null }}</td>
                                    <td>{{ $d->remark }}</td>
                                    <td>
                                        {!! badge_render(__('gps.status_class_' . $d->status), __('gps.status_text_' . $d->status)) !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                @can(Actions::View . '_' . Resources::GPSCheckSignalReplacement)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-check-signal-job-replaces.show', ['gps_check_signal_job_replace' => $d]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('config_approves.view') }}
                                                    </a>
                                                @endcan
                                                @can(Actions::Manage . '_' . Resources::GPSCheckSignalReplacement)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-check-signal-job-replaces.edit', ['gps_check_signal_job_replace' => $d]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
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
        @include('admin.gps-check-signal-jobs.modals.send-check-signal')
    </div>
@endsection

@include('admin.gps-check-signal-jobs.scripts.gps-check-job-script')
@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
