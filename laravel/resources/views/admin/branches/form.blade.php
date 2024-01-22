@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    <x-blocks.block :title="__('branches.branch_data')" >
        @include('admin.branches.sections.branch-detail')
    </x-blocks.block>

    <x-blocks.block :title="__('branches.location_data')" >
        @include('admin.branches.sections.location-detail')
    </x-blocks.block>

    <x-forms.hidden id="id" :value="$d->id" />

    <x-blocks.block>
        <x-forms.submit-group :optionals="[
            'url' => 'admin.branches.index', 
            'view' => empty($view) ? null : $view,
            'manage_permission' => Actions::Manage . '_' . Resources::Branch
        ]" />
    </x-blocks.block>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.branches.store'),
])
@include('admin.components.date-input-script')
@include('admin.branches.scripts.branch-location-script')

@include('admin.components.select2-ajax', [
    'id' => 'location_group_field',
    'url' => route('admin.util.select2.location-groups'),
    'modal' => '#modal-branch-location'
])

@include('admin.components.select2-ajax', [
    'id' => 'location_field',
    'url' => route('admin.util.select2.locations'),
    'parent_id' => 'location_group_field',
    'modal' => '#modal-branch-location'
])

@push('scripts')
    <script>
        $('#code').prop('disabled', true);
    </script>
@endpush