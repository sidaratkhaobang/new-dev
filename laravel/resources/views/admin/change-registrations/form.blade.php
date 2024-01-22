@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <form id="save-form">
        <input type="hidden" name="change_registeration_id" value="{{$d?->id}}">
        @if($d?->type == ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-license-plate-tax-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.receive-sign-information')
            @include('admin.change-registrations.sections.receipt-address-information')
            @include('admin.change-registrations.sections.payment-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.delivery_information')
        @endif
        @if($d?->type == ChangeRegistrationTypeEnum::CHANGE_COLOR)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-color-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.withdrawl-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.service-information')
        @endif
        @if($d?->type == ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-characteristic-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.withdrawl-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.service-information')
        @endif
        @if($d?->type == ChangeRegistrationTypeEnum::CHANGE_TYPE)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-type-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.withdrawl-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.service-information')
        @endif
        @if($d?->type == ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-swap-license-plate-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.withdrawl-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.service-information')
        @endif
        @if($d?->type == ChangeRegistrationTypeEnum::CANCEL_USE_CAR)
            @include('admin.change-registrations.sections.car-detail')
            @include('admin.change-registrations.sections.request-change-cancle-car-detail')
            @include('admin.change-registrations.sections.contact-information')
            @include('admin.change-registrations.sections.document-registration-information')
            @include('admin.change-registrations.sections.withdrawl-information')
            @include('admin.change-registrations.sections.dlt-processing_information')
            @include('admin.change-registrations.sections.service-information')
        @endif
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.submit-group :optionals="[
                'url' => 'admin.change-registrations.index',
                'view' => empty($view) ? null : $view,
                'manage_permission' => Actions::Manage . '_' . Resources::ChangeRegistration
                ]"/>
            </div>
        </div>

    </form>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.form-save', [
    'store_uri' => route('admin.change-registrations.store'),
])
@include('admin.components.upload-image', [
    'id' => 'document_payment',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png',
    'mock_files' => $document_payment ?? null,
])
@if(in_array($d?->type,[
    ChangeRegistrationTypeEnum::CHANGE_COLOR,
    ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC,
    ChangeRegistrationTypeEnum::CHANGE_TYPE,
]))
    @include('admin.components.upload-image',
            [
             'id' => 'car_bodyfiles',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['car_body_files'] ?? [],
            ])
    @include('admin.components.upload-image',
            [
             'id' => 'receipt_file',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['receipt_file'] ?? [],
            ])
@endif

@if(strcmp($d?->type, ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE) === 0)
    @include('admin.components.upload-image',
            [
             'id' => 'registeration_book',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['register_files'] ?? [],
            ])
    @include('admin.components.upload-image',
            [
             'id' => 'power_attorney',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['power_attorney_files'] ?? [],
            ])
    @include('admin.components.upload-image',
            [
             'id' => 'letter_consent',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['letter_consent_files'] ?? [],
            ])
    @include('admin.components.upload-image',
            [
             'id' => 'citizen',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['citizen_files'] ?? [],
            ])

@endif

@if(strcmp($d?->type, ChangeRegistrationTypeEnum::CANCEL_USE_CAR) === 0)
    @include('admin.components.upload-image',
            [
             'id' => 'optional_file',
             'max_files' => 1,
             'accepted_files' => '.jpg,.jpeg,.png',
             'mock_files' => $media['optional_cancel_use_car_files'] ?? [],
            ])
@endif

@push('scripts')
    <script>
        $('#car_id').prop('disabled', true)
        $('#delivery_channel').prop('disabled', true)
        $('#wait_registration_book_duration_day').prop('disabled', true)
        $('#wait_power_attorney_tls_duration_day').prop('disabled', true)
        $('#wait_power_attorney_duration_day').prop('disabled', true)
        $('#summary_avance').prop('disabled', true)
        $('#summary_service').prop('disabled', true)
        @if(isset($view) && $view === true)
        $('.form-control').prop('disabled', true)
        $('select').prop('readonly', true)
        @endif
        @if($d?->status != ChangeRegistrationStatusEnum::WAITING_DOCUMENT)
        $('#payment_date').prop('disabled', true)
        $('#receive_registration_book_date').prop('disabled', true)
        $('#request_power_attorney_tls_date').prop('disabled', true)
        $('#receive_power_attorney_tls_date').prop('disabled', true)
        $('#request_power_attorney_date').prop('disabled', true)
        $('#receive_power_attorney_date').prop('disabled', true)
        $('#memo_no').prop('disabled', true)
        $('#receipt_avance').prop('disabled', true)
        $('#operation_fee_avance').prop('disabled', true)
        @endif
        @if($d?->status != ChangeRegistrationStatusEnum::WAITING_SEND_DLT)
        $('#processing_date').prop('disabled', true)
        $('#completion_registration_date').prop('disabled', true)
        $('[id^=recive_licen_list]').prop('disabled', true)

        @endif
        @if($d?->status != ChangeRegistrationStatusEnum::PROCESSING)
        $('#completion_duration_date').prop('disabled', true)
        $('#completion_date').prop('disabled', true)
        $('#return_registration_book_date').prop('disabled', true)
        $('#ems').prop('disabled', true)
        $('#delivery_date').prop('disabled', true)
        $('#receipt_date').prop('disabled', true)
        $('#receipt_no').prop('disabled', true)
        $('#receipt_fee').prop('disabled', true)
        $('#service_fee').prop('disabled', true)
        $('#summary_service').prop('disabled', true)
        @endif

        $(document).on('keyup change', "#request_registration_book_date,#receive_registration_book_date", function () {
            let diff_day = diffDays('request_registration_book_date', 'receive_registration_book_date')
            $('#wait_registration_book_duration_day').val(diff_day)
        })

        $(document).on('keyup change', "#request_power_attorney_tls_date,#receive_power_attorney_tls_date", function () {
            let diff_day = diffDays('request_power_attorney_tls_date', 'receive_power_attorney_tls_date')
            $('#wait_power_attorney_tls_duration_day').val(diff_day)
        })

        $(document).on('keyup change', "#request_power_attorney_date,#receive_power_attorney_date", function () {
            let diff_day = diffDays('request_power_attorney_date', 'receive_power_attorney_date')
            $('#wait_power_attorney_duration_day').val(diff_day)
        })
        $(document).on('keyup change', "#processing_date,#completion_date", function () {
            let diff_day = diffDays('processing_date', 'completion_date')
            $('#completion_duration_date').val(diff_day)
        })
        $(document).on('keyup change', "#receipt_avance,#operation_fee_avance", function () {
            let with_draw_total = calPriceSummary('receipt_avance', 'operation_fee_avance')
            $('#summary_avance').val(with_draw_total)
        })

        $(document).on('keyup change', "#receipt_fee,#service_fee", function () {
            let service_total = calPriceSummary('receipt_fee', 'service_fee')
            $('#summary_service').val(service_total)
        })

        function diffDays(first_element, second_element) {
            if ($(`#${first_element}`).length && $(`#${second_element}`).length) {
                let first_date = $(`#${first_element}`).val()
                let second_date = $(`#${second_element}`).val()
                if (!first_date || !second_date) {
                    return 0
                }
                let first_date_format = new Date(first_date);
                let second_date_format = new Date(second_date);
                let time_diff = second_date_format - first_date_format;
                let days_diff = time_diff / (1000 * 60 * 60 * 24);
                return days_diff;
            }
            return 0;
        }

        function calPriceSummary(first_element, second_element) {
            if ($(`#${first_element}`).length && $(`#${second_element}`).length) {
                let receipt_avance = $(`#${first_element}`).val()
                let operation_fee_avance = $(`#${second_element}`).val()
                if (!receipt_avance || !operation_fee_avance) {
                    return 0
                }
                let receipt_avance_float = parseFloat(receipt_avance.replace(/,/g, ''));
                let operation_fee_avance_float = parseFloat(operation_fee_avance.replace(/,/g, ''));
                if (isNaN(receipt_avance_float) || isNaN(operation_fee_avance_float)) {
                    return 0;
                }
                let summary_avance = receipt_avance_float + operation_fee_avance_float;
                let formatt_sum = summary_avance.toLocaleString();
                return formatt_sum;
            }
            return 0;
        }

    </script>
@endpush

