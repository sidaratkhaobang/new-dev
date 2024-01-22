@extends('admin.layouts.layout')

@section('page_title', $page_title.' '.$d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status_vmi))
        {!! badge_render(
                                               __('insurance_car.status_' . $d?->status_vmi),
                                               __('insurance_car.class_' . $d?->status_vmi),
                                           ) !!}
    @endif
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection
@section('btn-nav')
    <div class="btn-group" style="position: sticky;left: 100%;">
        <button type="button" class="btn btn-primary dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" style="width: 140px;"><i class="icon-settings"></i>แก้ไข</button>
        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" style="width: 34px;"
                data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item btn-request" data-type="{{InsuranceCarEnum::BENEFITS}}">ผู้รับผลประโยชน์</a></li>
            <li><a class="dropdown-item btn-request" data-type="{{InsuranceCarEnum::INSURANCE_FUNDS}}-funds">ปรับวงเงินทุนประกัน</a></li>
            <li><a class="dropdown-item btn-request" data-type="{{InsuranceCarEnum::BUY_PROTECTION}}">ซื้อความคุ้มครอง</a></li>
        </ul>
    </div>
@endsection
@push('styles')
    <style>
        .items-push {
            border: 1px solid #CBD4E1;
            border-radius: 6px;

        }

        .car-detail-wrapper {
            padding: 0.75rem 1.25rem
        }

        .car-add-border {
            border-right: 1px solid #CBD4E1;
        }

        .car-info {
            padding-left: 5rem;
        }

        td {
            border: 1px solid #CBD4E1 !important;
        }

        .table-background-gray {
            background: var(--bs-table-striped-bg);
        }

        .block-btn > .block-content > .row {
            margin: 0px;
        }
    </style>
@endpush

@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        <x-forms.hidden id="request_type" :value="null" />
    @include('admin.vmi-cars.sections.btn-group')
    <div class="tab-content">
        <div class="tab-pane active show" id="first" role="tabpanel" aria-labelledby="first-tab" tabindex="0">
            @include('admin.insurance-car-cmi.sections.form-rental-detail')
            @include('admin.insurance-car-cmi.sections.form-car-details')
            @include('admin.insurance-car-vmi.sections.act-detail')
            @include('admin.insurance-car-vmi.sections.pa-detail')
            @include('admin.insurance-car-vmi.sections.discount-detail')
            @include('admin.insurance-car-cmi.sections.form-premium')
            <div class="block block-btn {{ __('block.styles') }}">
                <div class="block-content">
                    <x-forms.hidden id="id" name="id" :value="$d->id"/>
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.insurance-car.index',
                        'manage_permission' => Actions::Manage . '_' . Resources::VMI
                    ]"/>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="second" role="tabpanel" aria-labelledby="second-tab" tabindex="0">
            @include('admin.insurance-car-vmi.sections.coverage-info')
            <div class="block block-btn {{ __('block.styles') }}">
                <div class="block-content">
                    <x-forms.hidden id="id" name="id" :value="$d->id"/>
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.insurance-car.index',
                        'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCar
                    ]"/>
                </div>
            </div>
        </div>

    </div>
    </form>
    @include('admin.insurance-car-component.modals.modal-car-renew')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.insurance-car-component.scripts.car-accessory-scripts')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.insurance-car-vmi.store'),
])

@include('admin.cmi-cars.scripts.premium-script')
@include('admin.insurance-car-vmi.scripts.set-recovery-script')
@include('admin.components.select2-ajax', [
    'id' => 'insurance_package_id',
    'url' => route('admin.util.select2-vmi.insurance-packages'),
])

@push('scripts')
    <script>

        $('#year_act').prop('disabled', true);
        $('#sum_insured_total').prop('disabled', true);
        $('#premium_total').prop('disabled', true);
        $('#withholding_tax').prop('disabled', true);

        var cmi_status = @if ($d->status) @json($d->status) @else null @endif;
        var mode = @if ($mode) @json($mode) @else null @endif;
        if (cmi_status == '{{ InsuranceStatusEnum::PENDING }}') {
            disabledPremiumSection(true);
            disabledVMIBar(true);
            disabledPA(true);
            disabledDiscount(true);
        }

        if (mode === '{{ MODE_VIEW }}') {
            disabledPremiumSection(true);
            disabledVMIBar(true);
            disabledPA(true);
            disabledDiscount(true);
            disabledRecovery(true);
            $('#car_class_insurance_id').prop('disabled', true);
            $('#type_vmi').prop('disabled', true);
            $('#type_cmi').prop('disabled', true);
            $('#sum_insured_car').prop('disabled', true);
            $('#sum_insured_accessory').prop('disabled', true);
            $('#insurer_id').prop('disabled', true);
            $('#insurance_type').prop('disabled', true);
            $('#insurance_package_id').prop('disabled', true);
            $('#beneficiary_id').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#send_date').prop('disabled', true);
            $('#term_start_date').prop('disabled', true);
            $('#term_end_date').prop('disabled', true);

        }
        if (mode === '{{ MODE_UPDATE }}') {
            disabledPremiumSection(true);
            disabledVMIBar(true);
            disabledPA(true);
            disabledDiscount(true);
            disabledRecovery(true);
            $('#car_class_insurance_id').prop('disabled', true);
            $('#type_vmi').prop('disabled', true);
            $('#type_cmi').prop('disabled', true);
            $('#sum_insured_car').prop('disabled', true);
            $('#sum_insured_accessory').prop('disabled', true);
            $('#insurer_id').prop('disabled', true);
            $('#insurance_type').prop('disabled', true);
            $('#insurance_package_id').prop('disabled', true);
            $('#beneficiary_id').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#send_date').prop('disabled', true);
            $('#term_start_date').prop('disabled', true);
            $('#term_end_date').prop('disabled', true);

        }

        function disabledPremiumSection(is_disabled) {
            $('#premium').prop('disabled', is_disabled);
            $('#discount').prop('disabled', is_disabled);
            $('#stamp_duty').prop('disabled', is_disabled);
            $('#tax').prop('disabled', is_disabled);
            $('#premium_total').prop('disabled', is_disabled);
            $('#premium_total').prop('disabled', is_disabled);
            $('#withholding_tax').prop('disabled', is_disabled);
            $('#statement_no').prop('disabled', is_disabled);
            $('#tax_invoice_no').prop('disabled', is_disabled);
            $('#statement_date').prop('disabled', is_disabled);
            $('#account_submission_date').prop('disabled', is_disabled);
            $('#operated_date').prop('disabled', is_disabled);
            $('#status_pay_premium').prop('disabled', is_disabled);
        }

        function disabledVMIBar(is_disabled) {
            $('#receive_date').prop('disabled', is_disabled);
            $('#check_date').prop('disabled', is_disabled);
            $('#policy_reference_child_vmi').prop('disabled', is_disabled);
            $('#policy_reference_vmi').prop('disabled', is_disabled);
            $('#endorse_vmi').prop('disabled', is_disabled);
        }

        function disabledPA(is_disabled) {
            $('#pa').prop('disabled', is_disabled);
            $('#pa_and_bb').prop('disabled', is_disabled);
            $('#pa_per_endorsement').prop('disabled', is_disabled);
            $('#pa_total_premium').prop('disabled', is_disabled);
            $('#id_deductible').prop('disabled', is_disabled);
            $('#discount_deductible').prop('disabled', is_disabled);
            $('#fit_discount').prop('disabled', is_disabled);
            $('#fleet_discount').prop('disabled', is_disabled);
            $('#ncb').prop('disabled', is_disabled);
            $('#good_vmi').prop('disabled', is_disabled);
            $('#bad_vmi').prop('disabled', is_disabled);
        }

        function disabledDiscount(is_disabled) {
            $('#other_discount_percent').prop('disabled', is_disabled);
            $('#other_discount').prop('disabled', is_disabled);
            $('#gps_discount').prop('disabled', is_disabled);
            $('#total_discount').prop('disabled', is_disabled);
            $('#net_discount').prop('disabled', is_disabled);
            $('#cct').prop('disabled', is_disabled);
            $('#gross').prop('disabled', is_disabled);
        }

        function disabledRecovery(is_disabled) {
            $('#tpbi_person').prop('disabled', is_disabled);
            $('#tpbi_aggregate').prop('disabled', is_disabled);
            $('#tppd_aggregate').prop('disabled', is_disabled);
            $('#deductible').prop('disabled', is_disabled);
            $('#own_damage').prop('disabled', is_disabled);
            $('#fire_and_theft').prop('disabled', is_disabled);
            $('#deductible_car').prop('disabled', is_disabled);
            $('#pa_driver').prop('disabled', is_disabled);
            $('#pa_passenger').prop('disabled', is_disabled);
            $('#medical_exp').prop('disabled', is_disabled);
            $('#bail_bond').prop('disabled', is_disabled);
        }

        $('#sum_insured_car, #sum_insured_accessory').on('input', function () {
            var input1 = parseFloat($('#sum_insured_car').val().replace(/,/g, ''));
            var input2 = parseFloat($('#sum_insured_accessory').val().replace(/,/g, ''));
            if (isNaN(input1)) {
                $('#sum_insured_car').val(0);
            }

            if (isNaN(input2)) {
                $('#sum_insured_accessory').val(0);
            }

            if (!isNaN(input1) && !isNaN(input2)) {
                var sum = parseFloat(input1) + parseFloat(input2);
                sum = parseFloat(sum).toFixed(2).toLocaleString();
                sum_text = sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#sum_insured_total').val(sum_text);
            }
        });
        $(document).on('click', ".btn-request", function () {
            let type = $(this).data('type')
            if (type == "{{InsuranceCarEnum::BENEFITS}}") {
                request_benefits();
                disabledRecovery(true);
            }
            if (type == "{{InsuranceCarEnum::INSURANCE_FUNDS}}-funds") {
                request_insurance_funds()
                disabledRecovery(true);
            }
            if (type == "{{InsuranceCarEnum::BUY_PROTECTION}}") {
                request_buy_protection()
                disabledRecovery(false);
            }
        })


        function request_benefits() {
            $('#request_type').val("{{InsuranceCarEnum::BENEFITS}}")
            $('#beneficiary_id').prop('disabled', false)
        }

        function request_insurance_funds() {
            $('#request_type').val("{{InsuranceCarEnum::INSURANCE_FUNDS}}")
            $('#beneficiary_id').prop('disabled', true)
            $('#sum_insured_car').prop('disabled', false);
            $('#sum_insured_accessory').prop('disabled', false);
        }

        function request_buy_protection() {
            $('#request_type').val("{{InsuranceCarEnum::BUY_PROTECTION}}")
            $('#beneficiary_id').prop('disabled', true)
        }
    </script>
@endpush
