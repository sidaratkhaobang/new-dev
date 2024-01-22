@extends('admin.layouts.layout')
@section('page_title', __('drivers.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Driver)
            <x-btns.add-new btn-text="{{ __('drivers.add_new') }}" route-create="{{ route('admin.drivers.create') }}" />
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
                @include('admin.components.forms.simple-search')
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
                            <th style="width: 1px;">#</th>
                            <th>@sortablelink('code', __('drivers.code'))</th>
                            <th>@sortablelink('name', __('drivers.name'))</th>
                            <th>@sortablelink('emp_status', __('drivers.emp_status'))</th>
                            <th>@sortablelink('province', __('drivers.province'))</th>
                            <th>@sortablelink('driving_skill_name', __('drivers.driver_skill'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ __('drivers.emp_status_' . $d->emp_status) }}</td>
                                <td>{{ $d->province }}</td>
                                <td>{{ $d->driving_skill_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.drivers.show', ['driver' => $d]),
                                        'edit_route' => route('admin.drivers.edit', ['driver' => $d]),
                                        'delete_route' => route('admin.drivers.destroy', ['driver' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::Driver,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Driver,
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
