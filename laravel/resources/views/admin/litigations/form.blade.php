@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    @include('admin.litigations.sections.charge')
    @include('admin.litigations.sections.table-file-upload')
    @include('admin.litigations.sections.incident')
    @includeWhen(strcmp($d->location_case, LitigationLocationEnum::POLICE_STATION) === 0, 'admin.litigations.sections.police')
    @includeWhen(strcmp($d->location_case, LitigationLocationEnum::COURT) === 0, 'admin.litigations.sections.court')
    @includeWhen(!empty($d->location_case), 'admin.litigations.sections.status')
    @includeWhen(strcmp($d->status, LitigationStatusEnum::FOLLOW) === 0, 'admin.litigations.sections.cost')

    
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="id" name="id" :value="$d->id" />
                @include('admin.litigations.sections.submit-group')
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.litigations.store'),
])
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.litigations.scripts.status-script')
@includeWhen(strcmp($d->status, LitigationStatusEnum::FOLLOW) === 0, 'admin.litigations.scripts.cost-script')
@include('admin.components.upload-image', [
    'id' => 'litigation_files',
    'max_files' => 5,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $litigation_files ?? [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@include('admin.components.upload-image', [
    'id' => 'additional_files',
    'max_files' => 1,
    'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
    'preview_files' => true,
])

@include('admin.components.select2-ajax', [
    'id' => 'responsible_person_id',
    'url' => route('admin.util.select2.users'),
])

@push('scripts')
    <script>
        $(".btn-save-draft-litigation").on("click", function () {
            const storeUri = "{{ route('admin.litigations.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));
            appendDataFileToForm(formData);
            formData.append('is_draft', true);
            saveForm(storeUri, formData);
        });

        $(".btn-close-litigation").on("click", function () {
            const storeUri = "{{ route('admin.litigations.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));
            appendDataFileToForm(formData);
            formData.append('is_close', true);
            saveForm(storeUri, formData);
        });

        $(".btn-save-form-litigation").on("click", function () {
            const storeUri = "{{ route('admin.litigations.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));
            appendDataFileToForm(formData)
            saveForm(storeUri, formData);
        });

        function appendDataFileToForm(formData)
        {
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }

            if (window.tableFileUpload) {
                let allData = window.tableFileUpload.data_list;
                if (allData && allData.length > 0) {
                    allData.forEach((file) => {
                        if ((!file.saved) && (file.raw_file)) {
                            formData.append('additional_files[][file_name]', file.name);
                            formData.append('additional_files[][file]', file.raw_file);
                        }
                    });
                }

                let delete_media_file_ids = window.tableFileUpload.pending_delete_media_file_ids;
                if (delete_media_file_ids && (delete_media_file_ids.length > 0)) {
                    delete_media_file_ids.forEach(function (delete_media_file_id) {
                        formData.append('delete_additional_file_ids[]', delete_media_file_id);
                    });
                }
            }
        }

        var status = '{{ $d->status }}';
        var is_view = '{{ $view ?? null }}';
        var location_case = @json($d->location_case);
        if (location_case) {
            disableSectionCharge(true);
            disableIncident(true);
        }
        if (status == '{{ LitigationStatusEnum::PENDING }}') {
            disableSectionCharge(true);
        }

        if (is_view) {
            disableSectionCharge(true);
            disableIncident(true);
            disableExtendIncident(true);
            disableCourt(true);
        }

        function disableSectionCharge(is_disabled) {
            $("#title").prop('disabled', is_disabled);
            $("#case").prop('disabled', is_disabled);
            $("#case_type").prop('disabled', is_disabled);
            $("#tls_type").prop('disabled', is_disabled);
            $("#accuser_defendant").prop('disabled', is_disabled);
            $("#incident_date").prop('disabled', is_disabled);
            $("#consultant").prop('disabled', is_disabled);
            $("#fund").prop('disabled', is_disabled);
            $("#responsible_person_id").prop('disabled', is_disabled);
            $("#legal_service_provider").prop('disabled', is_disabled);
            $("#legal_service_provider").prop('disabled', is_disabled);
            $("#legal_service_fee").prop('disabled', is_disabled);
            $("#legal_note").prop('disabled', is_disabled);
        }

        function disableIncident(is_disabled) {
            $("#location_case").prop('disabled', is_disabled);
            $("#details").prop('disabled', is_disabled);
        }

        function disableExtendIncident(is_disabled) {
            $("#request_date").prop('disabled', is_disabled);
            $("#receive_date").prop('disabled', is_disabled);
        }

        function disableCourt(is_disabled) {
            $("#court_filing_date").prop('disabled', is_disabled);
            $("#location_name").prop('disabled', is_disabled);
            $("#black_number").prop('disabled', is_disabled);
            $("#red_number").prop('disabled', is_disabled);
            $("#age").prop('disabled', is_disabled);
            $("#remark").prop('disabled', is_disabled);
            $("#remark").prop('disabled', is_disabled);
            $("#due_date").prop('disabled', is_disabled);
            $("#inquiry_official").prop('disabled', is_disabled);
            $("#inquiry_official_tel").prop('disabled', is_disabled);
        }
    </script>
@endpush