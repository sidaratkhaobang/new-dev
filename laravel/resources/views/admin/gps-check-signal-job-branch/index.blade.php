@extends('admin.layouts.layout')
@section('page_title', __('gps.job') . __('gps.page_title_check'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch)
            <button class="btn btn-success" onclick="openModal()"><i class="fa fa-cloud-download-alt"></i>
                {{ __('gps.download_excel') }}</button>
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
                            <x-forms.select-option id="branch" :value="$branch" :list="$branch_list" :label="__('gps.branch')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
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
                            <th style="width: 10%;">@sortablelink('vid', __('gps.vid'))</th>
                            <th style="width: 10%;">@sortablelink('branch_name', __('gps.branch'))</th>
                            <th style="width: 10%;">@sortablelink('check_date', __('gps.check_date_branch'))</th>
                            <th style="width: 10%;">@sortablelink('remark', __('gps.remark_branch'))</th>
                            <th style="width: 8%;">@sortablelink('status', __('gps.status_branch'))</th>
                            <th style="width: 8%;">@sortablelink('main_branch_date', __('gps.main_branch_date'))</th>
                            <th class="text-center" style="width: 8%;">@sortablelink('status_main_branch', __('gps.status_main_branch'))</th>
                            <th style="width: 10%;">@sortablelink('remark_main_branch', __('gps.remark_main_branch'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
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
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->vid }}</td>
                                    <td>{{ $d->branch_name }}</td>
                                    <td>{{ $d->check_date ? get_thai_date_format($d->check_date, 'd/m/Y') : null }}</td>
                                    <td>{{ $d->remark }}</td>
                                    <td class="text-center"> {!! badge_render(__('gps.status_class_' . $d->status), __('gps.status_text_' . $d->status)) !!}</td>
                                    <td>{{ $d->main_branch_date ? get_thai_date_format($d->main_branch_date, 'd/m/Y') : null }}
                                    </td>
                                    <td class="text-center"> {!! badge_render(
                                        __('gps.status_class_' . $d->status_main_branch),
                                        __('gps.status_text_' . $d->status_main_branch),
                                    ) !!}</td>
                                    <td>{{ $d->remark_main_branch }}</td>
                                    <td class="sticky-col text-center">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                @can(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-check-signal-job-branch.show', ['gps_check_signal_job_branch' => $d]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('config_approves.view') }}
                                                    </a>
                                                @endcan
                                                @can(Actions::Manage . '_' . Resources::GPSCheckSignalShortTermBranch)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-check-signal-job-branch.edit', ['gps_check_signal_job_branch' => $d]) }}"><i
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
        @include('admin.gps-cars.modals.check-status-gps')
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
