@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('location_groups.page_title'))

@section('content')
    <x-blocks.block-search>
        @include('admin.components.forms.simple-search')
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::LocationGroup)
                <x-btns.add-new btn-text="{{ __('location_groups.add_new') }}"
                                route-create="{{ route('admin.location-groups.create') }}"/>
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th>@sortablelink('name', __('location_groups.name'))</th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->name }}</td>
                    <td class=" text-end">
                        @include('admin.components.dropdown-action', [
                            'view_route' => route('admin.location-groups.show', [
                                'location_group' => $d,
                            ]),
                            'edit_route' => route('admin.location-groups.edit', [
                                'location_group' => $d,
                            ]),
                            'delete_route' => route('admin.location-groups.destroy', [
                                'location_group' => $d,
                            ]),
                            'view_permission' => Actions::View . '_' . Resources::LocationGroup,
                            'manage_permission' =>
                                Actions::Manage . '_' . Resources::LocationGroup,
                        ])
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
