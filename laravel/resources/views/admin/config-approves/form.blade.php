@extends('admin.layouts.layout')
@section('page_title', $page_title . ' > ' . __('config_approves.config_type_' . $d->type))

@section('block_options_list')
    <div class="block-options-item">
        @can(Actions::Manage . '_' . Resources::ConfigApprove)
        <button type="button" class="btn btn-primary"
            onclick="addConfigLine2()">{{ __('config_approves.add_new') }}
        </button>
        @endcan
    </div>
@endsection

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('config_approves.approve_table'),
            'block_icon_class' => '',
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <form id="save-form">
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-config">
                        <thead class="bg-body-dark">
                            <th style="width: 10px;">{{ '#' }}</th>
                            <th style="width: 15%" class="text-start">{{ __('config_approves.department') }}</th>
                            <th style="width: 1px;" class="text-center">{{ __('config_approves.all_person') }}</th>
                            <th style="width: 20%" class="text-start">{{ __('config_approves.section') }}</th>
                            <th style="width: 1px;" class="text-center">{{ __('config_approves.all_person') }}</th>
                            <th style="width: 20%" class="text-start">{{ __('config_approves.role') }}</th>
                            <th style="width: 1px;" class="text-center">{{ __('config_approves.all_person') }}</th>
                            <th style="width: 20%" class="text-start">{{ __('config_approves.user') }}</th>
                            <th style="width: 1px;" class="text-center">{{ __('config_approves.super_user2') }}</th>
                            <th style="width: 10px;"></th>
                        </thead>
                        <tbody>
                            {!! $table_html !!}
                        </tbody>
                    </table>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="branch_id" :value="$branch_id" />
                <div class="row mt-3">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route('admin.config-approves.index', ['branch_id' => $branch_id]) }}">{{ __('lang.back') }}</a>
                        <button type="button" class="btn btn-primary btn-save-form">{{ __('lang.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.config-approves.modals.config')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.config-approves.store'),
])
@include('admin.config-approves.scripts.config-approve-line-script')

@include('admin.components.select2-ajax', [
    'id' => 'm_user_id',
    'url' => route('admin.util.select2.users'),
    'modal' => '#modal-config',
])

@include('admin.components.select2-ajax', [
    'id' => 'm_department_id',
    'url' => route('admin.util.select2.departments'),
    'modal' => '#modal-config',
])

@include('admin.components.select2-ajax', [
    'id' => 'm_section_id',
    'url' => route('admin.util.select2.sections'),
    'parent_id' => 'm_department_id',
    'modal' => '#modal-config',
])

@include('admin.components.select2-ajax', [
    'id' => 'm_role_id',
    'url' => route('admin.util.select2.roles'),
    'parent_id' => 'm_department_id',
    'parent_id_2' => 'm_section_id',
    'modal' => '#modal-config',
])