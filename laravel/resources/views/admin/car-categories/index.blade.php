@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_categories.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        {{--        <div class="block-header">--}}
        {{--            <h3 class="block-title">{{ __('car_categories.total_items') }}</h3>--}}
        {{--            <div class="block-options">--}}
        {{--                <div class="block-options-item">--}}
        {{--                    @can(Actions::Manage . '_' . Resources::CarCategory)--}}
        {{--                        <x-btns.add-new btn-text="{{ __('car_categories.add_new') }}"--}}
        {{--                            route-create="{{ route('admin.car-categories.create') }}" />--}}
        {{--                    @endcan--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
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
                            <x-forms.select-option id="code" :value="$code" :list="$code_list" :label="__('car_categories.code')" />
                        </div> --}}
                        <div class="col-sm-3">
                            <x-forms.select-option id="name" :value="$name" :list="$name_list"
                                                   :label="__('car_categories.page_title')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_group_id" :value="$car_group_id" :list="$car_group_name_list"
                                                   :label="__('car_categories.car_group')"/>
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
                @can(Actions::Manage . '_' . Resources::CarCategory)
                    <x-btns.add-new btn-text="{{ __('car_categories.add_new') }}"
                                    route-create="{{ route('admin.car-categories.create') }}"/>
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
                        <th>@sortablelink('code', __('car_categories.code'))</th>
                        <th>@sortablelink('name', __('car_categories.name'))</th>
                        <th>@sortablelink('name', __('car_categories.car_group'))</th>
                        <th class="text-center">@sortablelink('name', __('car_categories.reserve_small_size'))</th>
                        <th class="text-center">@sortablelink('name', __('car_categories.reserve_big_size'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->car_group_name }}</td>
                                <td class="text-center">{{ $d->reserve_small_size }}</td>
                                <td class="text-center">{{ $d->reserve_big_size }}</td>
                                <td class="sticky-col text-center">
                                    <x-tables.dropdown
                                        :view-route="route('admin.car-categories.show', ['car_category' => $d])"
                                        :edit-route="route('admin.car-categories.edit', ['car_category' => $d])"
                                        :delete-route="route('admin.car-categories.destroy', ['car_category' => $d])"
                                        :view-permission="Actions::View . '_' . Resources::CarCategory"
                                        :manage-permission="Actions::Manage . '_' . Resources::CarCategory"
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
