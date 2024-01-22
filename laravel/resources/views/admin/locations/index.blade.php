@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('locations.page_title'))

@section('content')
    <x-blocks.block-search>
        @include('admin.components.forms.simple-search')
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::Location)
                <x-btns.add-new btn-text="{{ __('locations.add_new') }}" route-create="{{ route('admin.locations.create') }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$list">
            <x-slot name="thead">
                <th style="width: 25%;">@sortablelink('name', __('locations.name'))</th>
                <th style="width: 25%;">@sortablelink('name_th', __('locations.location_group'))</th>
                <th style="width: 20%;">{{ __('locations.transportation_type') }}</th>
                <th style="width: 20%;" class="text-center">@sortablelink('status', __('locations.status'))</th>
            </x-slot>
            @foreach ($list as $index => $d)
                <tr>
                    <td>{{ $list->firstItem() + $index }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->name_th }}</td>
                    <td>@if($d->can_transportation_car == 1 && $d->can_transportation_boat == 2)
                            {{__('locations.car')}}, {{ __('locations.boat')}}
                        @elseif($d->can_transportation_car == 1)
                            {{ __('locations.car') }}
                        @else
                            {{  __('locations.boat') }}
                        @endif
                    </td>
                    <td class="text-center">{!! badge_render(__('locations.class_' . $d->status), __('lang.status_' . $d->status), 'w-25') !!} </td>
                    <td class="sticky-col text-center">
                        @include('admin.components.dropdown-action', [
                            'view_route' => route('admin.locations.show', ['location' => $d]),
                            'edit_route' => route('admin.locations.edit', ['location' => $d]),
                            'delete_route' => route('admin.locations.destroy', ['location' => $d]),
                            'view_permission' => Actions::View . '_' . Resources::Location,
                            'manage_permission' => Actions::Manage . '_' . Resources::Location
                        ])
                    </td>
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
