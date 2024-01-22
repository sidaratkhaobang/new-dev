@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <form id="save-form">
        @include('admin.check-credit-new-customers.sections.author-info')
        @include('admin.check-credit-new-customers.sections.customer-info')
        @include('admin.check-credit-new-customers.sections.file-check-credit')
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                @include('admin.check-credit-new-customers.sections.submit')
            </div>
        </div>
    </div>
</form>
    @include('admin.check-credit-new-customers.modals.form-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
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
    'store_uri' => route('admin.check-credit-new-customers.store'),
])

@push('scripts')
    <script>
        $('.form-control').prop('disabled' , {{Route::is('*.show') || $d->is_create_customer}});
        $('.form-check-input').prop('disabled' , {{Route::is('*.show')}});

        @if(Route::is('*.show'))
        $('input').removeAttr('name');
        @endif

        @if($d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM)
        $('#display-approve .form-control').prop('disabled' , true);
        $('#display-approve input').removeAttr('name');
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
    </script>

    <script>
        function appendDataFileToForm(formData) {
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

        $(".btn-save-form-draft").on("click", function() {
            const storeUri = "{{ route('admin.check-credit-new-customers.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));

            appendDataFileToForm(formData)

            saveForm(storeUri, formData);
        });

        $(".btn-save-pending-approve").on("click", function() {
            const storeUri = "{{ route('admin.check-credit-new-customers.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));

            appendDataFileToForm(formData)

            formData.append('status_pending_approve', true);

            saveForm(storeUri, formData);
        });

        $(".btn-save-create-customer").on("click", function() {
            const storeUri = "{{ route('admin.check-credit-new-customers.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));
            appendDataFileToForm(formData)
            formData.append('status_create_customer', true);

            saveForm(storeUri, formData);
        });
    </script>
@endpush
