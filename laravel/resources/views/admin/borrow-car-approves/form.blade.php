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
    </style>
@endpush

@section('content')
    @include('admin.components.creator')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="ConfigApproveTypeEnum::BORROW_CAR" :id="$d->id" :model="get_class($d)" />
    <form id="save-form">
        @include('admin.borrow-car-approves.sections.borrow-detail')
        @include('admin.borrow-car-approves.sections.borrower-detail')
        @include('admin.borrow-car-approves.sections.borrow-car-detail')
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
        @if ($approve_line_owner)
            <x-forms.hidden id="approve_line" :value="$approve_line_owner->id" />
        @endif
    </form>
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.borrow-cars.store'),
])

@include('admin.borrow-car-approves.scripts.update-status')
@include('admin.components.date-input-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_borrow_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $optional_borrow_files,
])


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';
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

            $("#car_id").attr('disabled', true);
            $("#car_class").prop('readonly', true);
            $("#car_color").prop('readonly', true);
            var status_default = '{{ $d->status }}';
            if (status_default != '') {
                $('#borrower').show();
                $('#car_borrow').show();
                var type = '{{ $d->borrow_type }}';
                console.log(type)
                var enum_employee = '{{ \App\Enums\BorrowTypeEnum::BORROW_EMPLOYEE }}';
                var enum_other = '{{ \App\Enums\BorrowTypeEnum::BORROW_OTHER }}';
                var enum_status_confirm = '{{ \App\Enums\BorrowCarEnum::CONFIRM }}';
                if (type == enum_employee) {
                    $('.borrower-topic').show();
                    $('.for-employee').show();
                    $('.for-other').hide();
                    $("#contact_employee").prop('readonly', true);
                    $("#branch").prop('readonly', true);
                    $("#department").prop('readonly', true);
                    $("#role").prop('readonly', true);
                } else if (type == enum_other) {
                    $('.borrower-topic').show();
                    $('.for-other').show();
                    $('.for-employee').hide();
                    $('#driver_need').hide();
                } else {
                    $('.borrower-topic').hide();
                    $('.for-other').hide();
                    $('.for-employee').hide();
                }

                var status = '{{ $d->is_driver }}';
                if (status == '{{ STATUS_ACTIVE }}' && type == enum_employee) {
                    $('#driver_need_employee').show();
                } else {
                    $('#driver_need_employee').hide();
                }

                if (status == '{{ STATUS_ACTIVE }}' && type == enum_other) {
                    $('#driver_need_other').show();
                } else {
                    $('#driver_need_other').hide();
                }
            }

            $('.borrower-topic').hide();
            $('#borrow_id').change(function() {
                $('#borrower').show();
                $('#car_borrow').show();
                var type = $('#borrow_id :selected').val();
                console.log(type)
                var enum_employee = '{{ \App\Enums\BorrowTypeEnum::BORROW_EMPLOYEE }}';
                var enum_other = '{{ \App\Enums\BorrowTypeEnum::BORROW_OTHER }}';
                if (type == enum_employee) {
                    $('.borrower-topic').show();
                    $('.for-employee').show();
                    $('.for-other').hide();
                    $("#contact_employee").prop('readonly', true);
                    $("#branch").prop('readonly', true);
                    $("#department").prop('readonly', true);
                    $("#role").prop('readonly', true);
                } else if (type == enum_other) {
                    $('.borrower-topic').show();
                    $('.for-other').show();
                    $('.for-employee').hide();
                    $('#driver_need').hide();
                } else {
                    $('.borrower-topic').hide();
                    $('.for-other').hide();
                    $('.for-employee').hide();
                }

                var status = $('input[name="is_driver_other"]:checked').val();
                console.log(status);
                if (status == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need').show();
                } else {
                    $('#driver_need').hide();
                }

                if (status_default == enum_status_confirm) {
                    console.log(status_default, enum_status_confirm);
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
                    $("#end_date").prop('readonly', true);
                    $("#remark").prop('readonly', true);
                }

            });

            $('input[name="is_driver_other"]').change(function() {
                var status = $('input[name="is_driver_other"]:checked').val();
                console.log(status);
                if (status == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need').show();
                } else {
                    $('#driver_need').hide();
                }
            });

        });
    </script>
@endpush
