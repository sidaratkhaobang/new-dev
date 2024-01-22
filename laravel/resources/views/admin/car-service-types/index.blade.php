@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_service_types.page_title'))

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
                <x-forms.select-option :value="$engine_no" id="engine_no" :list="null" :label="__('cars.engine_no')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $engine_no_text,
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option :value="$chassis_no" id="chassis_no" :list="null" :label="__('cars.chassis_no')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $chassis_no_text,
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option :value="$license_plate" id="license_plate" :list="null" :label="__('cars.license_plate')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $license_plate_text,
                    ]" />
                <input type="hidden" id="type" value="{{ $type }}">
            </div>
        </div>

        @include('admin.components.btns.search')
    </form>
</x-blocks.block-search>

<x-blocks.block-table>
    <x-tables.table :list="$list" >
        <x-slot name="thead" >
            <th style="width: 25%;">@sortablelink('engine_no', __('cars.engine_no'))</th>
            <th style="width: 25%;">@sortablelink('chassis_no', __('cars.chassis_no'))</th>
            <th style="width: 25%;">@sortablelink('license_plate', __('cars.license_plate'))</th>
            <th style="width: 25%;">@sortablelink('class_name', __('car_service_types.car_class'))</th>
        </x-slot>
        @foreach ($list as $index => $d)
            <tr>
                <td>{{ $list->firstItem() + $index }}</td>
                <td>{{ $d->engine_no }}</td>
                <td>{{ $d->chassis_no }}</td>
                <td>{{ $d->license_plate }}</td>
                <td>{{ $d->class_name }}</td>
                <td class="sticky-col text-center">
                    @include('admin.components.dropdown-action', [
                        'view_route' => route('admin.car-service-types.show', [
                            'car_service_type' => $d,
                        ]),
                        'edit_route' => route('admin.car-service-types.edit', [
                            'car_service_type' => $d,
                        ]),
                        'view_permission' => Actions::View . '_' . Resources::CarServiceType,
                        'manage_permission' =>
                            Actions::Manage . '_' . Resources::CarServiceType,
                    ])
                </td>
            </tr>
        @endforeach
    </x-tables.table>
</x-blocks.block-table>

@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')

@include('admin.components.select2-ajax', [
    'id' => 'engine_no',
    'url' => route('admin.util.select2.car-engine-no-rental-type'),
    'parent_id' => 'type',
])

@include('admin.components.select2-ajax', [
    'id' => 'chassis_no',
    'url' => route('admin.util.select2.car-chassis-no-rental-type'),
    'parent_id' => 'type',
])

@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2.car-license-plate'),
    'parent_id' => 'type',
])
