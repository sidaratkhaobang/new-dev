@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('accessories.page_title'))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::Accessory)
            <x-btns.add-new btn-text="{{ __('accessories.add_new') }}"
                route-create="{{ route('admin.accessories.create') }}" />
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
                        <div class="col-sm-6">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-6">
                            <x-forms.select-option id="creditor_id" :value="$creditor_id" :list="$dealer_lists" :label="__('accessories.dealer')" />
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
                            <th>@sortablelink('code', __('accessories.code'))</th>
                            <th>@sortablelink('name', __('accessories.name'))</th>
                            <th>@sortablelink('version', __('accessories.version'))</th>
                            <th>@sortablelink('price', __('accessories.price'))</th>
                            <th>@sortablelink('dealer_name', __('accessories.dealer'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $d)
                            <tr>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->version }}</td>
                                <td>{{ number_format($d->price, 2) }}</td>
                                <td>{{ $d->dealer_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.accessories.show', ['accessory' => $d]),
                                        'edit_route' => route('admin.accessories.edit', ['accessory' => $d]),
                                        'delete_route' => route('admin.accessories.destroy', [
                                            'accessory' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::Accessory,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Accessory,
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
