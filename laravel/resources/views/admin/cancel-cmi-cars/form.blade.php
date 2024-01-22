@extends('admin.layouts.layout')
@section('history')
    @include('admin.components.btns.history')
@endsection
@section('page_title_sub')
    @if (isset($cancel_insurance->status))
        {!! badge_render(
            __('cmi_cars.class_' . $cancel_insurance->status),
            __('cmi_cars.status_' . $cancel_insurance->status),
        ) !!}
    @endif
@endsection
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

        /* td {
                border: 1px solid #CBD4E1 !important;
            } */
    </style>
@endpush

@section('content')
    @include('admin.components.creator')
        @include('admin.cancel-cmi-cars.sections.btn-group')
        <div class="tab-content">
            <div class="tab-pane" id="first" role="tabpanel" aria-labelledby="first-tab" tabindex="0">
                @include('admin.cmi-cars.sections.rental')
                @include('admin.cmi-cars.sections.car-detail')
                @include('admin.cmi-cars.sections.act-detail')
                @include('admin.cmi-cars.sections.premium')
                <div class="block {{ __('block.styles') }}">
                    <div class="block-content">
                        <x-forms.submit-group :optionals="[
                            'url' => 'admin.cancel-cmi-cars.index', 
                            'view' => true,
                            'manage_permission' => Actions::Manage . '_' . Resources::CMI
                        ]" />
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="second" role="tabpanel" aria-labelledby="second-tab" tabindex="0">
                @include('admin.cmi-cars.sections.coverage-info')
                <div class="block {{ __('block.styles') }}">
                    <div class="block-content">
                        <x-forms.submit-group :optionals="[
                            'url' => 'admin.cancel-cmi-cars.index', 
                            'view' => true,
                            'manage_permission' => Actions::Manage . '_' . Resources::CMI
                        ]" />
                    </div>
                </div>
            </div>
            <div class="tab-pane active show" id="third" role="tabpanel" aria-labelledby="third-tab" tabindex="0">
                <form id="save-form">
                    @include('admin.cancel-cmi-cars.sections.cancel-info')
                    @include('admin.cancel-cmi-cars.sections.refund')
                    <div class="block {{ __('block.styles') }}">
                        <div class="block-content">
                            <x-forms.hidden id="id" name="id" :value="$cancel_insurance->id" />
                            <x-forms.submit-group :optionals="[
                                'url' => 'admin.cancel-cmi-cars.index', 
                                'view' => ($mode == MODE_VIEW) ? true : null,
                                'manage_permission' => Actions::Manage . '_' . Resources::CMI
                            ]" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.cancel-cmi-cars.scripts.refund-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.cancel-cmi-cars.store')
])

@push('scripts')
    <script>
        $('#reason').prop('disabled', true);
        $('#request_cancel_date').prop('disabled', true);
        $('#refund_total').prop('disabled', true);
        $('#refund_withholding_tax').prop('disabled', true);

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
        $('#year_act').prop('disabled', true);
        $('#sum_insured_total').prop('disabled', true);
        $('#premium_total').prop('disabled', true);
        $('#withholding_tax').prop('disabled', true);
        $('#premium').prop('disabled', true);
        $('#discount').prop('disabled', true);
        $('#stamp_duty').prop('disabled', true);
        $('#tax').prop('disabled', true);
        $('#statement_no').prop('disabled', true);
        $('#tax_invoice_no').prop('disabled', true);
        $('#statement_date').prop('disabled', true);
        $('#account_submission_date').prop('disabled', true);
        $('#operated_date').prop('disabled', true);
        $('#status_pay_premium').prop('disabled', true);
        $('#receive_date').prop('disabled', true);
        $('#check_date').prop('disabled', true);
        $('#number_bar_cmi').prop('disabled', true);
        $('#policy_reference_cmi').prop('disabled', true);
        $('#endorse_cmi').prop('disabled', true);

        var mode = @if ($mode) @json($mode) @else null @endif;
        if (mode === '{{ MODE_VIEW }}') {
            $('#cancel_remark').prop('disabled', true);
            $('#actual_cancel_date').prop('disabled', true);
            $('#refund').prop('disabled', true);
            $('#refund_stamp').prop('disabled', true);
            $('#refund_vat').prop('disabled', true);
            $('#credit_note').prop('disabled', true);
            $('#credit_note_date').prop('disabled', true);
            $('#refund_check_date').prop('disabled', true);
            $('#send_account_date').prop('disabled', true);
        }

    </script>
@endpush

