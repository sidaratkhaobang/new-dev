@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
           'text' => __('lang.search'),
           'block_icon_class' => 'icon-search',
           'is_toggle' => true,
       ])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('purchase_orders.all_item') }}</h3>--}}
        {{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_id" :value="$worksheet_id" :list="null"
                                                   :label="__('replacement_cars.worksheet_no')"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $worksheet_name,
                            ]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="replacement_type" :value="$replacement_type"
                                                   :list="$replacement_type_list"
                                                   :label="__('replacement_cars.replacement_type')"
                            />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$job_type" :list="$replacement_job_type_list"
                                                   :label="__('replacement_cars.job_id')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_id" :value="null" :list="null"
                                                   :label="__('replacement_cars.ref_no')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="main_car_id" :value="$main_car_id" :list="null"
                                                   :label="__('replacement_cars.main_license_plate')"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $main_car_license_plate,
                            ]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="replacement_car_id" :value="$replacement_car_id" :list="null"
                                                   :label="__('replacement_cars.replace_license_plate')" :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $replacement_car_license_plate,
                            ]"/>
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
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('worksheet_no', __('replacement_cars.worksheet_no'))</th>
                        <th>@sortablelink('replacement_type', __('replacement_cars.replacement_type'))</th>
                        <th>@sortablelink('job_id', __('replacement_cars.job_id'))</th>
                        <th>@sortablelink('replacement_date', __('replacement_cars.job_date_time'))</th>
                        <th>@sortablelink('replacement_place', __('replacement_cars.place'))</th>
                        <th>@sortablelink('customer_name', __('replacement_cars.customer_name'))</th>
                        <th>{{ __('replacement_cars.main_license_plate') }}</th>
                        <th>{{ __('replacement_cars.replace_license_plate') }}</th>
                        <th>{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                {{-- <td>{{ $list->firstItem() + $index }}</td> --}}
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ __('replacement_cars.type_' . $d->replacement_type) }}</td>
                                <td>{{ __('replacement_cars.job_type_' . $d->job_type) }} {{ $d->job ? $d->job->worksheet_no : '-'}}</td>
                                <td>{{ $d->replacement_expect_date ? get_thai_date_format($d->replacement_expect_date, 'd/m/Y') : '-' }}</td>
                                <td>{{ $d->replacement_expect_place }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ $d->mainCar ? $d->mainCar->license_plate : '' }}</td>
                                <td>{{ $d->replacementCar ? $d->replacementCar->license_plate : '' }}</td>
                                <td>
                                    {!! badge_render(__('replacement_cars.class_' . $d->status),
                                    __('replacement_cars.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.replacement-car-approves.sections.dropdown-actions')
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="12">" {{ __('lang.no_list') }} "</td>
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
