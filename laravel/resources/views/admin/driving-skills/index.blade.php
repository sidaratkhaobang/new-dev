@extends('admin.layouts.layout')
@section('page_title', __('driving_skills.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::DrivingSkill)
            <x-btns.add-new btn-text="{{ __('driving_skills.add_new') }}" route-create="{{ route('admin.driving-skills.create') }}" />
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
                            <th style="width: 48%;">@sortablelink('name', __('driving_skills.name'))</th>
                            <th style="width: 48%;">@sortablelink('service_type_name', __('driving_skills.service_category'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->service_type_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.driving-skills.show', [
                                            'driving_skill' => $d,
                                        ]),
                                        'edit_route' => route('admin.driving-skills.edit', [
                                            'driving_skill' => $d,
                                        ]),
                                        'delete_route' => route('admin.driving-skills.destroy', [
                                            'driving_skill' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DrivingSkill,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DrivingSkill,
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
