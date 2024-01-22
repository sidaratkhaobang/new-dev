@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('users.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::User)
            <x-btns.add-new btn-text="{{ __('users.add_new') }}" route-create="{{ route('admin.users.create') }}" />
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
            <form action="" method="GET" id="form-search">
                <div class="form-group row push">
                    <div class="col-sm-3">
                        <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                        <input type="text" id="s" name="s" class="form-control"
                            placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="department_id" :value="$department_id" :list="$department_lists"
                            :label="__('users.department')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="section_id" :value="$section_id" :list="$section_lists"
                            :label="__('users.section')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="branch_id" :value="$branch_id" :list="$branch_lists"
                            :label="__('users.branch')" />
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
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
                            <th>@sortablelink('username', __('users.username'))</th>
                            <th>@sortablelink('name', __('users.name'))</th>
                            <th>@sortablelink('email', __('users.email'))</th>
                            <th>@sortablelink('department_name', __('users.department'))</th>
                            <th>@sortablelink('section_name', __('users.section'))</th>
                            <th>@sortablelink('branch_name', __('users.branch'))</th>
                            <th style="width: 10px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->username }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->email }}</td>
                                <td>{{ $d->department_name }}</td>
                                <td>{{ $d->section_name }}</td>
                                <td>{{ $d->branch_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.users.show', ['user' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::User,
                                        'edit_route' => route('admin.users.edit', ['user' => $d]),
                                        'manage_permission' => Actions::Manage . '_' . Resources::User,
                                        'delete_route' => route('admin.users.destroy', [
                                            'user' => $d,
                                        ]),
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

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-ajax', [
    'id' => 'section_id',
    'url' => route('admin.util.select2.sections'),
    'parent_id' => 'department_id',
])