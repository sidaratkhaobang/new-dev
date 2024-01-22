@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('service_types.page_title'))

@section('content')
<x-blocks.block-search>
    @include('admin.components.forms.simple-search')
</x-blocks.block-search>

<x-blocks.block-table>
    <x-tables.table :list="$list" >
        <x-slot name="thead" >
            <th>@sortablelink('name', __('service_types.name'))</th>
            <th>@sortablelink('transportation_type', __('service_types.transportation_type'))</th>
        </x-slot>

        @foreach ($list as $index => $d)
            <tr>
                <td>{{ $list->firstItem() + $index }}</td>
                <td>{{ $d->name }}</td>
                <td>@if($d->transportation_type == 1)
                        {{__('service_types.car')}}
                    @else
                        {{  __('service_types.boat') }}
                    @endif
                </td>
                <td class="sticky-col text-center">
                    @include('admin.components.dropdown-action', [
                        'view_route' => route('admin.service-types.show', ['service_type' => $d]),
                        'edit_route' => route('admin.service-types.edit', ['service_type' => $d]),
                        'view_permission' => Actions::View . '_' . Resources::ServiceType,
                        'manage_permission' => Actions::Manage . '_' . Resources::ServiceType,
                    ])
                </td>
            </tr>
        @endforeach
    </x-tables.table>
</x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
