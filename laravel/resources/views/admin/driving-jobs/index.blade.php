@extends('admin.layouts.layout')
@section('page_title', __('driving_jobs.page_title'))
@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush
@section('content')
<x-blocks.block-search>
    <form action="" method="GET" id="form-search">
        <div class="form-group row push">
            <div class="col-sm-3">
                <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                <input type="text" id="s" name="s" class="form-control"
                    placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list"
                    :label="__('driving_jobs.worksheet_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="work_status" :value="$work_status" :list="$work_status_list" :label="__('driving_jobs.status')" />
            </div>
            {{-- <div class="col-sm-3">
                <x-forms.select-option id="job_type" :value="$job_type" :list="$job_list" :label="__('driving_jobs.worksheet_type')" />
            </div> --}}
            <div class="col-sm-3">
                <x-forms.select-option id="self_drive_type" :value="$self_drive_type" :list="$self_drive_types"
                    :label="__('driving_jobs.job_type')" />
            </div>
        </div>
        <div class="form-group row push">

            <div class="col-sm-3">
                <x-forms.select-option id="driver_id" :value="$driver_id" :list="null" :label="__('driving_jobs.driver_name')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $driver_name,
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="is_confirm_wage" :value="$is_confirm_wage" :list="$is_confirm_wages"
                    :label="__('driving_jobs.is_confirm_wage')" />
            </div>
            <div class="col-sm-3">
                <label class="text-start col-form-label"
                    for="from_delivery_date">{{ __('driving_jobs.work_day') }}</label>
                <div class="form-group">
                    <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                        data-autoclose="true" data-today-highlight="true">
                        <input type="text" class="js-flatpickr form-control flatpickr-input" id="from_date"
                            name="from_date" value="{{ $from_date }}"
                            placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                            data-today-highlight="true">
                        <div class="input-group-prepend input-group-append">
                            <span class="input-group-text font-w600">
                                <i class="fa fa-fw fa-arrow-right"></i>
                            </span>
                        </div>
                        <input type="text" class="js-flatpickr form-control flatpickr-input" id="to_date"
                            name="to_date" value="{{ $to_date }}"
                            placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                            data-today-highlight="true">
                    </div>
                </div>
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</x-blocks.block-search>
<x-blocks.block-table>
    <x-slot name="options">
        <div class="block-options-item">
            @can(Actions::Manage . '_' . Resources::DrivingJob)
                <a class="btn btn-light" href="{{ route('admin.driving-jobs.calendar') }}"><i
                        class="far fa-calendar-days "></i>&nbsp;
                    {{ __('driving_jobs.view_reserve') }} </a> &nbsp;
                <x-btns.add-new btn-text="{{ __('driving_jobs.add_new') }}"
                    route-create="{{ route('admin.driving-jobs.create') }}" />
            @endcan
        </div>
    </x-slot>
    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th style="width: 1px;">#</th>
                    <th>@sortablelink('worksheet_no', __('driving_jobs.worksheet_no'))</th>
                    <th>@sortablelink('job_type', __('driving_jobs.worksheet_type'))</th>
                    <th>@sortablelink('job_id', __('driving_jobs.job_id'))</th>
                    <th>@sortablelink('self_drive_type', __('driving_jobs.job_type'))</th>
                    <th>@sortablelink('job_id', __('driving_jobs.work_day'))</th>
                    <th>@sortablelink('driver_name', __('driving_jobs.driver_name'))</th>
                    <th class="text-center">@sortablelink('status', __('driving_jobs.status'))</th>
                    <th class="text-center">@sortablelink('is_confirm_wage', __('driving_jobs.is_confirm_wage'))</th>
                    <th style="width: 50px;" class="sticky-col"></th>
                </tr>
            </thead>
            <tbody>
                @if (sizeof($list) > 0)
                    @foreach ($list as $index => $d)
                        <tr>
                            <td>{{ $list->firstItem() + $index }}</td>
                            <td>{{ $d->worksheet_no }}</td>
                            <td>{{ $d->job_type ? __('driving_jobs.job_type_' . $d->job_type) : null }}</td>
                            <td>
                                {{ $d->ref_worksheet_no ? $d->ref_worksheet_no : '' }}
                            </td>
                            <td>{{ $d->self_drive_type ? __('driving_jobs.self_drive_type_' . $d->self_drive_type) : null }}
                                {{ $d->service_type_name ? '(' . $d->service_type_name . ')' : '' }}</td>
                            <td>
                                @if (strcmp($d->self_drive_type, SelfDriveTypeEnum::OTHER) === 0)
                                    {{ get_thai_date_format($d->ref_start_date, 'd/m/Y H:i') . ' - ' . get_thai_date_format($d->ref_end_date, 'd/m/Y H:i') }}
                                @else
                                    {{ get_thai_date_format($d->ref_start_date, 'd/m/Y H:i') }}
                                @endif
                            </td>
                            <td>{{ $d->driver_name }}</td>
                            <td class="text-center">
                                {!! badge_render(
                                    __('driving_jobs.status_' . $d->status . '_class'),
                                    __('driving_jobs.status_' . $d->status . '_text'),
                                    null,
                                ) !!}
                            </td>

                            <td class="text-center">
                                @if(in_array($d->job_type, [$rental_model]))
                                {!! badge_render(
                                    __('driving_jobs.is_confirm_wage_' . $d->is_confirm_wage . '_class'),
                                    __('driving_jobs.is_confirm_wage_' . $d->is_confirm_wage . '_text'),
                                    null,
                                ) !!}
                                @endif
                            </td>
                            <td class="sticky-col text-center">
                                @if (strcmp($d->status, DrivingJobStatusEnum::COMPLETE) == 0)
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driving-jobs.show', [
                                            'driving_job' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DrivingJob,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DrivingJob,
                                    ])
                                @elseif(strcmp($d->status, DrivingJobStatusEnum::CANCEL) == 0)
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driving-jobs.show', [
                                            'driving_job' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DrivingJob,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DrivingJob,
                                    ])
                                @else
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driving-jobs.show', [
                                            'driving_job' => $d,
                                        ]),
                                        'edit_route' => route('admin.driving-jobs.edit', [
                                            'driving_job' => $d,
                                        ]),
                                        'delete_route' => route('admin.driving-jobs.destroy', [
                                            'driving_job' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DrivingJob,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DrivingJob,
                                    ])
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="10" class="text-center">" {{ __('lang.no_list') }} "</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    {!! $list->appends(\Request::except('page'))->render() !!}
</x-blocks.block-table>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-ajax', [
    'id' => 'driver_id',
    'url' => route('admin.util.select2.driver'),
])
@include('admin.components.date-input-script')
