@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<x-blocks.block :title="__('branches.branch_data')" >
    @include('admin.branches.sections.branch-detail')
</x-blocks.block>

<x-blocks.block :title="__('branches.location_data')" >
    @include('admin.branches.sections.views.location-detail')
</x-blocks.block>

<x-blocks.block>
    <x-forms.submit-group :optionals="[
        'url' => 'admin.branches.index', 
        'view' => empty($view) ? null : $view,
        'manage_permission' => Actions::Manage . '_' . Resources::Branch
    ]" />
</x-blocks.block>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $('#name').prop('disabled', true);
        $('#code').prop('disabled', true);
        $('#open_time').prop('disabled', true);
        $('#close_time').prop('disabled', true);
        $('#tax_no').prop('disabled', true);
        $('#tel').prop('disabled', true);
        $('#email').prop('disabled', true);
        $('#address').prop('disabled', true);
        $('#lat').prop('disabled', true);
        $('#lng').prop('disabled', true);
        $("input[type=radio]").attr('disabled', true);
        $('#cost_center').prop('disabled', true);
        $('#document_prefix').prop('disabled', true);
        $('#registered_code').prop('disabled', true);
    </script>
@endpush