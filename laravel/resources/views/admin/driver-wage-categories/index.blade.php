@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('driver_wage_categories.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::DriverWageCategory)
            <x-btns.add-new btn-text="{{ __('driver_wage_categories.add_new') }}" route-create="{{ route('admin.driver-wage-categories.create') }}" />
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
                            <th style="width: 60%;">@sortablelink('name', __('driver_wage_categories.name'))</th>
                            <th style="width: 35%;" class="text-center">@sortablelink('status', __('driver_wage_categories.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td class="text-center">{!! badge_render(__('driver_wage_categories.class_' . $d->status), __('lang.status_' . $d->status)) !!} </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driver-wage-categories.show', ['driver_wage_category' => $d]),
                                        'edit_route' => route('admin.driver-wage-categories.edit', ['driver_wage_category' => $d]),
                                        'delete_route' => route('admin.driver-wage-categories.destroy', [
                                            'driver_wage_category' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DriverWageCategory,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DriverWageCategory,
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
