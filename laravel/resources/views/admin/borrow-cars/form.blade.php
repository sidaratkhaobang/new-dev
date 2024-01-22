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

        @include('admin.borrow-cars.sections.borrow-detail')
        @include('admin.borrow-cars.sections.borrower-detail')
        {{-- @include('admin.borrow-cars.sections.borrow-car-detail') --}}
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
                @include('admin.borrow-cars.submit')
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.borrow-cars.store'),
])

@include('admin.transfer-cars.scripts.update-status')
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
        var enum_employee = '{{ \App\Enums\BorrowTypeEnum::BORROW_EMPLOYEE }}';
        var enum_other = '{{ \App\Enums\BorrowTypeEnum::BORROW_OTHER }}';
        var enum_status_confirm = '{{ \App\Enums\BorrowCarEnum::CONFIRM }}';
        var type = '{{ $d->borrow_type }}';
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
                
                console.log(type)

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

                var is_driver = '{{ $d->is_driver }}';
                
                console.log(is_driver);
                if (is_driver == '{{ STATUS_ACTIVE }}' && type == enum_employee) {
                    $('#driver_need_employee').show();
                } else {
                    $('#driver_need_employee').hide();
                }

                if (is_driver == '{{ STATUS_ACTIVE }}' && type == enum_other) {
                    $('#driver_need_other').show();
                } else {
                    $('#driver_need_other').hide();
                }
            }

            $('.borrower-topic').hide();
            $('#borrow_id').change(function() {
                $('#borrower').show();
                $('#car_borrow').show();
                // $('#driver_need').hide();
                // $('input[name="is_driver_employee"]').val(2);
                // $('input[name="is_driver_other"]').val(2);
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
                    $('#end_date_require').show();
                    // $('input[name="is_driver_employee"]').val(1);
                } else if (type == enum_other) {
                    $('.borrower-topic').show();
                    $('.for-other').show();
                    $('.for-employee').hide();
                    // $('#driver_need').hide();
                    
                    $('#end_date_require').hide();
                    // $('input[name="is_driver_other"]').val(1);
                } else {
                    $('.borrower-topic').hide();
                    $('.for-other').hide();
                    $('.for-employee').hide();
                    
                }

                
                var is_driver_other_borrow = $('input[name="is_driver_other"]:checked').val();
                var is_driver_employee_borrow = $('input[name="is_driver_employee"]:checked').val();
                // console.log(is_driver_other_borrow);
                if (is_driver_other_borrow == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need_other').show();
                } else {
                    $('#driver_need_other').hide();
                }

                if (is_driver_employee_borrow == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need_employee').show();
                } else {
                    $('#driver_need_employee').hide();
                }

            });

            $('input[name="is_driver_other"]').change(function() {
                var is_driver_other = $('input[name="is_driver_other"]:checked').val();
                // console.log(is_driver_other);
                if (is_driver_other == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need_other').show();
                } else {
                    $('#driver_need_other').hide();
                }
            });

            $('input[name="is_driver_employee"]').change(function() {
                var is_driver_employee = $('input[name="is_driver_employee"]:checked').val();
                console.log(is_driver_employee);
                if (is_driver_employee == '{{ STATUS_ACTIVE }}') {
                    $('#driver_need_employee').show();
                } else {
                    $('#driver_need_employee').hide();
                }
            });

        });
    </script>
@endpush
