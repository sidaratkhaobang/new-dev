@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::RepairList)
            <x-btns.add-new btn-text="{{ __('lang.add') . __('lang.list') }}"
                route-create="{{ route('admin.repair-lists.create') }}" />
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
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="code" :value="$code" :list="null" :label="__('repair_lists.code')"
                                :optionals="['ajax' => true, 'default_option_label' => $code_name]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="name" :value="$name" :list="null" :label="__('repair_lists.name')"
                                :optionals="['ajax' => true, 'default_option_label' => $name_text]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
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
                            <th>{{ __('repair_lists.code') }}</th>
                            <th>{{ __('repair_lists.name') }}</th>
                            <th>{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>
                                    {!! badge_render(__('repair_lists.class_' . $d->status), __('repair_lists.status_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.repair-lists.show', ['repair_list' => $d]),
                                        'edit_route' => route('admin.repair-lists.edit', ['repair_list' => $d]),
                                        'delete_route' => route('admin.repair-lists.destroy', [
                                            'repair_list' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::RepairList,
                                        'manage_permission' => Actions::Manage . '_' . Resources::RepairList,
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
    'id' => 'code',
    'url' => route('admin.util.select2-repair.repair-code-list'),
])

@include('admin.components.select2-ajax', [
    'id' => 'name',
    'url' => route('admin.util.select2-repair.repair-name-list'),
])