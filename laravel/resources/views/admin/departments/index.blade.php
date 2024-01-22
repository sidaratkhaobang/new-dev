@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('user_departments.page_title'))

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
                            <th>@sortablelink('name', __('user_departments.name'))</th>
                            <th style="width: 10px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.departments.show', [
                                            'department' => $d,
                                        ]),
                                        /* 'edit_route' => route('admin.departments.edit', [
                                            'department' => $d,
                                        ]),
                                        'delete_route' => route('admin.departments.destroy', [
                                            'department' => $d,
                                        ]), */
                                        'view_permission' => Actions::View . '_' . Resources::Department,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Department,
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
