@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('branches.page_title'))

@section('content')
<x-blocks.block-search>
    @include('admin.components.forms.simple-search')
</x-blocks.block-search>
<x-blocks.block-table>
    <x-slot name="options" >
        @can(Actions::Manage . '_' . Resources::Branch)
            <x-btns.add-new btn-text="{{ __('branches.add_new') }}" route-create="{{ route('admin.branches.create') }}" />
        @endcan
    </x-slot>
    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
            <tr>
                <th>@sortablelink('name', __('branches.name'))</th>
                <th style="width: 20%;" >@sortablelink('is_main', __('branches.is_main'))</th>
                <th style="width: 20%;" >@sortablelink('is_head_office', __('branches.is_head_office'))</th>
                <th style="width: 10px;" class="sticky-col"></th>
            </tr>
            </thead>
            <tbody>
            @if(!$list->isEmpty())
                @foreach ($list as $d)
                    <tr>
                        <td>{{ $d->name }}</td>
                        <td>
                            {!! badge_render(__('branches.class_' . $d->is_main), __('branches.is_main_' . $d->is_main)) !!}
                        </td>
                        <td>
                            {!! badge_render(__('branches.class_' . $d->is_head_office), __('branches.is_head_office_' . $d->is_head_office)) !!}
                        </td>
                        <td class="sticky-col text-center">
                            @include('admin.components.dropdown-action', [
                                'view_route' => route('admin.branches.show', ['branch' => $d]),
                                'edit_route' => route('admin.branches.edit', ['branch' => $d]),
                                'delete_route' => route('admin.branches.destroy', ['branch' => $d]),
                                'manage_permission' => Actions::Manage . '_' . Resources::Branch,
                                'view_permission' => Actions::View . '_' . Resources::Branch
                            ])
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
</x-blocks.block-table>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
