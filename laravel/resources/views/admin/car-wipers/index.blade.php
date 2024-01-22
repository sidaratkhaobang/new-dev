@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_wipers.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::CarWiper)
            <x-btns.add-new btn-text="{{ __('car_wipers.add_new') }}"
                route-create="{{ route('admin.car-wipers.create') }}" />
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
<<<<<<< Updated upstream
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
=======
{{--        <div class="block-header">--}}
{{--            <h3 class="block-title">{{ __('car_wipers.total_items') }}</h3>--}}
{{--            <div class="block-options">--}}
{{--                <div class="block-options-item">--}}
{{--                    @can(Actions::Manage . '_' . Resources::CarWiper)--}}
{{--                        <x-btns.add-new btn-text="{{ __('car_wipers.add_new') }}"--}}
{{--                            route-create="{{ route('admin.car-wipers.create') }}" />--}}
{{--                    @endcan--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
>>>>>>> Stashed changes
        <div class="block-content">
            <div class="justify-content-between">
                @include('admin.components.forms.simple-search')
            </div>
<<<<<<< Updated upstream
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
                            <th>@sortablelink('name', __('car_wipers.name'))</th>
                            <th>@sortablelink('version', __('car_wipers.version'))</th>
                            <th>@sortablelink('detail', __('car_wipers.detail'))</th>
                            <th>@sortablelink('price', __('car_wipers.price'))</th>
                            <th style="width: 100px;" class="sticky-col">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->version }}</td>
                                <td>{{ $d->detail }}</td>
                                <td>{{ number_format($d->price, 2) }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-wipers.show', ['car_wiper' => $d]),
                                        'edit_route' => route('admin.car-wipers.edit', ['car_wiper' => $d]),
                                        'delete_route' => route('admin.car-wipers.destroy', [
                                            'car_wiper' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CarWiper,
                                        'manage_permission' => Actions::Manage . '_' . Resources::CarWiper,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
=======

>>>>>>> Stashed changes
        </div>
    </div>

    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
            <tr>
                <th>@sortablelink('name', __('car_wipers.name'))</th>
                <th>@sortablelink('version', __('car_wipers.version'))</th>
                <th>@sortablelink('detail', __('car_wipers.detail'))</th>
                <th>@sortablelink('price', __('car_wipers.price'))</th>
                <th style="width: 100px;" class="sticky-col">{{ __('lang.tools') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($list as $d)
                <tr>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->version }}</td>
                    <td>{{ $d->detail }}</td>
                    <td>{{ number_format($d->price, 2) }}</td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action', [
                            'view_route' => route('admin.car-wipers.show', ['car_wiper' => $d]),
                            'edit_route' => route('admin.car-wipers.edit', ['car_wiper' => $d]),
                            'delete_route' => route('admin.car-wipers.destroy', [
                                'car_wiper' => $d,
                            ]),
                            'view_permission' => Actions::View . '_' . Resources::CarWiper,
                            'manage_permission' => Actions::Manage . '_' . Resources::CarWiper,
                        ])
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $list->appends(\Request::except('page'))->render() !!}
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
