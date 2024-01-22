@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('btn_1')
    <button class="btn btn-primary" onclick="powerAttorneyPdf()">ขอหนังสือมอบอำนาจ (TLS)</button>
@endsection
@section('btn_2')
    <button class="btn btn-primary" onclick="transferPdf()">ขอชุดโอน/เล่มทะเบียน/มอบอำนาจ</button>
@endsection

@section('content')
    @include('admin.components.creator')
    <x-progress :type="ProgressStepEnum::OWNERSHIP_TRANSFER" :step="$d->step"></x-progress>
    <form id="save-form">
        @include('admin.ownership-transfers.section-documents.car-detail')
        @include('admin.ownership-transfers.section-documents.contract-detail')
        @include('admin.ownership-transfers.section-documents.prepare-transfer')
        @include('admin.ownership-transfers.section-documents.avance-detail')
        @include('admin.ownership-transfers.section-waiting-transfers.transfer-detail')
        @include('admin.ownership-transfers.section-documents.optional-document-detail')
        <x-forms.hidden id="id" :value="$d->id" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.ownership-transfers.submit')
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $optional_files,
    'show_url' => true,
])
@include('admin.components.form-save', [
    'store_uri' => route('admin.ownership-transfers.store-waiting-transfer'),
])


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('input[name=check_vat]').prop('disabled', true);
        }
        $("#amount_wait_transfer_kit_date").prop('readonly', true);
        $("#amount_wait_power_attorney_tls_date").prop('readonly', true);

        $("#request_transfer_kit_date").attr('disabled', true);
        $("#receive_transfer_kit_date").attr('disabled', true);
        $("#request_power_attorney_tls_date").attr('disabled', true);
        $("#receive_power_attorney_tls_date").attr('disabled', true);
        $("#memo_no").prop('readonly', true);
        $("#receipt_avance").prop('readonly', true);
        $("#operation_fee_avance").prop('readonly', true);

        $(document).ready(function() {

            var receive_transfer_kit_date = new Date($('#receive_transfer_kit_date').val());
            var request_transfer_kit_date = new Date($('#request_transfer_kit_date').val());

            // amount_wait_transfer_kit_date
            $('#receive_transfer_kit_date , #request_transfer_kit_date').on('change', function() {
                var receive_transfer_kit_date = new Date($('#receive_transfer_kit_date').val());
                var request_transfer_kit_date = new Date($('#request_transfer_kit_date').val());
                var day_diff = DateDifference(request_transfer_kit_date, receive_transfer_kit_date);
                $('#amount_wait_transfer_kit_date').val(day_diff);
            });

            var amount_wait_transfer_kit_date = DateDifference(request_transfer_kit_date,
                receive_transfer_kit_date);
            $('#amount_wait_transfer_kit_date').val(amount_wait_transfer_kit_date);

            //amount_wait_transfer_kit_date
            var request_power_attorney_tls_date = new Date($('#request_power_attorney_tls_date').val());
            var receive_power_attorney_tls_date = new Date($('#receive_power_attorney_tls_date').val());

            // amount_wait_transfer_kit_date
            $('#request_power_attorney_tls_date , #receive_power_attorney_tls_date').on('change', function() {
                var receive_power_attorney_tls_date = new Date($('#receive_power_attorney_tls_date').val());
                var request_power_attorney_tls_date = new Date($('#request_power_attorney_tls_date').val());
                var day_diff = DateDifference(request_power_attorney_tls_date,
                    receive_power_attorney_tls_date);
                $('#amount_wait_power_attorney_tls_date').val(day_diff);
            });

            var amount_wait_power_attorney_tls_date = DateDifference(request_power_attorney_tls_date,
                receive_power_attorney_tls_date);
            $('#amount_wait_power_attorney_tls_date').val(amount_wait_power_attorney_tls_date);



            function DateDifference(date1, date2) {
                var time_diff = date2 - date1;
                days_diff = Math.floor(time_diff / (1000 * 60 * 60 * 24));
                return day_diff = isNaN(days_diff) ? null : days_diff;
            }


            function calculateTotalAvance() {
                var receipt_avance = parseFloat($('#receipt_avance').val().replace(/,/g, ''));
                receipt_avance = isNaN(receipt_avance) ? 0 : receipt_avance;
                var operation_fee_avance = parseFloat($('#operation_fee_avance').val().replace(/,/g, ''));
                operation_fee_avance = isNaN(operation_fee_avance) ? 0 : operation_fee_avance;
                var total = receipt_avance + operation_fee_avance;

                total = parseFloat(total).toFixed(2).toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")


                $('#total_avance').val(total);
            }

            $('#operation_fee_avance, #receipt_avance').on('change', function() {
                calculateTotalAvance();
            });

            calculateTotalAvance();


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
            return formData;
        }

        $(".btn-save-form-ownership").on("click", function() {
            let storeUri = "{{ route('admin.ownership-transfers.store') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            formData.append('status', status);
            saveForm(storeUri, formData);
        });

        function powerAttorneyPdf() {
            var id = $('#id').val();
            var form = document.createElement('form');
            form.action = "{{ route('admin.ownership-transfers.export-pdf-attorney') }}";
            form.method = 'GET';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'attorney_list_arr[]';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        function transferPdf() {
            var id = $('#id').val();
            var form = document.createElement('form');
            form.action = "{{ route('admin.ownership-transfers.export-pdf-transfer') }}";
            form.method = 'GET';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'transfer_list_arr[]';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
