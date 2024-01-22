@extends('admin.layouts.layout')

@section('page_title', $page_title)
@push('styles')
    <style>

        .car-detail-wrapper {
            border: 1px solid #CBD4E1;
             border-radius: 6px;
            padding: 0.25rem 1.25rem
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
    </style>
@endpush

@section('content')
@include('admin.components.creator')
<form id="save-form">
    @include('admin.insurance-car-cmi-renew.sections.btn-group')
    <div class="tab-content">
        <div class="tab-pane active show" id="first" role="tabpanel" aria-labelledby="first-tab" tabindex="0">
            @include('admin.insurance-car-cmi-renew.sections.rental')
            @include('admin.insurance-car-cmi-renew.sections.car-detail')
            @include('admin.insurance-car-cmi-renew.sections.act-detail')
            @include('admin.insurance-car-cmi-renew.sections.premium')
            <div class="block {{ __('block.styles') }}">
                <div class="block-content">
                    <x-forms.hidden id="id" name="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.insurance-cmi-renew.index',
                        'view' => ($mode == MODE_VIEW) ? true : null,
                        'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCarCmiRenew
                    ]" />
                </div>
            </div>
        </div>
        <div class="tab-pane" id="second" role="tabpanel" aria-labelledby="second-tab" tabindex="0">
            @include('admin.insurance-car-cmi-renew.sections.coverage-info')

        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')

@include('admin.cmi-cars.scripts.premium-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.insurance-cmi-renew.store')
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
            disabledCMIBar(true);
        }

        if (mode === '{{ MODE_VIEW }}') {
            disabledPremiumSection(true);
            disabledCMIBar(true);
            $('#car_class_insurance_id').prop('disabled', true);
            $('#type_vmi').prop('disabled', true);
            $('#type_cmi').prop('disabled', true);
            $('#sum_insured_car').prop('disabled', true);
            $('#sum_insured_accessory').prop('disabled', true);
            $('#insurer_id').prop('disabled', true);
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

        function disabledCMIBar(is_disabled) {
            $('#receive_date').prop('disabled', is_disabled);
            $('#check_date').prop('disabled', is_disabled);
            $('#number_bar_cmi').prop('disabled', is_disabled);
            $('#policy_reference_cmi').prop('disabled', is_disabled);
            $('#endorse_cmi').prop('disabled', is_disabled);
        }

        $('#sum_insured_car, #sum_insured_accessory').on('input', function() {
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
    </script>
@endpush
