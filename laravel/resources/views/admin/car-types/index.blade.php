@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_types.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
{{--                <div class="block-header">--}}
{{--                    <h3 class="block-title">{{ __('car_types.total_items') }}</h3>--}}
{{--                    <div class="block-options">--}}
{{--                        <div class="block-options-item">--}}
{{--                            @can(Actions::Manage . '_' . Resources::CarType)--}}
{{--                                <x-btns.add-new btn-text="{{ __('car_types.add_new') }}"--}}
{{--                                    route-create="{{ route('admin.car-types.create') }}" />--}}
{{--                            @endcan--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
        <div class="block-content">
            <div class="justify-content-between mb-4">
                {{-- @include('admin.components.forms.simple-search') --}}
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                   placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="category_id2" :value="null" :list="$category_list" :label="__('car_types.code')" />
                        </div> --}}
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="category_id" :value="$category_id" :list="$category_list" :label="__('car_types.name')" />
                        </div> --}}
                        <div class="col-sm-3">
                            <x-forms.select-option id="brand_id" :value="$brand_id" :list="$brand_list"
                                                   :label="__('car_types.brand')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="group_id" :value="$group_id" :list="$group_list"
                                                   :label="__('car_types.car_group')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="category_id" :value="$category_id" :list="$category_list"
                                                   :label="__('car_types.category')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::CarType)
                    <x-btns.add-new btn-text="{{ __('car_types.add_new') }}"
                                    route-create="{{ route('admin.car-types.create') }}" />
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
                        <th>@sortablelink('code', __('car_types.code'))</th>
                        <th>@sortablelink('name', __('car_types.name'))</th>
                        <th>@sortablelink('car_brand_name', __('car_types.brand'))</th>
                        <th>@sortablelink('car_group_name', __('car_types.car_group'))</th>
                        <th>@sortablelink('car_category_name', __('car_types.category'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->car_brand_name }}</td>
                                <td>{{ $d->car_group_name }}</td>
                                <td>{{ $d->car_category_name }}</td>
                                <td class="sticky-col text-center">
                                    <x-tables.dropdown
                                        :view-route="route('admin.car-types.show', ['car_type' => $d])"
                                        :edit-route="route('admin.car-types.edit', ['car_type' => $d])"
                                        :delete-route="route('admin.car-types.destroy', ['car_type' => $d])"
                                        :view-permission="Actions::View . '_' . Resources::CarType"
                                        :manage-permission="Actions::Manage . '_' . Resources::CarType"
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

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
