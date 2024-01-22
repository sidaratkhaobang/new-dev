@extends('admin.layouts.layout')

@section('page_title', $page_title)
@push('styles')
    <style>
        .items-push {
             border: 1px solid #CBD4E1;
             border-radius: 6px;

        }
        .car-detail-wrapper {
            padding:0.75rem 1.25rem
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
    </style>
@endpush

@section('content')
@include('admin.components.creator')
<form id="save-form">
    @include('admin.vmi-cars.sections.btn-group')
    <div class="tab-content">
        <div class="tab-pane active show" id="first" role="tabpanel" aria-labelledby="first-tab" tabindex="0">
            @include('admin.cmi-cars.sections.rental')
            @include('admin.cmi-cars.sections.car-detail')
            @include('admin.vmi-cars.sections.act-detail')
            @include('admin.vmi-cars.sections.pa-detail')
            @include('admin.vmi-cars.sections.discount-detail')
            @include('admin.cmi-cars.sections.premium')
            <div class="block {{ __('block.styles') }}">
                <div class="block-content">
                    <x-forms.hidden id="id" name="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.vmi-cars.index',
                        'view' => ($mode == MODE_VIEW) ? true : null,
                        'manage_permission' => Actions::Manage . '_' . Resources::VMI
                    ]" />
                </div>
            </div>
        </div>
        <div class="tab-pane" id="second" role="tabpanel" aria-labelledby="second-tab" tabindex="0">
            @include('admin.vmi-cars.sections.coverage-info')
            <div class="block {{ __('block.styles') }}">
                <div class="block-content">
                    <x-forms.hidden id="id" name="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.vmi-cars.index',
                        'view' => ($mode == MODE_VIEW) ? true : null,
                        'manage_permission' => Actions::Manage . '_' . Resources::VMI
                    ]" />
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.vmi-cars.store')
])

@include('admin.cmi-cars.scripts.premium-script')
@include('admin.vmi-cars.scripts.disable-script')
@include('admin.vmi-cars.scripts.set-recovery-script')
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
