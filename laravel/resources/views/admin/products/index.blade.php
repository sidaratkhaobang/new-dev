@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('products.page_title'))

@section('content')

    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                        placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$sku" id="sku" :list="$sku_list" :label="__('products.sku')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$name" id="name" :list="$name_list" :label="__('products.name')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option :value="$service_type_id" id="service_type_id" :list="$service_type_list" :label="__('products.service_type')" />
                </div>
            </div>
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.select-option :value="$branch_id" id="branch_id" :list="$branch_list" :label="__('products.branch')" />
                </div>
            </div>

            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::Product)
                <x-btns.add-new btn-text="{{ __('products.add_new') }}" route-create="{{ route('admin.products.create') }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th>@sortablelink('name', __('products.name'))</th>
                <th>@sortablelink('service_type_name', __('products.service_type'))</th>
                <th>@sortablelink('standard_price', __('products.price'))</th>
                <th>@sortablelink('branch_name', __('products.branch'))</th>
                <th class="text-center">
                    {{ __('products.status') }}
                </th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->service_type_name }}</td>
                    <td>{{ number_format($d->standard_price, 2) }}</td>
                    <td>{{ $d->branch_name }}</td>
                    <td class="text-center">
                        {!! badge_render(__('products.class_' . $d->status), __('products.status_' . $d->status)) !!}
                    </td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action', [
                            'view_route' => route('admin.products.show', ['product' => $d]),
                            'edit_route' => route('admin.products.edit', ['product' => $d]),
                            'delete_route' => route('admin.products.destroy', ['product' => $d]),
                            'view_permission' => Actions::View . '_' . Resources::Product,
                            'manage_permission' => Actions::Manage . '_' . Resources::Product,
                        ])
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
