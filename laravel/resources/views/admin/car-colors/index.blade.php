@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_colors.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::CarColor)
            <x-btns.add-new btn-text="{{ __('car_colors.add_new') }}"
                route-create="{{ route('admin.car-colors.create') }}" />
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
                            <th>@sortablelink('code', __('car_colors.code'))</th>
                            <th>@sortablelink('name', __('car_colors.name'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-colors.show', ['car_color' => $d]),
                                        'edit_route' => route('admin.car-colors.edit', ['car_color' => $d]),
                                        'delete_route' => route('admin.car-colors.destroy', ['car_color' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::CarColor,
                                        'manage_permission' => Actions::Manage . '_' . Resources::CarColor,
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
