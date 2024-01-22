@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('roles.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Role)
        <x-btns.add-new btn-text="{{ __('lang.add') }}" route-create="{{ route('admin.roles.create') }}" />
    @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content pt-0">
            @include('admin.components.forms.simple-search')
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 10px;" >#</th>
                            <th>@sortablelink('name', __('roles.name'))</th>
                            <th>@sortablelink('department_name', __('users.department'))</th>
                            <th>@sortablelink('section_name', __('users.section'))</th>
                            <th>@sortablelink('updated_at', __('roles.updated_at'))</th>
                            <th style="width: 10px;" class="sticky-col">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->department_name }}</td>
                                <td>{{ $d->section_name }}</td>
                                <td>{{ get_thai_date_format($d->updated_at, 'd/m/Y') }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.roles.show', ['role' => $d]),
                                        'edit_route' => route('admin.roles.edit', [
                                            'role' => $d,
                                        ]),
                                        'delete_route' => route('admin.roles.destroy', [
                                            'role' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::Role,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Role,
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
