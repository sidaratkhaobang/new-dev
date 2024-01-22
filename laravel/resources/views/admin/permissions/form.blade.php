@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('permissions.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @foreach($departments as $department_index => $department)
                    <div class="block block-mode-hidden">
                        <div class="block-header">
                            <label for="">
                                <h5 class="p-0 m-0" >{{ $department->name }}</h5>
                            </label>
                            <div class="block-options ">
                                <div class="block-options-item ms-2">
                                    <a class="block-option-toggle " data-toggle="block-option" data-action="content_toggle"><i class="si si-arrow-up"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="block-content" >
                            <table class="table table-bordered table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th>{{ __('permissions.role_name') }}</th>
                                        <th style="width: 100px;" >{{ __('permissions.view') }}</th>
                                        <th style="width: 100px;" >{{ __('permissions.manage') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role_index => $role)
                                        @php
                                        if(strcmp($role->department_id, $department->id) != 0){
                                            continue;
                                        }
                                        @endphp
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <input class="form-check-input form-check-input-each"
                                                    type="checkbox" id="{{ $role->id }}_{{ $department_index }}_{{ $role_index }}"
                                                    name="role_view[{{ $role->id }}]"
                                                    value="{{ 'view_' . $permission }}"
                                                       @if(!empty($role->view_permission))
                                                        checked
                                                       @endif
                                                >
                                            </td>
                                            <td>
                                                <input class="form-check-input form-check-input-each"
                                                    type="checkbox" id="{{ $role->id }}_{{ $department_index }}_{{ $role_index }}"
                                                    name="role_manage[{{ $role->id }}]"
                                                    value="{{ 'manage_' . $permission }}"
                                                       @if(!empty($role->manage_permission))
                                                        checked
                                                       @endif
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                <x-forms.submit-group :optionals="['url' => 'admin.permissions.index', 'view' => empty($view) ? null : $view]" />
                <x-forms.hidden id="permission" :value="$permission" />
            </form>
        </div>
    </div>
@endsection


@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.permissions.store'),
])
