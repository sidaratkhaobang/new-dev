@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('customers.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Customer)
            <x-btns.add-new btn-text="{{ __('customers.add_new') }}"
                route-create="{{ route('admin.customers.create') }}" />
        @endcan
    </div>
@endsection

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
                <x-forms.select-option id="name" :value="$name" :list="[]" :label="__('customers.name')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $customer_name,
                    ]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_type" :value="$customer_type" :list="$customer_type_list"
                    :label="__('customers.customer_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="province_id" :value="$province_id" :list="null" :label="__('customers.province')"
                    :optionals="[
                        'ajax' => true,
                        'default_option_label' => $province_name,
                    ]" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.select-option id="sale_id" :value="$sale_id" :list="$sale_list"
                    :label="__('customers.sale')" />
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</x-blocks.block-search>

<x-blocks.block-table>
    <x-slot name="options" >
        @can(Actions::Manage . '_' . Resources::Customer)
            <x-btns.add-new btn-text="{{ __('customers.add_new') }}"
                route-create="{{ route('admin.customers.create') }}" />
        @endcan
    </x-slot>

    <x-tables.table :list="$list" >
        <x-slot name="thead" >
            <th>@sortablelink('customer_code', __('customers.customer_code'))</th>
            <th>@sortablelink('debtor_code', __('customers.debtor_code'))</th>
            <th>@sortablelink('name', __('customers.name'))</th>
            <th>@sortablelink('customer_type', __('customers.customer_type'))</th>
            <th>@sortablelink('province', __('customers.province'))</th>
            <th>@sortablelink('sale_name', __('customers.sale'))</th>
        </x-slot>
        @foreach ($list as $index => $d)
            <tr>
                <td>{{ $list->firstItem() + $index }}</td>
                <td>{{ $d->customer_code }}</td>
                <td>{{ $d->debtor_code }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ __('customers.type_' . $d->customer_type) }}</td>
                <td>{{ $d->province }}</td>
                <td>{{ $d->sale_name }}</td>
                <td class="sticky-col text-center">
                    @include('admin.components.dropdown-action', [
                        'view_route' => route('admin.customers.show', ['customer' => $d]),
                        'edit_route' => route('admin.customers.edit', ['customer' => $d]),
                        'delete_route' => route('admin.customers.destroy', ['customer' => $d]),
                        'view_permission' => Actions::View . '_' . Resources::Customer,
                        'manage_permission' => Actions::Manage . '_' . Resources::Customer,
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

@include('admin.components.select2-ajax', [
    'id' => 'province_id',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'name',
    'url' => route('admin.util.select2-customer.customers'),
])
