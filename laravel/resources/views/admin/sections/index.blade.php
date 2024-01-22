@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('sections.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_search',
        ])
        <div class="block-content pt-0">
            <form action="" method="GET" id="form-search">
                <div class="form-group row push">
                    <div class="col-sm-4">
                        <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                        <input type="text" id="s" name="s" class="form-control"
                            placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                    </div>
                    <div class="col-sm-4">
                        <x-forms.select-option id="department_id" :value="$department_id" :list="$department_lists"
                            :label="__('users.department')" />
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
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
                            <th style="width: 10px;" >#</th>
                            <th style="width: 50%;" >@sortablelink('name', __('sections.name'))</th>
                            <th style="width: 50%;" >@sortablelink('department_name', __('sections.department_name'))</th>
                            <th style="width: 10px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->department_name }}</td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.sections.show', [
                                            'section' => $d,
                                        ]),
                                        /* 'edit_route' => route('admin.sections.edit', [
                                            'section' => $d,
                                        ]),
                                        'delete_route' => route('admin.sections.destroy', [
                                            'section' => $d,
                                        ]), */
                                        'view_permission' => Actions::View . '_' . Resources::Section,
                                        'manage_permission' => Actions::Manage . '_' . Resources::Section,
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
