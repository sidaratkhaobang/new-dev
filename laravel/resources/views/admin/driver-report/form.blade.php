@extends('admin.layouts.layout')

@section('page_title', 'สรุปรายจ่ายพนักงานขับรถ')

@push('custom_styles')
    <style>
        .nav-link {
            color: #343a40;
        }

        .nav-tabs-alt .nav-link.active,
        .nav-tabs-alt .nav-item.show .nav-link {
            color: #0665d0;
        }
    </style>
@endpush

@section('content')
    <div class="block block-rounded">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs nav-tabs-alt" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="btabs-alt-static-info-tab" data-bs-toggle="tab"
                                data-bs-target="#btabs-alt-static-info" role="tab" aria-controls="btabs-alt-static-info"
                                aria-selected="true">{{ __('ข้อมูลงาน') }}</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="btabs-alt-static-wage-job-tab" data-bs-toggle="tab"
                                data-bs-target="#btabs-alt-static-wage-job" role="tab"
                                aria-controls="btabs-alt-static-wage-job"
                                aria-selected="false">{{ __('ข้อมูรายจ่าย') }}</button>
                    </li>
                </ul>
                <form id="save-form">
                    <div class="block-content tab-content">
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.label id="driver_name" :value="$d->name" :label="__('ชื่อพนักงาน')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="report_month" :value="$report_month" :list="$month_list" :label="__('เดือนที่สรุป')" />
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="report_year" :value="$report_year" :list="$year_list" :label="__('ปีที่สรุป')" />
                            </div>
                        </div>

                        @include('admin.components.btns.search')

                        <div class="tab-pane active mt-4" id="btabs-alt-static-info" role="tabpanel" aria-labelledby="btabs-alt-static-info-tab">
                            <div class="tab-pane active" id="btabs-alt-static-info" role="tabpanel" aria-labelledby="btabs-alt-static-info-tab">
                                <div class="table-wrap db-scroll">
                                    <table class="table table-striped table-vcenter">
                                        <thead class="bg-body-dark">
                                        <tr>
                                            <th>{{__('เลขที่ใบงาน')}}</th>
                                            <th>{{__('ประเภทงาน')}}</th>
                                            <th>{{__('เลขที่อ้างอิงงาน')}}</th>
                                            <th>{{__('สถานะงาน')}}</th>
                                            <th>{{__('ยืนยันรายจ่าย')}}</th>
                                            <th>{{__('รายจ่าย')}}</th>
                                            <th>{{__('เครื่องมือ')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($driving_job_list as $item)
                                            <tr>
                                                <td>{{$item->worksheet_no}}</td>
                                                <td>{{__('driving_jobs.self_drive_type_' . $item->self_drive_type)}}</td>
                                                <td>
                                                    @if (!in_array($item->job_type, [DrivingJobTypeStatusEnum::OTHER]))
                                                        @if ($item->job && $item->job->worksheet_no != null)
                                                            {{ $item->job->worksheet_no }}
                                                        @else
                                                            @if ($item->worksheet_no_ref)
                                                                {{ $item->worksheet_no_ref }}
                                                            @endif
                                                        @endif
                                                    @else
                                                        {{ null }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {!! badge_render(
                                                        __('driving_jobs.status_' . $item->status . '_class'),
                                                        __('driving_jobs.status_' . $item->status . '_text'),
                                                        null,
                                                    ) !!}
                                                </td>
                                                <td>
                                                    {!! badge_render(
                                                        __('driving_jobs.is_confirm_wage_' . $item->is_confirm_wage . '_class'),
                                                        __('driving_jobs.is_confirm_wage_' . $item->is_confirm_wage . '_text'),
                                                        null,
                                                    ) !!}
                                                </td>
                                                <td>{{$item->summary_wage_job}}</td>
                                                <td class="sticky-col text-center">
                                                    @if($item->is_confirm_wage == BOOL_FALSE)
                                                        <div class="btn-group">
                                                            <div class="col-sm-12">
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                                        <a class="dropdown-item" href="{{route('admin.driver-report-wage.edit',['driver_id' => $item->driver_id,'driving_id' => $item->id])}}"><i class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {!! $driving_job_list->appends(\Request::except('page'))->render() !!}
                            </div>
                        </div>

                        <div class="tab-pane mt-4" id="btabs-alt-static-wage-job" role="tabpanel" aria-labelledby="btabs-alt-static-wage-job-tab">
                            <div class="tab-pane active" id="btabs-alt-static-info" role="tabpanel" aria-labelledby="btabs-alt-static-info-tab">
                                <div class="table-wrap db-scroll">
                                    <table class="table table-striped table-vcenter">
                                        <thead class="bg-body-dark">
                                        <tr>
                                            <th>{{__('ประเภทรายจ่าย')}}</th>
                                            <th>{{__('รายจ่าย')}}</th>
                                            <th>{{__('จำนวนเงิน')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
{{--                                        <tr>--}}
{{--                                            <td>รายเดือน</td>--}}
{{--                                            <td>เงินเดือน</td>--}}
{{--                                            <td>1000</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td>รายวัน</td>--}}
{{--                                            <td>ค่าเที่ยว</td>--}}
{{--                                            <td>1000</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td>รายวัน</td>--}}
{{--                                            <td>ค่าล่วงเวลา</td>--}}
{{--                                            <td>1000</td>--}}
{{--                                        </tr>--}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{--                        <x-forms.hidden id="id" :value="$d->id" />--}}
{{--                        @if ($d->driving_job_type === DrivingJobTypeStatusEnum::MAIN_JOB)--}}
{{--                            <x-forms.hidden id="job_type" :value="$d->job_type" />--}}
{{--                            <x-forms.hidden id="job_id" :value="$d->job_id" />--}}
{{--                            <x-forms.hidden id="self_drive_type" :value="$d->self_drive_type" />--}}
{{--                            <x-forms.hidden id="car_id" :value="$d->car_id" />--}}
{{--                        @endif--}}
{{--                        <div class="row push">--}}
{{--                            <div class="col-md-12 text-end">--}}
{{--                                <a class="btn btn-secondary"--}}
{{--                                   href="{{ route('admin.driving-jobs.index') }}">{{ __('lang.back') }}</a>--}}
{{--                                @if (!isset($view))--}}
{{--                                    <button type="button"--}}
{{--                                            class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>--}}
{{--                                @endif--}}
{{--                                @if (strcmp($d->status, DrivingJobStatusEnum::COMPLETE) == 0 && strcmp($d->is_confirm_wage, BOOL_FALSE) == 0)--}}
{{--                                    <button type="button"--}}
{{--                                            class="btn btn-primary btn-save-draft">{{ __('lang.save') }}</button>--}}
{{--                                    <button type="button" class="btn btn-info btn-save-complete-modal"--}}
{{--                                            data-status="{{ BOOL_TRUE }}">{{ __('driving_jobs.save_complete') }}</button>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.driving-jobs.modals.complete-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.driving-jobs.scripts.wage-job-script')
@include('admin.driving-jobs.scripts.job-parent-script')

@push('scripts')

@endpush
