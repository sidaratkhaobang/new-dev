@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('customer_groups.page_title'))

@section('content')
<x-blocks.block-search>
    @include('admin.components.forms.simple-search')
</x-blocks.block-search>
<x-blocks.block-table>
    <x-slot name="options" >
        @can(Actions::Manage . '_' . Resources::CustomerGroup)
            <x-btns.add-new btn-text="{{ __('customer_groups.add_new') }}"
                route-create="{{ route('admin.customer-groups.create') }}" />
        @endcan
    </x-slot>
    <x-tables.table :list="$list" >
        <x-slot name="thead" >
            <th>@sortablelink('name', __('customer_groups.name'))</th>
        </x-slot>
        @foreach ($list as $index => $d)
            <tr>
                <td>{{ $list->firstItem() + $index }}</td>
                <td>{{ $d->name }}</td>
                <td class="sticky-col text-center">
                    @include('admin.components.dropdown-action', [
                        'view_route' => route('admin.customer-groups.show', [
                            'customer_group' => $d,
                        ]),
                        'edit_route' => route('admin.customer-groups.edit', [
                            'customer_group' => $d,
                        ]),
                        'delete_route' => route('admin.customer-groups.destroy', [
                            'customer_group' => $d,
                        ]),
                        'view_permission' => Actions::View . '_' . Resources::CustomerGroup,
                        'manage_permission' => Actions::Manage . '_' . Resources::CustomerGroup,
                    ])
                </td>
            </tr>
        @endforeach
    </x-tables.table>
</x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
