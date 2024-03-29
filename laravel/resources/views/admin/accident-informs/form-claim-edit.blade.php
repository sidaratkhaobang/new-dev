@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('accident_informs.class_job_' . $d->status),
            __('accident_informs.status_job_' . $d->status),
            null,
        ) !!}
    @endif
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }

        .img-fluid {
            /* width: 250px; */
            height: 100px;
            object-fit: cover;
        }

        .car-border {
            border: 1px solid #CBD4E1;
            width: 400px;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
        }

        .hide {
            display: none !important;
        }

        .show {
            display: block !important;
            opacity: 1;
            animation: fade 1s;
        }

        @keyframes fade {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }

        .expanded {
            width: 400px;
            height: 400px;
        }

        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-image {
            max-width: 100%;
            max-height: 100%;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .overlay-icon {
            position: absolute;
            /* top: 90%; */
            /* right: 5px; */
            margin: 85% -13%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
@endpush

@push('custom_styles')
    <style>
        .badge-custom {
            min-width: 20rem;
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    @if (isset($btn_group_sheet))
        @include('admin.accident-informs.sections.btn-group-sheet')
    @else
        @include('admin.accident-informs.sections.btn-group')
    @endif
    <form id="save-form-edit">
        @include('admin.accident-informs.sections.claim-detail-edit')
        @include('admin.accident-informs.sections.insurance-detail-edit')
        @include('admin.accident-informs.sections.car-claim-detail-edit')
        @include('admin.accident-informs.sections.repair-detail-edit')
        @include('admin.accident-informs.submit')
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="job_type" :value="null" />
        <x-forms.hidden id="job_id" :value="null" />
    </form>
    @include('admin.components.transaction-modal')
@endsection

@include('admin.accident-informs.scripts.repair-script')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.accident-informs.scripts.input-tag')
@if (isset($btn_group_sheet))
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-inform-sheets.store-edit-claim'),
    ])
@else
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-informs.store-edit-claim'),
    ])
@endif

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'before_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png',
    'mock_files' => [],
    'show_url' => true
])

{{-- @include('admin.components.upload-image', [
    'id' => 'after_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png',
    'mock_files' => [],
    'show_url' => true
]) --}}

@push('scripts')
    <script>
        $("#tls_cost").prop('readonly', true);
        $("#insurance_company").prop('disabled', true);
        $("#policy_no").prop('disabled', true);
        $("#coverage_start_date").prop('disabled', true);
        $("#coverage_end_date").prop('readonly', true);
        $("#save_claim_amount").prop('readonly', true);

        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
        }
        $('#zip_code').prop('disabled', true);

        $("#subdistrict").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.garages.zip-code') }}", {
                params: {
                    id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    $("#zip_code").val(response.data.data.zip_code);
                }
            });
        });

        $(document).ready(function() {
            $('input[name="is_withdraw_true[]"]').click(function() {
                var tls_cost = $(this).is(':checked');
                if (tls_cost) {
                    $("#tls_cost_modal_label").show();
                } else {
                    $("#tls_cost_modal_label").hide();
                }
            });

        });

        $(document).ready(function() {
            function calculateSaveClaimAmount() {
                var amountClaimTls = parseFloat($('#amount_claim_tls').val()) || 0;
                var amountClaimCustomer = parseFloat($('#amount_claim_customer').val()) || 0;
                var saveClaimAmount = amountClaimTls - amountClaimCustomer;

                $('#save_claim_amount').val(saveClaimAmount);
            }

            $('#amount_claim_tls, #amount_claim_customer').on('change', calculateSaveClaimAmount);

            calculateSaveClaimAmount();
        });

        $("#reason_except_deductible_id").hide();
        $('#is_except_deductible').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue === '{{ \App\Enums\RightsEnum::NOT_USE_RIGHTS }}') {

                $("#reason_except_deductible_id").show();
            } else {
                $("#reason_except_deductible_id").hide();
                // $('#cradle').val('').change();
            }
        });

        $(document).ready(function() {
            var selectedValue = $('#is_except_deductible :selected').val();

            if (selectedValue === '{{ \App\Enums\RightsEnum::NOT_USE_RIGHTS }}') {
                $("#reason_except_deductible_id").show();
            } else {
                $("#reason_except_deductible_id").hide();
                // $('#cradle').val('').change();
            }
        });


        $(".btn-save-review").on("click", function() {
            var check_route = @json(isset($btn_group_sheet));
            let storeUri = null;
            if (check_route == true) {
                storeUri = "{{ route('admin.accident-inform-sheets.store-edit-claim') }}";
            } else {
                storeUri = "{{ route('admin.accident-informs.store-edit-claim') }}";
            }
            var formData = new FormData(document.querySelector('#save-form-edit'));
            if (window.addRepairVue.before_files_delete) {
                window.addRepairVue.before_files_delete.forEach((before_files_delete) => {
                    formData.append('before_files__pending_delete_ids[]', before_files_delete);
                });
            }
            if (window.addRepairVue.after_files_delete) {
                window.addRepairVue.after_files_delete.forEach((after_files_delete) => {
                    formData.append('after_files__pending_delete_ids[]', after_files_delete);
                });
            }

            if (window.addRepairVue) {
                let data = window.addRepairVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.before_files && item.before_files.length > 0) {
                            item.before_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('before_files[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }

                        if (item.after_files && item.after_files.length > 0) {
                            item.after_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('after_files[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addRepairVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_before_files && item.pending_delete_before_files.length >
                            0) {
                            item.pending_delete_before_files.forEach(function(id) {
                                formData.append('pending_delete_before_files[]', id);
                            });
                        }
                    });
                }
                //delete slide row
                let delete_claim_ids = window.addRepairVue.pending_delete_claim_ids;
                if (delete_claim_ids && (delete_claim_ids.length > 0)) {
                    delete_claim_ids.forEach(function(delete_claim_ids) {
                        formData.append('delete_claim_ids[]', delete_claim_ids);
                    });
                }
            }
            saveForm(storeUri, formData);
        });
    </script>
@endpush
