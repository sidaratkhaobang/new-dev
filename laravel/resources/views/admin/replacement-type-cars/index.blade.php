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
{{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$license_plate" id="license_plate" :list="$license_plate_list"
                                :label="__('cars.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_class_id" id="car_class_id" :list="$car_class_list"
                                :label="__('cars.brand')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
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
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('license_plate', __('cars.license_plate'))</th>
                        <th>@sortablelink('engine_no', __('cars.engine_no'))</th>
                        <th>@sortablelink('chassis_no', __('cars.chassis_no'))</th>
                        <th>@sortablelink('car_class', __('cars.brand'))</th>
                        <th>@sortablelink('car_storage_age', __('cars.car_storage_age'))</th>
                        <th>@sortablelink('slot', __('cars.slot'))</th>
                        <th>@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td style="width: 30%; white-space: normal;">{{ $d->class_name }} -
                                    {{ $d->car_class_name }}</td>
                                <td>{{ $d->car_age_start }}</td>
                                <td>{{ $d->slot }}</td>
                                <td>{!! badge_render(__('cars.class_' . $d->status), __('cars.status_' . $d->status)) !!} </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.replacement-type-cars.show', [
                                            'replacement_type_car' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::ReplacementTypeCar,
                                    ])
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
