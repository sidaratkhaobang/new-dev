@extends('admin.layouts.layout')
@section('page_title',  __('rental_categories.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">{{ __('rental_categories.total_items') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                    <x-btns.add-new btn-text="{{ __('rental_categories.add_new') }}" route-create="{{ route('admin.rental-categories.create') }}" />
                </div>
            </div>
        </div>
        <div class="block-content">
            <div class="justify-content-between mb-4">
                @include('admin.components.forms.simple-search')
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 33%;">@sortablelink('name', __('rental_categories.name'))</th>
                            <th style="width: 33%;">@sortablelink('service_type_name', __('rental_categories.service_category'))</th>
                            <th style="width: 20%;" class="text-center">@sortablelink('status', __('rental_categories.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->service_type_name }}</td>
                                <td class="text-center">{!! badge_render(__('rental_categories.class_' . $d->status), __('lang.status_' . $d->status), 'w-25') !!} </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.rental-categories.show', ['rental_category' => $d]),
                                        'edit_route' => route('admin.rental-categories.edit', ['rental_category' => $d]),
                                        'delete_route' => route('admin.rental-categories.destroy', [
                                            'rental_category' => $d,
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

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
