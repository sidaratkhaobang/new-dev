@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_tires.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        {{--                <div class="block-header">--}}
        {{--                    <h3 class="block-title">{{ __('car_tires.total_items') }}</h3>--}}
        {{--                    <div class="block-options">--}}
        {{--                        <div class="block-options-item">--}}
        {{--                            @can(Actions::Manage . '_' . Resources::CarTire)--}}
        {{--                                <x-btns.add-new btn-text="{{ __('car_tires.add_new') }}"--}}
        {{--                                    route-create="{{ route('admin.car-tires.create') }}" />--}}
        {{--                            @endcan--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        <div class="block-content">
            <div class="justify-content-between mb-4">
                @include('admin.components.forms.simple-search')
            </div>

        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::CarTire)
                    <x-btns.add-new btn-text="{{ __('car_tires.add_new') }}"
                                    route-create="{{ route('admin.car-tires.create') }}"/>
                @endcan
            </div>
        @endsection
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('name', __('car_tires.name'))</th>
                        <th>@sortablelink('version', __('car_tires.version'))</th>
                        <th>@sortablelink('detail', __('car_tires.detail'))</th>
                        <th>@sortablelink('price', __('car_tires.price'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
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
                                    <x-tables.dropdown
                                        :view-route="route('admin.car-tires.show', ['car_tire' => $d])"
                                        :edit-route="route('admin.car-tires.edit', ['car_tire' => $d])"
                                        :delete-route="route('admin.car-tires.destroy', ['car_tire' => $d])"
                                        :view-permission="Actions::View . '_' . Resources::CarTire"
                                        :manage-permission="Actions::Manage . '_' . Resources::CarTire"
                                    ></x-tables.dropdown>
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
