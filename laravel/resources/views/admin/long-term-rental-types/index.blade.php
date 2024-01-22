@extends('admin.layouts.layout')
@section('page_title', __('long_term_rental_types.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::LongTermRentalType)
            <x-btns.add-new btn-text="{{ __('long_term_rental_types.add_new') }}"
                route-create="{{ route('admin.long-term-rental-types.create') }}" />
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
                            <th style="width: 2px;">#</th>
                            <th>@sortablelink('name', __('long_term_rental_types.name'))</th>
                            <th>@sortablelink('type', __('long_term_rental_types.type'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $index + $list->firstItem() }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ __('long_term_rental_types.type_' . $d->type) }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.long-term-rental-types.show', [
                                            'long_term_rental_type' => $d,
                                        ]),
                                        'edit_route' => route('admin.long-term-rental-types.edit', [
                                            'long_term_rental_type' => $d,
                                        ]),
                                        'delete_route' => route('admin.long-term-rental-types.destroy', [
                                            'long_term_rental_type' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::LongTermRentalType,
                                        'manage_permission' => Actions::Manage . '_' . Resources::LongTermRentalType,
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
