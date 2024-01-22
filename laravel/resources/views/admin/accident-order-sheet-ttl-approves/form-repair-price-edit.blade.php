@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('accident_orders.class_job_' . $accident_order->status),
            __('accident_orders.status_job_' . $accident_order->status),
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

        /* .show {
                                    display: block !important;
                                    opacity: 1;
                                    animation: fade 1s;
                                } */

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

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;

        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .badge-bg-pending-previous {
            background-color: #909395;
        }

        .badge-bg-check {
            background-color: #6f9c40;
        }

        .badge-bg-pending {
            background-color: #e69f17;
        }

        .vl {
            border-right: 1px solid #CBD4E1;
            height: 30px;
            padding-right: 20px;
        }

        .th-topic {
            background-color: #F6F8FC;
            height: 100px;

        }

        .vl-topic {
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table thead th:first-child {
            border-top-left-radius: 0px !important;
        }

        .table thead th:last-child {
            border-top-right-radius: 0px !important;
        }

        .show {
            display: fixed !important;
            /* opacity: 1;
                                        animation: fade 1s; */
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
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL" :id="$d->id" :model="get_class($d)" />
    <form id="save-form">
        @include('admin.accident-order-sheet-ttl-approves.sections.repair-detail-edit')
        @include('admin.accident-orders.sections.garage-detail')
        @include('admin.accident-orders.sections.repair-price-detail')
        @include('admin.accident-orders.sections.repair-price-total')
        @include('admin.accident-orders.sections.spare-part')
        @include('admin.accident-order-sheet-ttl-approves.sections.remark')
        @include('admin.accident-order-sheet-ttl-approves.submit')

        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="job_type" :value="null" />
        <x-forms.hidden id="job_id" :value="null" />
        @if ($approve_line_owner)
            <x-forms.hidden id="approve_line" :value="$approve_line_owner->id" />
        @endif
    </form>
    @include('admin.components.transaction-modal')
@endsection


@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.accident-informs.scripts.input-tag')
@include('admin.components.form-save', [
    'store_uri' => route('admin.accident-orders.store-edit-repair-price'),
])

@include('admin.accident-orders.scripts.repair-script')
@include('admin.accident-orders.scripts.spare-part-script')
@include('admin.accident-order-sheet-ttl-approves.scripts.update-status')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'garage_quotation_file',
    'max_files' => 100,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $garage_quotation_file,
])

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', ".toggle-table", function() {
                var tr_block = $(this).parent().next('.tr-block').toggle();
                $(tr_block).parent().next('.tr-block').toggle();
                $(this).children().toggleClass('icon-arrow-down text-muted').toggleClass(
                    'icon-arrow-up text-muted');
            })
        });

        $("#tls_cost").prop('readonly', true);
        $("#insurance_company").prop('disabled', true);
        $("#policy_no").prop('disabled', true);
        $("#coverage_start_date").prop('disabled', true);
        $("#coverage_end_date").prop('readonly', true);
        $("#save_claim_amount").prop('readonly', true);
        $('#spare_part_total').prop('disabled', true);
        $('#car_insurance').prop('disabled', true);
        $('#equipment_insurance').prop('disabled', true);


        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('#remark').prop('disabled', false);
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
                console.log('222');
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
            }
        });

        $(document).ready(function() {
            var selectedValue = $('#is_except_deductible :selected').val();

            if (selectedValue === '{{ \App\Enums\RightsEnum::NOT_USE_RIGHTS }}') {
                $("#reason_except_deductible_id").show();
            } else {
                $("#reason_except_deductible_id").hide();
            }
        });

        $('input[name="is_appointment[]"]').on("click", function() {
            var isChecked = $('input[name="is_appointment[]"]:checked').val();
            if (isChecked == {{ STATUS_ACTIVE }}) {
                document.getElementById("appointment").style.display = "block"
            } else {
                document.getElementById("appointment").style.display = "none"
            }
        });
    </script>
@endpush
