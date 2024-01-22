@extends('admin.layouts.layout')
@section('page_title', $page_title . ' > ' . __('config_approves.config_type_' . $d->type))
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('config_approves.approve_table'),
            'block_icon_class' => '',
        ])
        <div class="block-content">
            <form id="save-form">
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
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
                        </thead>
                        <tbody>
                            {!! $table_html !!}
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.config-approves.index', ['branch_id' => $branch_id]) }}">{{ __('lang.back') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')

@push('scripts')
    <script>
        $('.form-control').prop('disabled', true);
        $("input[type=checkbox]").attr('disabled', true);
    </script>
@endpush
