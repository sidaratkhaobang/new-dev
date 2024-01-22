@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_batteries.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::CarBattery)
        <x-btns.add-new btn-text="{{ __('car_batteries.add_new') }}"
            route-create="{{ route('admin.car-batteries.create') }}" />
        @endcan
    </div>
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
<<<<<<< Updated upstream
            'block_option_id' => '_search',
        ])
=======
        ])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('car_batteries.total_items') }}</h3>--}}
        {{--            <div class="block-options">--}}
        {{--                <div class="block-options-item">--}}
        {{--                    @can(Actions::Manage . '_' . Resources::CarBattery)--}}
        {{--                        <x-btns.add-new btn-text="{{ __('car_batteries.add_new') }}"--}}
        {{--                            route-create="{{ route('admin.car-batteries.create') }}" />--}}
        {{--                    @endcan--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
>>>>>>> Stashed changes
        <div class="block-content">
            <div class="justify-content-between">
                @include('admin.components.forms.simple-search')
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
<<<<<<< Updated upstream
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
=======
        @section('block_options_list')
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::CarBattery)
                    <x-btns.add-new btn-text="{{ __('car_batteries.add_new') }}"
                                    route-create="{{ route('admin.car-batteries.create') }}"/>
                @endcan
            </div>
        @endsection
        @include('admin.components.block-header', [
           'text' => __('lang.total_list'),
           'block_option_id' => '_list',
       ])
>>>>>>> Stashed changes
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('name', __('car_batteries.name'))</th>
                        <th>@sortablelink('version', __('car_batteries.version'))</th>
                        <th>@sortablelink('detail', __('car_batteries.detail'))</th>
                        <th>@sortablelink('price', __('car_batteries.price'))</th>
                        <th style="width: 100px;" class="sticky-col">{{ __('lang.tools') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->version }}</td>
                                <td>{{ $d->detail }}</td>
                                <td>{{ number_format($d->price, 2) }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-batteries.show', ['car_battery' => $d]),
                                        'edit_route' => route('admin.car-batteries.edit', ['car_battery' => $d]),
                                        'delete_route' => route('admin.car-batteries.destroy', [
                                            'car_battery' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::CarBattery,
                                        'manage_permission' => Actions::Manage . '_' . Resources::CarBattery,
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

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
