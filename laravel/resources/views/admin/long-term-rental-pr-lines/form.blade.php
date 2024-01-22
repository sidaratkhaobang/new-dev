@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        @include('admin.long-term-rentals.sections.btn-group')
        <form id="save-form">
            <h4>{{ __('long_term_rentals.approval_info') }}</h4>
            <hr>
            <div class="row push mb-5">
                <div class="col-sm-3">
                    <x-forms.select-option id="approve_status" :value="$d->status" :list="$approval_status_list" :label="__('lang.status')"
                        :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-input id="require_date" name="require_date" :value="$d->require_date" :label="__('purchase_requisitions.require_date')"
                        :optionals="['required' => true, 'placeholder' => __('lang.select_date')]" />
                </div>
                <div class="col-sm-6">
                    @if (isset($view))
                        <x-forms.view-image :id="'approved_rental_file'" :label="__('long_term_rentals.approved_rental_file')" :list="$approved_rental_files" />
                    @else
                        <x-forms.upload-image :id="'approved_rental_file'" :label="__('long_term_rentals.approved_rental_file')" />
                    @endif
                </div>
            </div>
            @include('admin.long-term-rental-pr-lines.modals.print-rental')
            <div class="row push mb-4">
                <div class="col-auto">
                    <p>หากไม่มีใบ PO จากลูกค้า สามารถพิมพ์แบบฟอร์มขอเช่าได้จากระบบ โดยการกดปุ่ม</p>
                </div>
                <div class="col-auto text-start">
                    <a target="_blank" onclick="openModalPrintRentalRequisition('{{ $d->id }}')" class="btn btn-primary">
                        {{ __('long_term_rentals.requisition_pdf') }}
                    </a>
                </div>
            </div>
            @include('admin.long-term-rental-pr-lines.sections.pr-lines')
            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.hidden id="_temp_id" :value="$d->id" />
            <div class="row push">
                <div class="text-end">
                    <a class="btn btn-secondary" href="{{ route('admin.long-term-rentals.index') }}">{{ __('lang.back') }}</a>
                    @if (!isset($view))
                        <button type="button" class="btn btn-primary btn-create-pr">{{ __('lang.save') }}</button>
                        {{-- <button type="button" class="btn btn-primary btn-save-pr-form">{{ __('long_term_rentals.save_pr') }}</button> --}}
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.long-term-rentals.pr-lines.store'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'approved_rental_file',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => isset($approved_rental_files) ? $approved_rental_files : [],
])

@include('admin.components.upload-image', [
    'id' => 'temp_approved_rental_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@include('admin.long-term-rental-pr-lines.scripts.lt-select2-script')
@include('admin.long-term-rental-pr-lines.scripts.pr-lines-script')

@push('scripts')
    <script>
        $(".btn-create-pr").on("click", function() {
            let storeUri = "{{ route('admin.long-term-rentals.pr-lines.store') }}";
            var formData = appendFormData();
            saveForm(storeUri, formData);
        });

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form'));
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
            if (window.addPrLineVue) {
                let data = window.addPrLineVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.lt_pr_line_approved_rental_files && item.lt_pr_line_approved_rental_files.length > 0) {
                            item.lt_pr_line_approved_rental_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('lt_pr_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addPrLineVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_approved_rental_files && item.pending_delete_approved_rental_files.length > 0) {
                            item.pending_delete_approved_rental_files.forEach(function(id) {
                                formData.append('pending_delete_lt_pr_files[]', id);
                            });
                        }
                    });
                }

                //delete lt_pr_line row
                let delete_lt_pr_line_ids = window.addPrLineVue.pending_delete_lt_pr_line_ids;
                if (delete_lt_pr_line_ids && (delete_lt_pr_line_ids.length > 0)) {
                    delete_lt_pr_line_ids.forEach(function(delete_lt_pr_line_id) {
                        formData.append('delete_lt_pr_line_ids[]', delete_lt_pr_line_id);
                    });
                }
            }
            return formData;
        }
    </script>
@endpush