@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .btn-group {
            background-color: #ffffff;
            color: #000000;
        }
    </style>
@endpush
@section('content')
    @include('admin.selling-prices.sections.btn-group')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('driving_jobs.license_plate_chassis_no')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_name,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_class_id" :value="$car_class_id" :list="null" :label="__('gps.car_class')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_class_name,
                                ]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th>@sortablelink('license_plate', __('cars.license_plate'))</th>
                            <th>@sortablelink('car_class_name', __('gps.car_class'))</th>
                            <th>@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                            <th>@sortablelink('engine_no', __('cars.engine_no'))</th>
                            <th>@sortablelink('current_mileage', __('selling_prices.mileage'))</th>
                            <th class="text-center">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->car_class_name }}</td>
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->engine_no }}</td>
                                    <td>{{ $d->current_mileage }}</td>
                                    <td class="text-center">{!! badge_render(__('selling_prices.class_' . $d->status), __('selling_prices.status_' . $d->status)) !!}</td>
                                    <td class="sticky-col text-center">
                                        @if (in_array($d->status, [SellingPriceStatusEnum::PENDING_SALE]))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.selling-cars.show', [
                                                    'selling_car' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::SellingCar,
                                            ])
                                        @else
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.selling-cars.show', [
                                                    'selling_car' => $d,
                                                ]),
                                                'edit_route' => route('admin.selling-cars.edit', [
                                                    'selling_car' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::SellingCar,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::SellingCar,
                                            ])
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="7">" {{ __('lang.no_list') }} "</td>
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

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-car-auction.sale-car-license-plates'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2-car-auction.sale-car-car-class'),
])
