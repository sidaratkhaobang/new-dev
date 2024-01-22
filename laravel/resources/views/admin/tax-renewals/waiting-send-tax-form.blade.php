@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d?->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('tax_renewals.status_' . $d->status . '_class'), __('tax_renewals.status_' . $d->status . '_text'), null) !!}
    @endif
@endsection
@section('content')
    <x-progress :type="ProgressStepEnum::TAX_RENEWAL" :step="$d->step"></x-progress>
    <form id="save-form">
        @include('admin.tax-renewals.section-infos.car-detail')
        @include('admin.tax-renewals.section-infos.use-car-detail')
        @include('admin.tax-renewals.section-infos.prepare-renew-tax-detail')
        @include('admin.tax-renewals.section-infos.avance-detail')
        @include('admin.tax-renewals.section-waiting-send-taxs.tax-renew-detail')
        <x-forms.hidden id="id" :value="$d->id" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.tax-renewals.submit')
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
    'store_uri' => route('admin.tax-renewals.store'),
])


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('input[type=radio]').prop('disabled', true);
        }
        $("#amount_wait_transfer_kit_date").prop('readonly', true);
        $("#amount_wait_power_attorney_tls_date").prop('readonly', true);
        $("#amount_day_wait_cmi").prop('readonly', true);
        $("#amount_wait_register_book_date").prop('readonly', true);
        $("#request_cmi_date").prop('disabled', true);
        $("#receive_cmi_date").prop('disabled', true);
        $("input[type=checkbox]").attr('disabled', true);
        $("input[type=radio]").attr('disabled', true);
        $("#request_registration_book_date").prop('disabled', true);
        $("#receive_registration_book_date").prop('disabled', true);
        $("#memo_no").prop('disabled', true);
        $("#receipt_avance").prop('disabled', true);
        $("#operation_fee_avance").prop('disabled', true);
        // $('input[name=check_vat]').prop('disabled', true);

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

            //
            //amount_day_wait_cmi
            var request_power_attorney_tls_date = new Date($('#request_power_attorney_tls_date').val());
            var receive_power_attorney_tls_date = new Date($('#receive_power_attorney_tls_date').val());


            var receive_cmi_date = new Date($('#receive_cmi_date').val());
            var request_cmi_date = new Date($('#request_cmi_date').val());
            // amount_day_wait_cmi
            $('#request_cmi_date , #receive_cmi_date').on('change', function() {
                var receive_cmi_date = new Date($('#receive_cmi_date').val());
                var request_cmi_date = new Date($('#request_cmi_date').val());
                var day_diff = DateDifference(request_cmi_date, receive_cmi_date);
                $('#amount_day_wait_cmi').val(day_diff);
            });

            var amount_day_wait_cmi = DateDifference(request_cmi_date,
                receive_cmi_date);
            $('#amount_day_wait_cmi').val(amount_day_wait_cmi);

            //
            var request_registration_book_date = new Date($('#request_registration_book_date').val());
            var receive_registration_book_date = new Date($('#receive_registration_book_date').val());

            // amount_day_wait_cmi
            $('#request_registration_book_date , #receive_registration_book_date').on('change', function() {
                var receive_registration_book_date = new Date($('#receive_registration_book_date').val());
                var request_registration_book_date = new Date($('#request_registration_book_date').val());
                var day_diff = DateDifference(request_registration_book_date,
                    receive_registration_book_date);
                $('#amount_wait_register_book_date').val(day_diff);
            });

            var amount_wait_register_book_date = DateDifference(request_registration_book_date,
                receive_registration_book_date);
            $('#amount_wait_register_book_date').val(amount_wait_register_book_date);



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

        $(".btn-save-form-tax-renewal").on("click", function() {
            let storeUri = "{{ route('admin.tax-renewals.store-waiting-send-tax') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            formData.append('status', status);
            saveForm(storeUri, formData);
        });

        jQuery(function() {
            var date = '{{ $d->created_at }}';
            flatpickr("#request_cmi_date", {
                minDate: date,
            });
            flatpickr("#receive_cmi_date", {
                minDate: date,
            });
            flatpickr("#request_registration_book_date", {
                minDate: date,
            });
            flatpickr("#receive_registration_book_date", {
                minDate: date,
            });
            flatpickr("#send_tax_renew_date", {
                minDate: date,
            });
        });
    </script>
@endpush
