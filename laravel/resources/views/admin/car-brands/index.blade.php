@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_brands.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
{{--                <div class="block-header">--}}
{{--                    <h3 class="block-title">{{ __('car_brands.total_items') }}</h3>--}}
{{--                    <div class="block-options">--}}
{{--                        <div class="block-options-item">--}}
{{--                            @can(Actions::Manage . '_' . Resources::CarBrand)--}}
{{--                                <x-btns.add-new btn-text="{{ __('car_brands.add_new') }}"--}}
{{--                                    route-create="{{ route('admin.car-brands.create') }}" />--}}
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
                @can(Actions::Manage . '_' . Resources::CarBrand)
                    <x-btns.add-new btn-text="{{ __('car_brands.add_new') }}"
                                    route-create="{{ route('admin.car-brands.create') }}" />
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
                        <th>@sortablelink('code', __('car_brands.code'))</th>
                        <th>@sortablelink('name', __('car_brands.name'))</th>
                        <th>@sortablelink('created_at', __('lang.created_at'))</th>
                        <th>@sortablelink('creator_name', __('lang.created_by'))</th>
                        <th>@sortablelink('updated_at', __('lang.updated_at'))</th>
                        <th>@sortablelink('updater_name', __('lang.updated_by'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>
                                    <x-tables.date-at :label="$d->created_at">
                                        </x-table.date-at>
                                </td>
                                <td>{{ $d->creator_name }}</td>
                                <td>
                                    <x-tables.date-at :label="$d->updated_at">
                                        </x-table.date-at>
                                </td>
                                <td>{{ $d->updater_name }}</td>
                                <td class="sticky-col text-center">
                                    <x-tables.dropdown
                                        :view-route="route('admin.car-brands.show', ['car_brand' => $d])"
                                        :edit-route="route('admin.car-brands.edit', ['car_brand' => $d])"
                                        :delete-route="route('admin.car-brands.destroy', ['car_brand' => $d])"
                                        :view-permission="Actions::View . '_' . Resources::CarBrand"
                                        :manage-permission="Actions::Manage . '_' . Resources::CarBrand"
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
