@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('positions.page_title'))

@section('content')
<x-blocks.block-search>
    @include('admin.components.forms.simple-search')
</x-blocks.block-search>

<x-blocks.block-table>
    @can(Actions::Manage . '_' . Resources::Position)
    <x-slot name="options" >
        <x-btns.add-new btn-text="{{ __('lang.add_new') }}" route-create="{{ route('admin.positions.create') }}" />
    </x-slot>
    @endcan

    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th style="width: 60%;">@sortablelink('name', __('positions.name'))</th>
                    <th style="width: 35%;" class="text-center">@sortablelink('status', __('lang.status'))</th>
                    <th style="width: 10px;" class="sticky-col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $d)
                    <tr>
                        <td>{{ $d->name }}</td>
                        <td class="text-center">{!! badge_status_render($d->status) !!} </td>
                        <td class="sticky-col text-center">
                            @include('admin.components.dropdown-action', [
                                'view_route' => route('admin.positions.show', ['position' => $d]),
                                'edit_route' => route('admin.positions.edit', ['position' => $d]),
                                'delete_route' => route('admin.positions.destroy', [
                                    'position' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::Position,
                                'manage_permission' => Actions::Manage . '_' . Resources::Position,
                            ])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {!! $list->appends(\Request::except('page'))->render() !!}
</x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
