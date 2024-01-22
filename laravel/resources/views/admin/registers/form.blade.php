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
    <x-progress :type="ProgressStepEnum::REGISTER" :step="0"></x-progress>
    <form id="save-form">
        @include('admin.registers.sections.purchase-order-detail')
        @include('admin.registers.sections.car-detail')
        @include('admin.registers.sections.registered-detail')
        @include('admin.registers.sections.avance-detail')
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
                'btn_name' => __('registers.save_register'),
                'btn_draft_name' => __('registers.save_register_draft'),
                'icon_class_name' => 'icon-send',
            ]"></x-forms.submit-group>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.registers.store'),
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
        }

        $("#document_date_amount").prop('readonly', true);
        $("#receive_registered_dress_date_amount").prop('readonly', true);
        $("#receive_cmi_amount").prop('readonly', true);
        $("#receive_document_sale_date_amount").prop('readonly', true);

        $("#receive_roof_receipt_date_amount").prop('readonly', true);



        $(document).ready(function() {
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
    </script>
@endpush
