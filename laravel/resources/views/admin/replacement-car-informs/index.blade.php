@extends('admin.layouts.layout')
@section('page_title', __('replacement_cars.page_title_inform'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title"><i class="fa fa-magnifying-glass me-1"></i> {{ __('lang.search') }}</h3>
        </div>

        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="null" :label="__('replacement_cars.worksheet_no')"
                            :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $worksheet_name,
                            ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="replacement_type" :value="$replacement_type" :list="$replacement_type_list" :label="__('replacement_cars.replacement_type')"
                             />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$job_type" :list="$replacement_job_type_list" :label="__('replacement_cars.job_id')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_id" :value="null" :list="null" :label="__('replacement_cars.ref_no')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="main_car_id" :value="$main_car_id" :list="null" :label="__('replacement_cars.main_license_plate')"
                            :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $main_car_license_plate,
                            ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="replacement_car_id" :value="$replacement_car_id" :list="null" :label="__('replacement_cars.replace_license_plate')" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $replacement_car_license_plate,
                            ]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="d-md-flex justify-content-md-between align-items-md-center mb-4">
                <div>
                    <h4 class="h4 mb-1"><i class="fa fa-file-lines me-1"></i>{{ __('lang.total_list') }}</h4>
                </div>
                <div>
                    @can(Actions::Manage . '_' . Resources::ReplacementCarInform)
                        <x-btns.add-new btn-text="{{ __('lang.add_data') }}" route-create="{{ route('admin.replacement-car-informs.create') }}" />
                    @endcan
                </div>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>@sortablelink('worksheet_no', __('replacement_cars.worksheet_no'))</th>
                            <th>@sortablelink('replacement_type', __('replacement_cars.replacement_type'))</th>
                            <th>@sortablelink('job_id', __('replacement_cars.job_id'))</th>
                            <th>@sortablelink('replacement_date', __('replacement_cars.job_date_time'))</th>
                            <th>@sortablelink('replacement_place', __('replacement_cars.place'))</th>
                            <th>@sortablelink('customer_name',  __('replacement_cars.customer_name'))</th>
                            <th>{{ __('replacement_cars.main_license_plate') }}</th>
                            <th>{{ __('replacement_cars.replace_license_plate') }}</th>
                            <th>{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ __('replacement_cars.type_' . $d->replacement_type) }}</td>
                                <td>{{ __('replacement_cars.job_type_' . $d->job_type) }} {{ $d->job_worksheet_no ?? '-'}}</td>
                                <td>{{ $d->replacement_date ? get_thai_date_format($d->replacement_date, 'd/m/Y H:i') : '-' }}</td>
                                <td>{{ $d->replacement_place }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ $d->mainCar ? $d->mainCar->license_plate : '' }}</td>
                                <td>{{ $d->replacementCar ? $d->replacementCar->license_plate : '' }}</td>
                                <td>
                                    {!! badge_render(__('replacement_cars.class_' . $d->status),
                                    __('replacement_cars.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.replacement-car-informs.sections.dropdown-actions')
                                </td>
                            </tr>
                        @endforeach
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


@include('admin.components.select2-ajax', [
    'id' => 'worksheet_id',
    'url' => route('admin.util.select2-replacement-car.replacement-cars'),
])

@include('admin.components.select2-ajax', [
    'id' => 'main_car_id',
    'url' => route('admin.util.select2-replacement-car.replacement-main-cars'),
])

@include('admin.components.select2-ajax', [
    'id' => 'replacement_car_id',
    'url' => route('admin.util.select2-replacement-car.replacement-replace-cars'),
])
