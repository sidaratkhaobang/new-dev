@extends('admin.layouts.layout')
@section('page_title', $page_title)

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
    <div class="block {{ __('block.styles') }}">
        <div class="block-header block-header-default">
            <h3 class="block-title">{{ __('borrow_cars.search') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$license_plate" id="license_plate" :list="null" :label="__('repairs.license_plate')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $license_plate_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_brand_id" id="car_brand_id" :list="null" :label="__('car_classes.car_brand')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_brand_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_class_id" id="car_class_id" :list="null" :label="__('check_distance_notices.class')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_class_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_worksheet_no" :value="$rental_worksheet_no" :list="$rental_no_list"
                                :label="__('check_distance_notices.rental_no')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="check_next_date">{{ __('check_distance_notices.check_next_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_check_next_date" name="from_check_next_date"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_check_next_date" name="to_check_next_date"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
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
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>{{ __('cars.license_plate') }}</th>
                            <th>{{ __('cars.brand') }}</th>
                            <th>{{ __('cars.car_age') }}</th>
                            <th>{{ __('check_distance_notices.rental_no') }}</th>
                            <th>{{ __('check_distance_notices.rental_type') }}</th>
                            <th>{{ __('check_distance_notices.rental_duration') }}</th>
                            <th>{{ __('check_distance_notices.check_latest') }}</th>
                            <th>{{ __('check_distance_notices.check_latest_date') }}</th>
                            <th>{{ __('check_distance_notices.check_next') }}</th>
                            <th>{{ __('check_distance_notices.check_next_date') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->class_name }}</td>
                                <td>{{ $d->car_age }}</td>
                                <td>{{ $d->rental_no }}</td>
                                <td>{{ $d->rental_job_type }}</td>
                                <td>{{ $d->rental_duration }}</td>
                                <td class="text-end">{{ number_format($d->check_latest) }}</td>
                                <td>{{ $d->check_latest_date }}</td>
                                <td class="text-end">{{ number_format($d->check_next) }}</td>
                                <td>{{ $d->check_next_date }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.check-distance-notices.show', [
                                            'check_distance_notice' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CheckDistanceNotice,
                                    ])
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

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')

@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2.car-class-by-car-brand'),
    'parent_id' => 'car_brand_id',
])
