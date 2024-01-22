@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('borrow_cars.status_' . $d->status . '_class'),
            __('borrow_cars.status_' . $d->status . '_text'),
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
    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::BORROW_CAR" :id="$d->id" :model="get_class($d)" />
    @include('admin.components.creator')
    <form id="save-form">
        @include('admin.borrow-car-confirm-approves.sections.borrow-detail')
        @include('admin.borrow-car-confirm-approves.sections.borrower-detail')
        @include('admin.borrow-car-confirm-approves.sections.borrow-car-detail')
        @include('admin.borrow-car-confirm-approves.sections.borrow-place-detail')
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            {{-- @if (!in_array($d->status, [TransferCarEnum::SUCCESS])) --}}
            @include('admin.borrow-car-confirm-approves.submit')
            {{-- @endif --}}
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.borrow-car-confirm-approves.store'),
])

@include('admin.transfer-cars.scripts.update-status')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_class_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_borrow_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $optional_borrow_files,
])

@include('admin.components.select2-ajax', [
    'id' => 'parent_id',
    'url' => route('admin.util.select2.pr-parent'),
])

@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
        var enum_employee = '{{ \App\Enums\BorrowTypeEnum::BORROW_EMPLOYEE }}';
        var enum_other = '{{ \App\Enums\BorrowTypeEnum::BORROW_OTHER }}';
        var enum_status_confirm = '{{ \App\Enums\BorrowCarEnum::CONFIRM }}';
        var enum_status_pending_delivery = '{{ \App\Enums\BorrowCarEnum::PENDING_DELIVERY }}';
        var enum_status_in_process = '{{ \App\Enums\BorrowCarEnum::IN_PROCESS }}';
        if ($status) {
            $(".form-control").attr('disabled', true);
            $("input[name=is_need_driver]").attr('disabled', true);
            $("input[name=status_transfer]").attr('disabled', true);
            $("input[name=is_driver]").attr('disabled', true);
            $("input[name=is_driver_employee]").attr('disabled', true);
            $("input[name=is_driver_other]").attr('disabled', true);
        }

        $("#car_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.driving-jobs.default-car-license') }}", {
                params: {
                    car_id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#car_class").val(e.car_class_name);
                            $("#car_color").val(e.car_colors_name);
                        });
                    }
                }
            });
        });

        $(document).ready(function() {
            var status_default = '{{ $d->status }}';
            var borrow_type = '{{ $d->borrow_type }}';
            if (status_default != '') {
                $('#borrower').show();
                $('#car_borrow').show();
                var is_driver = '{{ $d->is_driver }}';


                if (is_driver == '{{ STATUS_ACTIVE }}') {
                    // console.log(is_driver)
                    $('.borrower-topic').show();

                    $("#contact_employee").prop('readonly', true);
                    $("#branch").prop('readonly', true);
                    $("#department").prop('readonly', true);
                    $("#place_employee").prop('readonly', true);
                    $("#delivery_date_employee").attr('disabled', true);
                    $("#role").prop('readonly', true);
                    $('.select-car-driver').show();
                    if (borrow_type == enum_other) { // other
                        $('.for-employee').hide();
                        $('.for-other').show();
                        $('#driver_need_other').show();
                    } else {
                        $('#driver_need_other').hide();
                    }

                    if (borrow_type == enum_employee) { // employee
                        $('#driver_need_employee').show();
                        $('.for-employee').show();
                        $('.for-other').hide();
                    } else {
                        $('#driver_need_employee').hide();
                    }
                } else if (is_driver == '{{ STATUS_INACTIVE }}') {
                    $('.borrower-topic').show();
                    $('.for-other').show();
                    $('.for-employee').hide();
                    $('#driver_need').hide();
                    $('.select-car-no-driver').show();
                    if (borrow_type == enum_other) {
                        $('.for-employee').hide();
                        $('.for-other').show();
                        $('#driver_need_other').hide();
                    } else {
                        $('.for-employee').show();
                        $('.for-other').hide();
                        $('#driver_need_other').hide();
                    }

                    if (borrow_type == enum_employee) {
                        $('#driver_need_employee').hide();
                    } else {
                        $('#driver_need_employee').hide();
                    }
                    // $('#driver_need').hide();
                } else {
                    $('.borrower-topic').hide();
                    $('.for-other').hide();
                    $('.for-employee').hide();
                    $('.select-car-driver').hide();
                    $('.select-car-no-driver').hide();
                }

                if (status_default == enum_status_confirm || status_default == enum_status_pending_delivery ||
                    status_default == enum_status_in_process) {
                    $("input[name=is_need_driver]").attr('disabled', true);
                    $("input[name=status_transfer]").attr('disabled', true);
                    $("input[name=is_driver]").attr('disabled', true);
                    $("input[name=is_driver_employee]").attr('disabled', true);
                    $("input[name=is_driver_other]").attr('disabled', true);
                    $("#borrow_branch_id").attr('disabled', true);
                    $("#car_class").prop('readonly', true);
                    $("#car_color").prop('readonly', true);
                    $("#contact_employee").prop('readonly', true);
                    $("#branch").prop('readonly', true);
                    $("#department").prop('readonly', true);
                    $("#role").prop('readonly', true);
                    $("#contact_other").prop('readonly', true);
                    $("#tel_other").prop('readonly', true);
                    $("#tel_employee").prop('readonly', true);
                    $("#place_other").prop('readonly', true);
                    $("#delivery_date_other").attr('disabled', true);
                    $("#place_employee").prop('readonly', true);
                    $("#delivery_date_employee").attr('disabled', true);

                    $("#borrow_id").attr('disabled', true);
                    $("#borrow_reason").prop('readonly', true);
                    $("#start_date").prop('readonly', true);
                    if (borrow_type == enum_employee) {
                        $("#end_date").prop('readonly', true);
                    }
                    $("#remark").prop('readonly', true);
                }
                if (status_default == enum_status_pending_delivery || status_default == enum_status_in_process) {
                    $("#car_id").attr('disabled', true);
                }

                if (status_default == enum_status_in_process) {
                    $("#pickup_place").prop('readonly', true);
                }


                if (borrow_type == enum_other && status_default == enum_status_in_process) {
                    $('#end_date_require').show();
                } else {
                    $('#end_date_require').hide();
                }

            }

        });
    </script>
@endpush
