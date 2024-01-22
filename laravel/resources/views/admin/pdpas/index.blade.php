@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . ' ' . __('pdpas.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Pdpa)
            <x-btns.add-new btn-text="{{ __('pdpas.add_new') }}" route-create="{{ route('admin.pdpa-managements.create') }}" />
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
                            <th style="width: 50%;">@sortablelink('consent_type', __('pdpas.consent_type'))</th>
                            <th style="width: 45%;">@sortablelink('version', __('pdpas.version'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->consent_type }}</td>
                                <td>{{ $d->version }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.pdpa-managements.show', ['pdpa_management' => $d]),
                                        'edit_route' => route('admin.pdpa-managements.edit', ['pdpa_management' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::Pdpa,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Pdpa,
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
