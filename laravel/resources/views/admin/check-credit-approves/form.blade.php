@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        #approve_other_file .dropzone-area {
            width: 100%!important;
        }

        #approve_other_file .dz-preview {
            max-width: 100%!important;
        }

        #approve_other_file .dz-preview-content {
            margin-left: 14px!important;
        }

        #approve_other_file {
            margin-top: 0!important;
        }
    </style>
@endpush
@section('content')
            <form id="save-form">
                <x-forms.hidden id="id" :value="$d->id" />
                @include('admin.check-credit-new-customers.sections.author-info')
                @include('admin.check-credit-new-customers.sections.customer-info')
                @include('admin.check-credit-new-customers.sections.file-check-credit')
                <div class="block {{ __('block.styles') }}">
                    <div class="block-content">
                        <div class="row push">
                            <x-forms.radio-inline id="approve_status"
                                                :value="$d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM || $d->status == \App\Enums\CheckCreditStatusEnum::REJECT ? $d->status : null"
                                                :list="$listApproveStatus"
                                                :label="__('check_credit.form.result_check_credit')"
                                                :optionals="['required' => true ,'input_class' => 'col-sm-6 input-pd']" />
                        </div>
                        <div class="row push" id="display-approve" style="display: none">
                            <div class="col-sm-3 mb-2">
                                <x-forms.input-new-line id="approved_amount" :value="$d->approved_amount" :label="__('check_credit.form.approved_amount')" :optionals="['placeholder' => __('lang.input.placeholder') ,'type' => 'number','required' => true]"/>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <x-forms.input-new-line id="approved_days" :value="$d->approved_days" :label="__('check_credit.form.approved_days')" :optionals="['type' => 'number', 'required' => true]"/>
                            </div>
                            <div class="col-sm-3">
                                @if (Route::is('*.show'))
                                    <x-forms.view-image id="approve_other_file" :label="__('เอกสารเพิ่มเติม')" :list="isset($check_credit_approve_file) ? $check_credit_approve_file : []" />
                                @else
                                    <x-forms.upload-image id="approve_other_file" :label="__('เอกสารเพิ่มเติม')" />
                                @endif
                            </div>
                        </div>
                        <div class="row" id="display-non-approve" style="display: none">
                            <div class="col-sm-6 mb-2">
                                <x-forms.input-new-line id="reason" :value="$d->reason" :label="__('check_credit.form.reason')" />
                            </div>
                        </div>
                    </div>
                </div>
                @include('admin.check-credit-approves.sections.submit')
            </form>
    @include('admin.check-credit-new-customers.modals.form-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.check-credit-new-customers.script.table-file-upload')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
                'id' => 'zone-upload-file',
                'max_files' => 10,
                'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
                'preview_files' => true,
            ])

@include('admin.components.upload-image', [
                'id' => 'approve_other_file',
                'max_files' => 10,
                'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
                'show_url' => true,
                'mock_files' => isset($check_credit_approve_file) ? $check_credit_approve_file : [],
            ])

@include('admin.components.form-save', [
    'store_uri' => route('admin.check-credit-approves.store'),
])

@push('scripts')
    <script>
        $('.form-control').prop('disabled' , {{Route::is('*.show') || $d->is_create_customer}});
        $('.form-check-input').prop('disabled' , {{Route::is('*.show')}});

        @if(Route::is('*.show'))
        $('input').removeAttr('name');
        @endif

        @if($d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM)
        $('input[type=radio]').prop('disabled' , true);
        $('input[type=radio]').removeAttr('name');
        @endif

        $(document).ready(function(){
            @if($d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM || $d->status == \App\Enums\CheckCreditStatusEnum::REJECT)
            displayConditionApprove();
            @endif
        });

        $('input[type=radio][name=approve_status]').change(function () {
            displayConditionApprove();
        });

        function displayConditionApprove() {
            const isApprove = $("#approve_status{{\App\Enums\CheckCreditStatusEnum::CONFIRM}}").is(':checked');
            if (isApprove) {
                $('#display-approve').show();
                $('#display-non-approve').hide();
            }
            else {
                $('#display-approve').hide();
                $('#display-non-approve').show();
            }
        }

        $(".btn-save-form-custom").on("click", function() {
            const storeUri = "{{ route('admin.check-credit-approves.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));
            appendDataFileToForm(formData)

            saveForm(storeUri, formData);
        });

        function appendDataFileToForm(formData) {
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]' , file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]' , id);
                        });
                    }
                });
            }

            if (window.tableFileUpload) {
                let allData = window.tableFileUpload.data_list;
                if (allData && allData.length > 0) {
                    allData.forEach((file) => {
                        if ((!file.saved) && (file.raw_file)) {
                            formData.append('document_file[][file_name]' , file.name);
                            formData.append('document_file[][file]' , file.raw_file);
                        }
                    });
                }

                //delete driver skill row
                let delete_media_file_ids = window.tableFileUpload.pending_delete_media_file_ids;
                if (delete_media_file_ids && (delete_media_file_ids.length > 0)) {
                    delete_media_file_ids.forEach(function (delete_media_file_id) {
                        formData.append('delete_media_file_ids[]' , delete_media_file_id);
                    });
                }
            }
        }
    </script>
@endpush
