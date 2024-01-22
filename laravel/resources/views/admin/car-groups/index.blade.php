@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_groups.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::CarGroup)
            <x-btns.add-new btn-text="{{ __('car_groups.add_new') }}"
                route-create="{{ route('admin.car-groups.create') }}" />
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
                            <th>@sortablelink('name', __('car_groups.name'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-groups.show', ['car_group' => $d]),
                                        'edit_route' => route('admin.car-groups.edit', ['car_group' => $d]),
                                        'delete_route' => route('admin.car-groups.destroy', [
                                            'car_group' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CarGroup,
                                        'manage_permission' => Actions::Manage . '_' . Resources::CarGroup,
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
