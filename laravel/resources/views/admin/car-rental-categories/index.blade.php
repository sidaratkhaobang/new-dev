@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('car_rental_categories.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('car_rental_categories.total_items') }}</h3>
            <div class="block-options">
                {{-- <div class="block-options-item">
                    <x-btns.add-new btn-text="{{ __('car_rental_categories.add_new') }}" route-create="{{ route('admin.car-rental-categories.create') }}" />
                </div> --}}
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="engine_no" :value="$engine_no" :list="$engine_no_list" :label="__('car_rental_categories.engine_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" :label="__('car_rental_categories.chassis_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list" :label="__('car_rental_categories.license_plate')" />
                        </div>
                    </div>

                    @include('admin.components.btns.search')
                </form>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 33%;">@sortablelink('engine_no', __('car_rental_categories.engine_no'))</th>
                            <th style="width: 33%;">@sortablelink('chassis_no', __('car_rental_categories.chassis_no'))</th>
                            <th style="width: 33%;">@sortablelink('license_plate', __('car_rental_categories.license_plate'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.car-rental-categories.show', ['car_rental_category' => $d]),
                                        'edit_route' => route('admin.car-rental-categories.edit', ['car_rental_category' => $d]),
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
@include('admin.components.select2-default')
