@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('roles.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="description" :value="$d->description" :label="__('roles.description')"  />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="department_id" :value="$d->department_id" :list="$department_lists"
                            :label="__('users.department')" :optionals="[
                                'required' => true
                            ]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="section_id" :value="$d->section_id" :list="[]" :label="__('users.section')" :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => $section_name,
                            'label_suffix' => __('users.section_helper')
                        ]" />
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="['url' => 'admin.roles.index', 'view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.roles.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'section_id',
    'url' => route('admin.util.select2.sections'),
    'parent_id' => 'department_id',
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#name').prop('disabled', true);
            $('#description').prop('disabled', true);
            $('#department_id').prop('disabled', true);
            $('#section_id').prop('disabled', true);
        }
    </script>
@endpush
