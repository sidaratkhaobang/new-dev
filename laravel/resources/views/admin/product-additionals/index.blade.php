@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('product_additionals.page_title'))


@section('content')
    <x-blocks.block-search>
        @include('admin.components.forms.simple-search')
    </x-blocks.block-search>

    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::ProductAdditional)
                <x-btns.add-new btn-text="{{ __('product_additionals.add_new') }}"
                    route-create="{{ route('admin.product-additionals.create') }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th>@sortablelink('name', __('product_additionals.name'))</th>
                <th>@sortablelink('price', __('product_additionals.price'))</th>
                <th>@sortablelink('is_stock', __('product_additionals.is_stock'))</th>
                <th>@sortablelink('amount', __('product_additionals.amount'))</th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ number_format($d->price, 2) }}</td>
                    <td>
                        {{ __('product_additionals.is_stock_' . $d->is_stock) }}
                    </td>
                    <td>
                        {{ $d->amount ? $d->amount : '-' }}
                    </td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action', [
                            'view_route' => route('admin.product-additionals.show', ['product_additional' => $d]),
                            'edit_route' => route('admin.product-additionals.edit', ['product_additional' => $d]),
                            'delete_route' => route('admin.product-additionals.destroy', [
                                'product_additional' => $d,
                            ]),
                            'view_permission' => Actions::View . '_' . Resources::ProductAdditional,
                            'manage_permission' => Actions::Manage . '_' . Resources::ProductAdditional,
                        ])
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
