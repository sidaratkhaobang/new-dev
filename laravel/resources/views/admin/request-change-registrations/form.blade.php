@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('change_registrations.status_' . $d->status . '_class'),
            __('change_registrations.status_' . $d->status . '_text'),
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
    {{-- <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" /> --}}

    @include('admin.components.creator')
    <form id="save-form">
        <x-blocks.block :title="__('change_registrations.job_type')">
            @include('admin.request-change-registrations.sections.request-type')
        </x-blocks.block>

        <div id="car_detail" class="section_detail">
            <x-blocks.block :title="__('change_registrations.car_detail')">
                @include('admin.request-change-registrations.sections.car-detail')
            </x-blocks.block>
        </div>

        <div id="copy_tax_detail" class="section_detail">
            <x-blocks.block :title="__('change_registrations.copy_tax_detail')">
                @include('admin.request-change-registrations.sections.license-plate-tax-copy-detail')
            </x-blocks.block>
        </div>

        <div id="change_color" class="section_detail">
            <x-blocks.block :title="__('change_registrations.change_color_detail')">
                @include('admin.request-change-registrations.change-color-sections.change-color-detail')
            </x-blocks.block>
        </div>

        <div id="change_characteristic" class="section_detail">
            <x-blocks.block :title="__('change_registrations.change_characteristic_detail')">
                @include('admin.request-change-registrations.change-characteristic-sections.change-characteristic-detail')
            </x-blocks.block>
        </div>

        <div id="change_type" class="section_detail">
            <x-blocks.block :title="__('change_registrations.change_type_detail')">
                @include('admin.request-change-registrations.change-type-sections.change-type-detail')
            </x-blocks.block>
        </div>

        <div id="swap_license_plate" class="section_detail">
            <x-blocks.block :title="__('change_registrations.swap_license_plate')">
                @include('admin.request-change-registrations.swap-license-plate-sections.swap-license-plate-detail')
            </x-blocks.block>
        </div>

        <div id="cancel_use_car" class="section_detail">
            <x-blocks.block :title="__('change_registrations.cancel_use_car')">
                @include('admin.request-change-registrations.cancel-use-car-sections.cancel-use-car-detail')
            </x-blocks.block>
        </div>

        <div id="contact_detail" class="section_detail">
            <x-blocks.block :title="__('change_registrations.contact_detail')">
                @include('admin.request-change-registrations.sections.contact-detail')
            </x-blocks.block>
        </div>

        <div id="receive_sign" class="section_detail">
            <x-blocks.block :title="__('change_registrations.receive_sign')">
                @include('admin.request-change-registrations.sections.receive-detail')
            </x-blocks.block>
        </div>

        <div id="receipt_address_detail" class="section_detail">
            <x-blocks.block :title="__('change_registrations.receipt_address_detail')">
                @include('admin.request-change-registrations.sections.receipt-address-detail')
            </x-blocks.block>
        </div>
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.request-change-registrations.submit')
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.request-change-registrations.store'),
])

@include('admin.transfer-cars.scripts.update-status')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_class_swap',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'contact_user_id',
    'url' => route('admin.util.select2.users'),
])

@include('admin.components.select2-ajax', [
    'id' => 'recipient_user_id',
    'url' => route('admin.util.select2.users'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $optional_files,
    'show_url' => true,
])

 @include('admin.components.upload-image', [
    'id' => 'car_body_color_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $car_body_color_files,
    'show_url' => true,
])


@include('admin.components.upload-image', [
    'id' => 'receipt_change_color_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $receipt_change_color_files,
    'show_url' => true,
])


 @include('admin.components.upload-image', [
    'id' => 'car_body_characteristic_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $car_body_characteristic_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'receipt_change_characteristic_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $receipt_change_characteristic_files,
    'show_url' => true,
]) 

@include('admin.components.upload-image', [
    'id' => 'car_body_type_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $car_body_type_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'receipt_change_type_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $receipt_change_type_files,
    'show_url' => true,
]) 

@include('admin.components.upload-image', [
    'id' => 'register_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $register_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'power_attorney_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $power_attorney_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'letter_consent_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $letter_consent_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'citizen_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $citizen_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'optional_cancel_use_car_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.png,.pdf',
    'mock_files' => $optional_cancel_use_car_files,
    'show_url' => true,
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }

        $('.department_tls').prop('disabled', true);
        $('.role_tls').prop('disabled', true);

        function updateSections() {
            $('.section_detail').hide();
            var request_type_id = $("input[type=radio][name=request_type_id]:checked").val();
            if (request_type_id == '{{ ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY }}') {
                licensePlateTaxCopyType();
            } else if (request_type_id == '{{ ChangeRegistrationTypeEnum::CHANGE_COLOR }}') {
                changeColorType();
            } else if (request_type_id == '{{ ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC }}') {
                changeCharacteristicType();
            } else if (request_type_id == '{{ ChangeRegistrationTypeEnum::CHANGE_TYPE }}') {
                changeType();
            } else if (request_type_id == '{{ ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE }}') {
                changeSwapLicensePlateType();
            }else if (request_type_id == '{{ ChangeRegistrationTypeEnum::CANCEL_USE_CAR }}') {
                cancelUseCar();
            }
        }

        $(document).ready(function() {
            updateSections();
        });

        $("input[type=radio][name=request_type_id]").on('change', function() {
            updateSections();
        });

        function licensePlateTaxCopyType() {
            $('#car_detail').show();
            $('#copy_tax_detail').show();
            $('#contact_detail').show();
            $('#receive_sign').show();
            $('#receipt_address_detail').show();
        }

        function changeColorType() {
            $('#car_detail').show();
            $('#contact_detail').show();
            $('#change_color').show();
        }

        function changeCharacteristicType() {
            $('#car_detail').show();
            $('#contact_detail').show();
            $('#change_characteristic').show();
        }

        function changeType() {
            $('#car_detail').show();
            $('#contact_detail').show();
            $('#change_type').show();
        }

        function changeSwapLicensePlateType() {
            $('#car_detail').show();
            $('#contact_detail').show();
            $('#swap_license_plate').show();
        }

        function cancelUseCar() {
            $('#car_detail').show();
            $('#contact_detail').show();
            $('#cancel_use_car').show();
        }
        
        function clearContact() {  
            $("#name_contact").val('');
            $("#tel_contact").val('');
            $("#email_contact").val('');
            $("#address_contact").val('');
            $("#contact_user_id").val(null).trigger('change');
            $("#department_tls_contact").val('');
            $("#role_tls_contact").val('');
            $("#tel_contact_tls").val('');
            $("#email_contact_tls").val('');
        }

        function clearRecipient() {  
            $("#name_recipient").val('');
            $("#tel_recipient").val('');
            $("#email_recipient").val('');
            $("#address_recipient").val('');
            $("#recipient_user_id").val(null).trigger('change');
            $("#department_tls_recipient").val('');
            $("#role_tls_recipient").val('');
            $("#tel_recipient_tls").val('');
            $("#email_recipient_tls").val('');
        }

    </script>
@endpush
