@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('m_flows.class_' . $d->status), __('m_flows.status_' . $d->status), null) !!}
    @endif
@endsection

@section('content')
    @include('admin.components.creator')
    <x-progress :type="ProgressStepEnum::M_FLOW" :step="$step"></x-progress>

    <form id="save-form">
        @include('admin.m-flows.sections.main-data')

        @if (in_array($d->status, [MFlowStatusEnum::PENDING]))
            @include('admin.m-flows.sections.noti-data')
        @endif

        @if (in_array($d->status, [MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE, MFlowStatusEnum::CLOSE]))
            @include('admin.m-flows.sections.noti-data')
            @include('admin.m-flows.sections.payment-data')
        @endif

        <x-blocks.block>
            <x-forms.hidden id="id" :value="$d->id" />
            @if (!isset($view))
                @if (strcmp($d->status, MFlowStatusEnum::PENDING) == 0)
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.m-flows.index',
                        'view' => empty($view) ? null : $view,
                        'manage_permission' => Actions::Manage . '_' . Resources::MFlow,
                        'btn_name' => __('m_flows.save_notifine'),
                        'data_status' => MFlowStatusEnum::IN_PROCESS,
                    ]">
                        <x-slot name="pos_3">
                            <button type="button" class="btn btn-danger btn-save-close"
                                data-status="{{ MFlowStatusEnum::CLOSE }}">{{ __('m_flows.btn_close') }}</button>
                        </x-slot>
                    </x-forms.submit-group>
                @elseif (strcmp($d->status, MFlowStatusEnum::IN_PROCESS) == 0)
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.m-flows.index',
                        'view' => empty($view) ? null : $view,
                        'manage_permission' => Actions::Manage . '_' . Resources::MFlow,
                        'btn_name' => __('m_flows.save_payment'),
                        'data_status' => MFlowStatusEnum::COMPLETE,
                    ]">
                        <x-slot name="pos_3">
                            <button type="button" class="btn btn-danger btn-save-close"
                                data-status="{{ MFlowStatusEnum::CLOSE }}">{{ __('m_flows.btn_close') }}</button>
                        </x-slot>
                    </x-forms.submit-group>
                @else
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.m-flows.index',
                        'view' => empty($view) ? null : $view,
                        'manage_permission' => Actions::Manage . '_' . Resources::MFlow,
                        'isdraft' => true,
                        'btn_name' => __('m_flows.save_overdue'),
                        'btn_draft_name' => __('registers.save_register_draft'),
                        'data_status' => MFlowStatusEnum::PENDING,
                    ]" />
                @endif
            @endif
        </x-blocks.block>
    </form>
@endsection

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.m-flows.store'),
])

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
@include('admin.components.upload-image', [
    'id' => 'payment_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($payment_file) ? $payment_file : [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])
@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[name=is_payment]").attr('disabled', true);
        }
        $('#car_id').prop('disabled', true);
        $('#overdue_date').prop('disabled', true);
        $('#offense_time').prop('disabled', true);
        var status = '{{ $d->status }}';
        var enum_pending = '{{ \App\Enums\MFlowStatusEnum::PENDING }}';
        if (status === enum_pending) {
            $('#document_date').prop('disabled', true);
            $('#expressway_id').prop('disabled', true);
            $('#fee').prop('disabled', true);
            $('#fine').prop('disabled', true);
            $('#maximum_fine').prop('disabled', true);
            $("input[name=is_payment]").attr('disabled', true);
        }
        var enum_inprocess = '{{ \App\Enums\MFlowStatusEnum::IN_PROCESS }}';
        if (status === enum_inprocess) {
            $('#document_date').prop('disabled', true);
            $('#expressway_id').prop('disabled', true);
            $('#fee').prop('disabled', true);
            $('#fine').prop('disabled', true);
            $('#maximum_fine').prop('disabled', true);
            $("input[name=is_payment]").attr('disabled', true);
            $('#notification_date').prop('disabled', true);
            $('#remark').prop('disabled', true);
        }

        $(".btn-save-close").on("click", function() {
            var storeUri = "{{ route('admin.m-flows.store-close') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            formData.append('status', status);
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
