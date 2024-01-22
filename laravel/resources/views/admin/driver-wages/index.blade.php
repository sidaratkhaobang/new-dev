@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('driver_wages.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::DriverWage)
        <x-btns.add-new btn-text="{{ __('driver_wages.add_new') }}" route-create="{{ route('admin.driver-wages.create') }}" />
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                @include('admin.components.forms.simple-search')
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
                            <th style="width: 35%;">@sortablelink('name', __('driver_wages.name'))</th>
                            <th style="width: 35%;">@sortablelink('service_type_name', __('driver_wages.service_type'))</th>
                            <th style="width: 35%;">@sortablelink('driver_wage_category_name', __('driver_wages.type_name'))</th>
                            <th style="width: 20%;" class="text-center">@sortablelink('status', __('driver_wages.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->service_type_name }}</td>
                                <td>{{ $d->driver_wage_category_name }}</td>
                                <td class="text-center">{!! badge_render(__('driver_wages.class_' . $d->status), __('lang.status_' . $d->status)) !!} </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driver-wages.show', ['driver_wage' => $d]),
                                        'edit_route' => route('admin.driver-wages.edit', ['driver_wage' => $d]),
                                        'delete_route' => route('admin.driver-wages.destroy', [
                                            'driver_wage' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DriverWage,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DriverWage,
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

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
