@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('permissions.page_title'))

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
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('permissions.list_menu'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>{{ __('permissions.menu_name') }}</th>
                            <th style="width: 10px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $permission => $d)
                            <tr>
                                <td>{{ __('permissions.' . $permission) }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'edit_route' => route('admin.permissions.edit', ['permission' => $permission]),
                                        'manage_permission' => Actions::Manage . '_' . Resources::Permission,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- {!! $list->appends(\Request::except('page'))->render() !!} --}}
        </div>
    </div>
@endsection


@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.permissions.store'),
])
