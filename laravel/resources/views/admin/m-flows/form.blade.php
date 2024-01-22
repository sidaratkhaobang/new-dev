@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    @include('admin.components.creator')
    <x-progress :type="ProgressStepEnum::M_FLOW" :step="$step"></x-progress>
    <form id="save-form">
        <x-blocks.block :title="__('registers.car_detail')">
            <div class="row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="car_id" :value="null" :list="null" :label="__('m_flows.license_plate_chassis_engine')"
                        :optionals="[
                            'ajax' => true,
                            'required' => true,
                        ]" />
                </div>
            </div>
        </x-blocks.block>

        <x-blocks.block :title="__('m_flows.offense_data')">
            @include('admin.m-flows.sections.offense-data')
        </x-blocks.block>

        <x-blocks.block>
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.hidden id="create" :value="true" />
            <x-forms.submit-group :optionals="[
                'url' => 'admin.m-flows.index',
                'view' => empty($view) ? null : $view,
                'manage_permission' => Actions::Manage . '_' . Resources::MFlow,
                'isdraft' => true,
                'btn_draft_name' => __('registers.save_register_draft'),
                'btn_name' => __('m_flows.save_overdue'),
                'data_status' => MFlowStatusEnum::PENDING,
            ]" />
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.m-flows.store'),
])

@include('admin.m-flows.scripts.offense-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'overdue_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($overdue_file) ? $overdue_file : [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])
@push('scripts')
    <script></script>
@endpush
