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
        @include('admin.accident-orders.sections.btn-group')
    @endif
    <form id="save-form">
        @include('admin.accident-orders.sections.appointment-detail')
        @include('admin.accident-orders.sections.participant-detail')
        @include('admin.accident-orders.sections.garage-detail')
        @include('admin.accident-orders.sections.repair-price-detail')
        @include('admin.accident-orders.sections.repair-price-total')
        @include('admin.accident-orders.sections.spare-part')
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="row push">
                    <div class="col-12 text-end">
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route('admin.accident-orders.index') }}">{{ __('lang.back') }}</a>
                        @if (!isset($view))
                            <button type="button"
                                class="btn btn-primary btn-custom-size btn-save-form ">{{ __('lang.save') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="job_type" :value="null" />
        <x-forms.hidden id="job_id" :value="null" />
        @include('admin.accident-orders.modals.appointment-modal')
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
@include('admin.components.select2-ajax', [
    'id' => 'true_leasing',
    'modal' => '#modal-appointment',
    'url' => route('admin.util.select2-accident.user-list'),
    // 'parent_id' => 'true_leasing',
])

@include('admin.components.select2-ajax', [
    'id' => 'insurance',
    'modal' => '#modal-appointment',
    'url' => route('admin.util.select2-accident.insurer-list'),
    // 'parent_id' => 'insurer',
])

{{-- @include('admin.components.select2-ajax', [
    'id' => 'wound_characteristics',
    'url' => route('admin.util.select2-accident.wound-list'),
]) --}}

@include('admin.accident-orders.scripts.repair-script')
@include('admin.accident-orders.scripts.spare-part-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'garage_quotation_file',
    'max_files' => 100,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $garage_quotation_file,
])

@push('scripts')
    <script>
        function appointmentModal() {
            $('#modal-appointment').modal('show');
        }
        // $("#amount_claim_customer").prop('disabled', true);
        // $("#amount_claim_tls").prop('disabled', true);
        // $("#compensation").prop('disabled', true);
        // $("#repair_type").prop('disabled', true);
        // $("#report_no").prop('disabled', true);
        // $("#claim_no").prop('disabled', true);
        // $("#claim_type_id").prop('disabled', true);
        // $("#responsible").prop('disabled', true);
        // $("#is_except_deductible").prop('disabled', true);
        // $("#reason_except_deductible").prop('disabled', true);

        $("#tls_cost").prop('readonly', true);
        $("#insurance_company").prop('disabled', true);
        $("#policy_no").prop('disabled', true);
        $("#coverage_start_date").prop('disabled', true);
        $("#coverage_end_date").prop('readonly', true);
        $("#save_claim_amount").prop('readonly', true);
        $('#spare_part_total').prop('disabled', true);
        $('#car_insurance').prop('disabled', true);
        $('#equipment_insurance').prop('disabled', true);
        $('#scheduled_completion_date').prop('disabled', true);
        $("#cradle_id").prop('disabled', true);
        $("#cradle_area_id").prop('disabled', true);
        // $("#appointment_date_modal").prop('disabled', true);
        // $("#appointment_place_modal").prop('disabled', true);
        $("#cradle_id_model").prop('disabled', true);
        $("#appointment_date").prop('disabled', true);
        $("#appointment_place").prop('disabled', true);
        $("#is_appointment_0").prop('disabled', true);



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



        $(document).ready(function() {
            function addDaysToRepairDate(days) {
                var repairDateInput = $('#repair_date').val();
                var amountCompletedInput = $('#amount_completed').val();


                if (repairDateInput === '' || amountCompletedInput === '') {
                    return;
                }

                var repairDate = new Date(repairDateInput);
                var amountCompleted = parseInt(amountCompletedInput, 10);

                if (isNaN(repairDate)) {
                    return;
                }

                var additionalDays = amountCompleted * days;
                repairDate.setDate(repairDate.getDate() + additionalDays);

                var formattedDate = repairDate.toISOString().split('T')[0];
                $('#scheduled_completion_date').val(formattedDate);
            }

            addDaysToRepairDate(1);

            $('#repair_date, #amount_completed').on('change', function() {
                addDaysToRepairDate(1);
            });



        });


        function sendMail() {
            var accident_repair_order_id = document.getElementById("id").value;
            var appointment_date = document.getElementById("appointment_date").value;
            var appointment_place = document.getElementById("appointment_place").value;

            var topic = document.getElementById("topic").value;
            var remark = document.getElementById("remark").value;
            var true_leasing_email = document.getElementById("true_leasing_email").value;
            var insurance_email = document.getElementById("insurance_email").value;
            var customer_email = document.getElementById("customer_email").value;
            var true_leasing = document.getElementById("true_leasing").value;
            var insurance = document.getElementById("insurance").value;
            var customer = document.getElementById("customer").value;
            var appointment_date_modal = document.getElementById("appointment_date_modal").value;
            var appointment_place_modal = document.getElementById("appointment_place_modal").value;

            if (!topic || !true_leasing || !insurance || !customer || !true_leasing_email || !insurance_email || !
                customer_email || !appointment_date_modal || !appointment_place_modal) {
                return warningAlert("{{ __('lang.required_field_inform') }}");
            }
            $("#is_appointment_0").prop("checked", true).trigger('change');

            // var $tags = document.querySelector('.js-tags');
            showLoading();
            axios.get("{{ route('admin.accident-orders.send-mail') }}", {
                params: {
                    accident_repair_order_id: accident_repair_order_id,
                    tags: tags,
                    appointment_place: appointment_place_modal,
                    appointment_date: appointment_date_modal,
                    topic: topic,
                    remark: remark,
                    true_leasing_email: true_leasing_email,
                    insurance_email: insurance_email,
                    customer_email: customer_email,
                    true_leasing: true_leasing,
                    insurance: insurance,
                    customer: customer,
                }
            }).then(response => {
                hideLoading();
                $("#modal-appointment").modal("hide");
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'ส่ง E-mail เรียบร้อยแล้ว',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            var is_checked_default = '{{$d->is_appointment}}';
            if (is_checked_default == "{{ STATUS_ACTIVE }}") {
                
                    $("#is_appointment_0").prop("checked", true).trigger('change');
                    document.getElementById("appointment").style.display = "block"
                } else {
                    $("#appointment").hide();
                }

            function handleCheckboxChange() {
                var is_checked = $('input[name="is_appointment[]"]:checked').val();
             
                if (is_checked == "{{ STATUS_ACTIVE }}") {
                    document.getElementById("appointment").style.display = "block"
                } else {
                    $("#appointment").hide();
                }

              
            }

            $('input[name="is_appointment[]"]').change(handleCheckboxChange);

            handleCheckboxChange();
        });

        $(document).ready(function() {
            var appointment_date = '{{ $d->appointment_date }}';
            $('#appointment_date').val(appointment_date);
            $('#appointment_date_hidden').val(appointment_date);
        });

        $(document).on('click', '#save_appointment', function() {
            var appointment_date = $('#appointment_date_modal').val();
            $('#appointment_date').val(appointment_date).trigger('change');
            $('#appointment_date_hidden').val(appointment_date).trigger('change');
        });

        $(document).ready(function() {
            var appointment_place = '{{ $d->appointment_place }}';
            $('#appointment_place').val(appointment_place);
            $('#appointment_place_hidden').val(appointment_place);
        });

        $(document).on('click', '#save_appointment', function() {
            var appointment_place = $('#appointment_place_modal').val();
            $('#appointment_place').val(appointment_place).trigger('change');
            $('#appointment_place_hidden').val(appointment_place).trigger('change');
        });

        $("#true_leasing").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.accident-orders.default-user') }}", {
                params: {
                    user: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    $("#true_leasing_email").val(response.data.data).trigger('change');
                }
            });
        });

        $("#insurance").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.accident-orders.default-insurer') }}", {
                params: {
                    insurance: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    $("#insurance_email").val(response.data.data).trigger('change');
                }
            });
        });
    </script>
@endpush
