@extends('admin.layouts.layout')
@section('page_title', __('long_term_rental_boms.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::LongTermRentalBom)
            <x-btns.add-new btn-text="{{ __('long_term_rental_boms.add_new') }}"
                route-create="{{ route('admin.long-term-rental-boms.create') }}" />
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
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_list"
                                :label="__('long_term_rental_boms.bom_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="type" :value="$type" :list="$type_list" :label="__('long_term_rental_boms.type')" />
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
                            <th style="width: 2px;">#</th>
                            <th>@sortablelink('name', __('long_term_rental_boms.bom_no'))</th>
                            <th>@sortablelink('type', __('long_term_rental_boms.name'))</th>
                            <th>@sortablelink('type', __('long_term_rental_boms.type'))</th>
                            <th>@sortablelink('type', __('long_term_rental_boms.remark'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $index + $list->firstItem() }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ __('long_term_rental_boms.type_' . $d->type) }}</td>
                                <td>{{ $d->remark }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.long-term-rental-boms.show', [
                                            'long_term_rental_bom' => $d,
                                        ]),
                                        'edit_route' => route('admin.long-term-rental-boms.edit', [
                                            'long_term_rental_bom' => $d,
                                        ]),
                                        'delete_route' => route('admin.long-term-rental-boms.destroy', [
                                            'long_term_rental_bom' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::LongTermRentalBom,
                                        'manage_permission' => Actions::Manage . '_' . Resources::LongTermRentalBom,
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
@include('admin.components.select2-default')
