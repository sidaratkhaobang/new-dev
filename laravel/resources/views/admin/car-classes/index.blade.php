@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_classes.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        {{--                <div class="block-header">--}}
        {{--                    <h3 class="block-title">{{ __('car_classes.total_items') }}</h3>--}}
        {{--                    <div class="block-options">--}}
        {{--                        <div class="block-options-item">--}}
        {{--                            @can(Actions::Manage . '_' . Resources::CarClass)--}}
        {{--                                <x-btns.add-new btn-text="{{ __('car_classes.add_new') }}"--}}
        {{--                                    route-create="{{ route('admin.car-classes.create') }}" />--}}
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
                            <x-forms.select-option id="name" :value="$name" :list="$name_list" :label="__('car_classes.code_class')" />
                        </div> --}}
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_brand_id" id="car_brand_id" :list="null"
                                                   :label="__('car_classes.car_brand')"
                                                   :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_brand_name,
                                ]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$car_type_id" id="car_type_id" :list="null"
                                                   :label="__('car_classes.car_type')"
                                                   :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $car_type_name,
                                ]"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="engine" :value="$engine" :list="$engine_list"
                                                   :label="__('car_classes.engine')"/>
                        </div>
                    </div>
                    <div class="row push mb-4">
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="full_name" :value="$full_name" :list="$full_name_list"
                                :label="__('car_classes.class')" />
                        </div> --}}

                        <div class="col-sm-3">
                            <x-forms.select-option id="gear_id" :value="$gear_id" :list="$gear"
                                                   :label="__('car_classes.gear')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="year" :value="$year" :list="$year_list"
                                                   :label="__('car_classes.manufacturing_year')"/>
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
                @can(Actions::Manage . '_' . Resources::CarClass)
                    <x-btns.add-new btn-text="{{ __('car_classes.add_new') }}"
                                    route-create="{{ route('admin.car-classes.create') }}"/>
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
                        <th>#</th>
                        <th>@sortablelink('name', __('car_classes.code_class'))</th>
                        <th>@sortablelink('car_brand_name', __('car_classes.car_brand'))</th>
                        <th>@sortablelink('car_type_id', __('car_classes.car_type'))</th>
                        <th>@sortablelink('full_name', __('car_classes.class'))</th>
                        <th>@sortablelink('engine_size', __('car_classes.engine'))</th>
                        <th>@sortablelink('gear_id', __('car_classes.gear'))</th>
                        <th>@sortablelink('manufacturing_year', __('car_classes.manufacturing_year'))</th>
                        <th>@sortablelink('remark', __('car_classes.remark'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->car_brand_name }}</td>
                                <td>{{ $d->car_type_name }}</td>
                                <td>{{ $d->full_name }}</td>
                                <td>{{ $d->engine_size }}</td>
                                <td>{{ $d->car_part_gear_name }}</td>
                                <td>{{ $d->manufacturing_year }}</td>
                                <td>{{ $d->remark }}</td>
                                <td class="sticky-col text-center">
                                    <x-tables.dropdown
                                        :view-route="route('admin.car-classes.show', ['car_class' => $d])"
                                        :edit-route="route('admin.car-classes.edit', ['car_class' => $d])"
                                        :delete-route="route('admin.car-classes.destroy', ['car_class' => $d])"
                                        :view-permission="Actions::View . '_' . Resources::CarClass"
                                        :manage-permission="Actions::Manage . '_' . Resources::CarClass"
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

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_type_id',
    'url' => route('admin.util.select2.car-type'),
    'parent_id' => 'car_brand_id',
])
