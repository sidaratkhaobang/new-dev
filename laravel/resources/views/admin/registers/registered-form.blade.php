@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('registers.status_' . $d->status . '_class'),
            __('registers.status_' . $d->status . '_text'),
            null,
        ) !!}
    @endif
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

        .active {
            color: #4D82F3 !important;
            background-color: #E5EDFE !important;
            border-color: #4D82F3 !important;
        }

        .inactive {
            color: #94A3B8 !important;
            background-color: #F6F8FC !important;
            border-color: #CBD4E1 !important;
        }
    </style>
@endpush

@section('content')
    <x-progress :type="ProgressStepEnum::REGISTER" :step="$d->step"></x-progress>
    <form id="save-form">
        @include('admin.registers.section-registereds.purchase-order-detail')
        @include('admin.registers.section-registereds.car-detail')
        @include('admin.registers.section-registereds.registered-detail')
        @include('admin.registers.section-registereds.avance-detail')
        @include('admin.registers.section-registereds.proceed-detail')
        <x-forms.hidden id="id" :value="$d->id" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.submit-group :optionals="[
                'view' => empty($view) ? null : $view,
                'isdraft' => true,
                'btn_name' => __('registers.save_registered'),
                'btn_draft_name' => __('registers.save_register_draft'),
                'icon_class_name' => 'icon-send',
            ]"></x-forms.submit-group>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.registers.store-registered'),
])

@include('admin.transfer-cars.scripts.update-status')
@include('admin.components.date-input-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $optional_files,
    'show_url' => true,
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('#receipt_date').css('background-color', '#f1f1f1')
        }

        $("#registered_date_amount").prop('readonly', true);
        $("#document_date_amount").prop('readonly', true);
        $("#receive_registered_dress_date_amount").prop('readonly', true);
        $("#receive_cmi_amount").prop('readonly', true);
        $("#receive_document_sale_date_amount").prop('readonly', true);
        $("#receive_roof_receipt_date_amount").prop('readonly', true);
        $("#memo_no").prop('readonly', true);
        $("#receipt_avance").prop('readonly', true);
        $("#operation_fee_avance").prop('readonly', true);
        $('#description').prop('disabled', true);
        $('#car_characteristic').prop('disabled', true);
        $('#car_category').prop('disabled', true);
        $('#register_sign').prop('disabled', true);
        $('#document_date').prop('disabled', true);
        $('#receive_registered_dress_date').prop('disabled', true);
        $('#receive_cmi').prop('disabled', true);
        $('#receive_document_sale_date').prop('disabled', true);
        $("input[name=is_roof_receipt]").attr('disabled', true);
        $("input[name=is_lock_license_plate]").attr('disabled', true);
        $('#receive_roof_receipt_date').prop('disabled', true);
        $('#type_lock_license_plate').prop('disabled', true);
        $('#detail_lock_license_plate').prop('disabled', true);
        $('#send_registered_date').prop('disabled', true);

        $(document).ready(function() {
            var send_registered_date = new Date('{{ $d->send_registered_date }}');
            var registered_date = new Date($('#registered_date').val());

            // registered_date
            $('#registered_date').on('change', function() {
                var registered_date = new Date($('#registered_date').val());
                var day_diff = DateDifferenceRegister(send_registered_date, registered_date);
                $('#registered_date_amount').val(day_diff);
            });

            var registered_date_amount = DateDifferenceRegister(send_registered_date, registered_date);
            $('#registered_date_amount').val(registered_date_amount);

            function DateDifferenceRegister(send_registered_date, date) {
                var time_diff = date - send_registered_date;
                days_diff = Math.floor(time_diff / (1000 * 60 * 60 * 24));
                return day_diff = isNaN(days_diff) ? null : days_diff;
            }

            var receive_information_date = new Date('{{ $d->receive_information_date }}');
            var receive_roof_receipt_date = new Date($('#receive_roof_receipt_date').val());
            var receive_cmi = new Date($('#receive_cmi').val());
            var receive_registered_dress_date = new Date($('#receive_registered_dress_date').val());
            var document_date = new Date($('#document_date').val());
            var receive_document_sale_date = new Date($('#receive_document_sale_date').val());

            jQuery(function() {
                flatpickr("#document_date", {
                    minDate: receive_information_date,
                });

                flatpickr("#receive_registered_dress_date", {
                    minDate: receive_information_date,
                });
                flatpickr("#receive_cmi", {
                    minDate: receive_information_date,
                });
                flatpickr("#receive_document_sale_date", {
                    minDate: receive_information_date,
                });
                flatpickr("#receive_roof_receipt_date", {
                    minDate: receive_information_date,
                });
            });

            // receive_roof_receipt_date
            $('#receive_roof_receipt_date').on('change', function() {
                var receive_roof_receipt_date = new Date($('#receive_roof_receipt_date').val());
                var day_diff = DateDifference(receive_information_date, receive_roof_receipt_date);
                $('#receive_roof_receipt_date_amount').val(day_diff);
            });

            var receive_roof_receipt_date_amount = DateDifference(receive_information_date,
                receive_roof_receipt_date);
            $('#receive_roof_receipt_date_amount').val(receive_roof_receipt_date_amount);

            // receive_document_sale_date
            $('#receive_document_sale_date').on('change', function() {
                var receive_document_sale_date = new Date($('#receive_document_sale_date').val());
                var day_diff = DateDifference(receive_information_date, receive_document_sale_date);
                $('#receive_document_sale_date_amount').val(day_diff);
            });

            var receive_document_sale_date_amount = DateDifference(receive_information_date,
                receive_document_sale_date);
            $('#receive_document_sale_date_amount').val(receive_document_sale_date_amount);

            // receive_cmi
            $('#receive_cmi').on('change', function() {
                var receive_cmi = new Date($('#receive_cmi').val());
                var day_diff = DateDifference(receive_information_date, receive_cmi);
                $('#receive_cmi_amount').val(day_diff);
            });

            var receive_cmi_amount = DateDifference(receive_information_date, receive_cmi);
            $('#receive_cmi_amount').val(receive_cmi_amount);


            // receive_registered_dress_date
            $('#receive_registered_dress_date').on('change', function() {
                var receive_registered_dress_date = new Date($('#receive_registered_dress_date').val());
                var day_diff = DateDifference(receive_information_date, receive_registered_dress_date);
                $('#receive_registered_dress_date_amount').val(day_diff);
            });

            var receive_registered_dress_date_amount = DateDifference(receive_information_date,
                receive_registered_dress_date);
            $('#receive_registered_dress_date_amount').val(receive_registered_dress_date_amount);

            // document_date
            $('#document_date').on('change', function() {
                var document_date = new Date($('#document_date').val());
                var day_diff = DateDifference(receive_information_date, document_date);
                $('#document_date_amount').val(day_diff);
            });

            var document_date_amount = DateDifference(receive_information_date, document_date);
            $('#document_date_amount').val(document_date_amount);

            function DateDifference(receive_information_date, date) {
                var time_diff = date - receive_information_date;
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

            function calculateTotalProceed() {
                var tax = parseFloat($('#tax').val().replace(/,/g, ''));
                tax = isNaN(tax) ? 0 : tax;
                var service_fee = parseFloat($('#service_fee').val().replace(/,/g, ''));
                service_fee = isNaN(service_fee) ? 0 : service_fee;
                var total = tax + service_fee;

                total = parseFloat(total).toFixed(2).toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")


                $('#total').val(total);
            }

            $('#tax, #service_fee').on('change', function() {
                calculateTotalProceed();
            });

            calculateTotalProceed();


            $('input[name="is_roof_receipt"]').on("click", function() {
                if ($('input[name="is_roof_receipt"]:checked').val() ===
                    '{{ STATUS_ACTIVE }}') {
                    $('.check-roof').show();
                } else {
                    $('.check-roof').hide();
                }
            });

            $('input[name="is_lock_license_plate"]').on("click", function() {
                if ($('input[name="is_lock_license_plate"]:checked').val() ===
                    '{{ STATUS_ACTIVE }}') {
                    $('.check-lock').show();
                } else {
                    $('.check-lock').hide();
                }
            });

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

        $(".btn-save-form-register").on("click", function() {
            let storeUri = "{{ route('admin.registers.store-registered') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            formData.append('status', status);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
