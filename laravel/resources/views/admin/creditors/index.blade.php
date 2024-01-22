@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('creditors.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Creditor)
            <x-btns.add-new btn-text="{{ __('creditors.add_new') }}"
                route-create="{{ route('admin.creditors.create') }}" />
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
                            <x-forms.input-new-line id="name" :value="$name" :optionals="['placeholder' => __('lang.search_placeholder')]" :label="__('creditors.name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="province_id" :value="$province_id" :list="$province_list" :label="__('creditors.province')" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-12">
                            <x-forms.checkbox-inline id="creditor_types" :list="$creditor_type_list" :label="__('creditors.creditor_type')"
                                :value="$creditor_types" />
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
                            <th>@sortablelink('code', __('creditors.code'))</th>
                            <th>@sortablelink('name', __('creditors.name'))</th>
                            <th>@sortablelink('tel', __('creditors.tel'))</th>
                            <th>@sortablelink('credit_terms', __('creditors.credit_terms_date'))</th>
                            <th>@sortablelink('province', __('creditors.province'))</th>
                            <th>{{ __('creditors.creditor_type') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->tel }}</td>
                                <td>{{ $d->credit_terms }}</td>
                                <td>{{ $d->province }}</td>
                                <td class="sm-text">{{ $d->creditor_types }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.creditors.show', ['creditor' => $d]),
                                        'edit_route' => route('admin.creditors.edit', ['creditor' => $d]),
                                        'delete_route' => route('admin.creditors.destroy', ['creditor' => $d]),
                                        'view_permission' => Actions::View . '_' . Resources::Creditor,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Creditor,
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
