@extends('admin.layouts.layout')
@section('page_title', __('สัญญา'))
@section('page_title_no', $data->worksheet_no)
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    {!! badge_render(
        __('contract.status_class_' . $data->status),
        __('contract.status_text_' . $data->status),
    ) !!}
@endsection
@section('btn-nav')
    @if((strcmp($data->job_type, \App\Models\LongTermRental::class) === 0 && !empty($data->contract_type)) || strcmp($data->job_type, \App\Models\Rental::class) === 0)
        <a class="btn btn-primary float-end" href="{{ route('admin.contracts.print-pdf', ['contract' => $data]) }}" 
            target="_blank" ><i class="fa fa-upload me-1"></i> พิมพ์สัญญา</a>
    @endif
@endsection
@push('custom_styles')
    <style>
        .pre-wrap {
            white-space: pre-wrap;
        }
        .body-add-btn {
            display: flex;
            justify-content: center;
            flex-direction: column-reverse;
        }
        
        #contract_attorney_file .dropzone-area , #contract_attach_file .dropzone-area {
            width: 100%!important;
        }

        #contract_attorney_file .dz-preview , #contract_attach_file .dz-preview {
            max-width: 100%!important;
        }
        .img-fluid {
            width: 250px;
            height: 100px;
            object-fit: cover;
        }

        .car-border {
            border: 1px solid #CBD4E1;
            width: 300px;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
        }

        .hide {
            display: none;
        }
        .btn-tap-page {
            background-color: #FFFFFF;
        }
    </style>
@endpush
@section('content')
    <form id="save-form">
        <x-forms.hidden id="contract_id" :value="$data->id"/>
        <section class="section-page" id="page-1">
            @include('admin.contracts.page-1.index')
        </section>

        <section class="section-page" id="page-2" style="display: none">
            @include('admin.contracts.page-2.index')
        </section>

        <section class="section-page" id="page-3" style="display: none">
            @include('admin.contracts.page-3.index')
        </section>

        <section class="section-page" id="page-4" style="display: none">
            @include('admin.contracts.page-4.index')
        </section>
    </form>
    @include('admin.contracts.modals.upload-file-modal')
    @include('admin.contracts.modals.history-accident-modal')
    @include('admin.contracts.modals.history-maintenance-modal')
    @include('admin.contracts.modals.history-edit-contract-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
                'id' => 'zone-upload-file',
                'max_files' => 10,
                'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
                'preview_files' => true,
            ])

@include('admin.components.upload-image', [
                'id' => 'contract_attorney_file',
                'max_files' => 1,
                'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
                'show_url' => true,
            ])

@include('admin.components.upload-image', [
                'id' => 'contract_attach_file',
                'max_files' => 1,
                'accepted_files' => '.jpeg,.jpg,.png,.svg,.pdf,.xls,.xlsx,.doc,.csv',
                'show_url' => true,
                'mock_files' => $contract_attach_file
            ])

@include('admin.components.form-save', [
    'store_uri' => route('admin.contracts.store'),
])

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true)
        $(".btn-tap-page").on("click", function () {
            const page = $(this).attr('data-page');
            $('.btn-tap-page').removeClass('active');
            $('.section-page').hide();
            $('#page-' + page).show();
            $('.page-' + page).addClass('active')
        });

        $(".btn-show-modal-history-accident").on("click", function () {
            $('#modal-history-accident').modal('show')
        });

        $(".btn-show-modal-history-maintenance").on("click", function () {
            $('#modal-history-maintenance').modal('show')
        });

        $(".btn-show-modal-history-edit-contract").on("click", function () {
            $('#modal-history-edit-contract').modal('show')
        });

        function appendDataFileToForm(formData) {
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
                            formData.append('contract_file[][file_name]', file.name);
                            formData.append('contract_file[][file]', file.raw_file);
                        }
                    });
                }

                //delete driver skill row
                let delete_media_file_ids = window.tableFileUpload.pending_delete_media_file_ids;
                if (delete_media_file_ids && (delete_media_file_ids.length > 0)) {
                    delete_media_file_ids.forEach(function (delete_media_file_id) {
                        formData.append('delete_media_file_ids[]', delete_media_file_id);
                    });
                }
            }

            if (window.tableFileAttorney) {
                let allData = window.tableFileAttorney.data_list;
                if (allData && allData.length > 0) {
                    allData.forEach((data, index) => {
                        if (!data.saved) {
                            formData.append('contract_signers[' + index + '][sign_type]', data.user_sign.signer_type);
                            formData.append('contract_signers[' + index + '][sign_name]', data.user_sign.name);
                            formData.append('contract_signers[' + index + '][contract_side]', data.user_sign.contract_side);
                            formData.append('contract_signers[' + index + '][is_attorney]', data.user_sign.is_attorney);
                            data.files.forEach((file) => {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('contract_signers[' + index + '][files][]', file.raw_file);
                                }
                            });
                        }
                    });
                }

                let delete_contract_signers_ids = window.tableFileAttorney.pending_delete_contract_signers_ids;
                if (delete_contract_signers_ids && (delete_contract_signers_ids.length > 0)) {
                    delete_contract_signers_ids.forEach(function (delete_contract_signers_ids) {
                        formData.append('delete_contract_signers_ids[]', delete_contract_signers_ids);
                    });
                }
            }
        }

        $(".btn-save-form-custom").on("click", function () {
            const storeUri = "{{ route('admin.contracts.store') }}";
            const formData = new FormData(document.querySelector('#save-form'));

            appendDataFileToForm(formData)

            saveForm(storeUri, formData);
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }

        const contract_lines = @if($data->contractline) @json($data->contractline) @else [] @endif;
        const status = @if($data->status) @json($data->status) @else null @endif;
        const contract_end_date = @if($data->contract_end_date) @json($data->contract_end_date) @else null @endif;
        if (contract_lines && contract_lines.length > 0) {
            contract_lines.forEach(element => {
                var line_id = element.id;
                var rental_price = element.rental_price;

                $("#contract_cars\\["+ line_id + "\\]\\[pick_up_date\\]").prop('disabled', true);
                $("#contract_cars\\["+ line_id + "\\]\\[expected_return_date\\]").prop('disabled', true);
                $("#contract_cars\\["+ line_id + "\\]\\[fine\\]").prop('readonly', true);
                if (status != '{{ ContractEnum::ACTIVE_BETWEEN_CONTRACT }}' ) {
                    $("#contract_cars\\["+ line_id + "\\]\\[return_date\\]").prop('disabled', true);
                    $('input:radio[name="contract_cars[' + line_id + '][is_fine]"]').prop('disabled', true);
                    $("#contract_cars\\["+ line_id + "\\]\\[percent_fine\\]").prop('disabled', true);
                }

                $("#contract_cars\\["+ line_id + "\\]\\[return_date\\]").on('change', function() {
                    const return_date = new Date($(this).val());
                    const _contract_end_date = new Date(contract_end_date);
                    if (return_date > _contract_end_date) {
                        $("#" + line_id + "_is_fine").removeClass('hide');
                    } else {
                        $("#" + line_id + "_is_fine").addClass('hide');
                        $("#" + line_id + "_percent_fine").val(null);
                        $("#" + line_id + "_fine").val(null);
                        $("#" + line_id + "_percent_fine").addClass('hide');
                        $("#" + line_id + "_fine").addClass('hide');
                    }
                });

                $('input:radio[name="contract_cars[' + line_id + '][is_fine]"]').change(function() {
                    var selected_value = $(this).val();
                    if (selected_value === '1') {
                        $("#" + line_id + "_percent_fine").removeClass('hide');
                        $("#" + line_id + "_fine").removeClass('hide');
                    } else {
                        $("#contract_cars\\["+ line_id + "\\]\\[percent_fine\\]").val(null);
                        $("#contract_cars\\["+ line_id + "\\]\\[fine\\]").val(null);
                        $("#" + line_id + "_percent_fine").addClass('hide');
                        $("#" + line_id + "_fine").addClass('hide');
                    }
                });

                $("#contract_cars\\["+ line_id + "\\]\\[percent_fine\\]").on('input', function() {
                    var percent_fine = $(this).val();
                    var formatted_percent_fine = percent_fine.replace(/,/g, "");
                    var fine = 0;
                    fine = formatted_percent_fine / 100 * rental_price;
                    var fine_text = parseFloat(fine).toFixed(2);
                    $("#contract_cars\\["+ line_id + "\\]\\[fine\\]").val(numberWithCommas(fine_text));
                });
            });            
        }
    </script>
@endpush
