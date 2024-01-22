@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="username" :value="$d->username" :label="__('users.username')" :optionals="['required' => true]" />
                    </div>
                    @if ($d->id != null)
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="password" :value="null" :label="__('users.password')" :optionals="['type' => 'password']" />
                        </div>
                    @else
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="password" :value="null" :label="__('users.password')" :optionals="['required' => true, 'type' => 'password']" />
                        </div>
                    @endif
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="name" :value="$d->name" :label="__('users.name')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('creditors.tel')" />
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="email" :value="$d->email" :label="__('users.email')" :optionals="['required' => true, 'type' => 'email']" />
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="department_id" :value="$d->department_id" :list="$user_department_lists"
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

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="role_id" :value="$d->role_id" :list="[]" :label="__('users.role')" :optionals="[
                            'select_class' => 'js-select2-custom',
                            'ajax' => true,
                            'default_option_label' => $role_name,
                            'required' => true
                        ]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list" :label="__('users.branch')"  :optionals="['required' => true]"/>
                    </div>
                </div>

                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.users.index', 'view' => empty($view) ? null : $view]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.users.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'section_id',
    'url' => route('admin.util.select2.sections'),
    'parent_id' => 'department_id',
])

@include('admin.components.select2-ajax', [
    'id' => 'role_id',
    'url' => route('admin.util.select2.roles'),
    'parent_id' => 'department_id',
    'parent_id_2' => 'section_id',
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        if ($status) {
            $('#username').prop('disabled', true);
            $('#name').prop('disabled', true);
            $('#password').prop('disabled', true);
            $('#tel').prop('disabled', true);
            $('#email').prop('disabled', true);
            $('#user_department_id').prop('disabled', true);
            $('#role_id').prop('disabled', true);
            $('#branch_id').prop('disabled', true);
            $('#department_id').prop('disabled', true);
            $('#section_id').prop('disabled', true);
        }
    </script>
@endpush
