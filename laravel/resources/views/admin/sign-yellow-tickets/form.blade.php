@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .table thead th:first-child {
            border-top-left-radius: 0px !important;
        }

        .table thead th:last-child {
            border-top-right-radius: 0px !important;
        }

        .change-only-read {
            pointer-events: none;
            background-color: #f1f1f1 !important;
            color: #898989 !important;
        }
    </style>
@endpush

@section('content')
    @include('admin.components.creator')
    @if ($d->status || !is_null($d->status))
        <x-progress :type="ProgressStepEnum::SIGN_YELLOW_TICKET" :step="$d->step"></x-progress>
    @endif
    <form id="save-form">
        @include('admin.sign-yellow-tickets.sections.find-detail')
        @include('admin.sign-yellow-tickets.sections.car-detail')
        @if (in_array($d->status, [SignYellowTicketStatusEnum::DRAFT, SignYellowTicketStatusEnum::WAITING_WRONG]) ||
                is_null($d->status))
            @include('admin.sign-yellow-tickets.sections.lawsuit-detail')
        @else
            @include('admin.sign-yellow-tickets.sections.lawsuit-detail-paid-dlt')
        @endif

        <x-forms.hidden id="id" :value="$d->id" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="row push me-1">
                <div class="col-sm-12 text-end">
                    @if (isset($url))
                        <a class="btn btn-outline-secondary btn-custom-size"
                            href="{{ route($url) }}">{{ __('lang.back') }}</a>
                    @endif
                    @include('admin.sign-yellow-tickets.submit')

                </div>
            </div>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.upload-image-scripts')
@include('admin.sign-yellow-tickets.scripts.sign-yellow-ticket-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'receipt_files',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
    'show_url' => true,
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.select2-ajax', [
    'id' => 'province',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2.provinces'),
])

@include('admin.components.select2-ajax', [
    'id' => 'responsible',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2-sign-yellow-ticket.responsible'),
])

@include('admin.components.select2-ajax', [
    'id' => 'training',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2-sign-yellow-ticket.training'),
])

@include('admin.components.select2-ajax', [
    'id' => 'mistake',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2-sign-yellow-ticket.mistake'),
])

@include('admin.components.select2-ajax', [
    'id' => 'is_payment_fine',
    'modal' => '#modal-lawsuit',
    'url' => route('admin.util.select2-sign-yellow-ticket.payment'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.form-save', [
    'store_uri' => route('admin.sign-yellow-tickets.store'),
])


@push('scripts')
    <script>
        $status = '{{ isset($view) }}';

        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
            $('input[type=radio]').prop('disabled', true);
        }

        $current_status = '{{ $d->status }}';
        if ($current_status == '{{ SignYellowTicketStatusEnum::WAITING_WRONG }}' || $current_status ==
            '{{ SignYellowTicketStatusEnum::WAITING_PAY_DLT }}' || $current_status ==
            '{{ SignYellowTicketStatusEnum::WAITING_PAY_FINE }}') {
            $('#car_id').prop('disabled', true);
            $('#receive_find_date').prop('disabled', true);
            $('#incident_date').prop('disabled', true);
            $('#lawsuit_detail').prop('disabled', true);
            $('#province').prop('disabled', true);
            $('#amount').prop('disabled', true);
            $('#responsible').prop('disabled', true);
            $('#training').prop('disabled', true);
        }

        if ($current_status == '{{ SignYellowTicketStatusEnum::WAITING_PAY_DLT }}') {
            $('#mistake').prop('disabled', true);
            $('#notification_date').prop('disabled', true);
        }

        if ($current_status == '{{ SignYellowTicketStatusEnum::WAITING_PAY_FINE }}') {
            $('#mistake').prop('disabled', true);
            $('#notification_date').prop('disabled', true);
            $('#receipt_no').prop('disabled', true);
            $('#payment_fine_date').prop('disabled', true);
            $('#amount_total').prop('disabled', true);

        }


        $("#amount_wait_transfer_kit_date").prop('readonly', true);
        $("#amount_wait_power_attorney_tls_date").prop('readonly', true);
        $("#amount_day_wait_cmi").prop('readonly', true);
        $("#amount_wait_register_book_date").prop('readonly', true);

        $("#driver_type").prop('readonly', true);
        $("#driver").prop('readonly', true);
        $("#tel").prop('readonly', true);
        $("#engine_no").prop('readonly', true);
        $("#chassis_no").prop('readonly', true);
        $("#car_class").prop('readonly', true);
        $("#branch").prop('readonly', true);
        $("#engine_no").prop('readonly', true);
        $("#chassis_no").prop('readonly', true);
        $("#car_status").prop('readonly', true);

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // // delete data
                    // let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    // if (pending_delete_ids.length > 0) {
                    //     pending_delete_ids.forEach((id) => {
                    //         formData.append(dropzone_id + '__pending_delete_ids[]', id);
                    //     });
                    // }
                });
            }
            if (addSignYellowTicketVue) {
                let data = addSignYellowTicketVue.getFiles();

                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.receipt_files && item.receipt_files.length > 0) {
                            // console.log('kk')
                            item.receipt_files.forEach(function(file) {
                                // console.log('dd')
                                if ((!file.saved) && (file.raw_file)) {
                                    // console.log(file.raw_file)
                                    formData.append('receipt_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
            }

            if (addSignYellowTicketVue) {
                let data = addSignYellowTicketVue.getFiles();
                if (addSignYellowTicketVue.receipt_files_delete) {
                    addSignYellowTicketVue.receipt_files_delete.forEach((receipt_files_delete) => {
                        formData.append('receipt_files__pending_delete_ids[]', receipt_files_delete);
                    });
                }
            }
            return formData;
        }

        $(".btn-save-form-sign-yellow-ticket").on("click", function() {
            let storeUri = "{{ route('admin.sign-yellow-tickets.store') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            if (status) {
                formData.append('status', status);
            }
            saveForm(storeUri, formData);
        });

        $(".btn-save-form-sign-yellow-ticket-mistake").on("click", function() {
            let storeUri = "{{ route('admin.sign-yellow-tickets.store-mistake') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            if (status) {
                formData.append('status', status);
            }
            saveForm(storeUri, formData);
        });

        $(".btn-save-form-sign-yellow-ticket-paid").on("click", function() {
            let storeUri = "{{ route('admin.sign-yellow-tickets.store-paid') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            if (status) {
                formData.append('status', status);
            }
            saveForm(storeUri, formData);
        });

        function updateCarData(carId) {
            axios.get("{{ route('admin.sign-yellow-tickets.default-data-car') }}", {
                params: {
                    car_id: carId,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        $("#engine_no").val(response.data.data.engine_no);
                        $("#chassis_no").val(response.data.data.chassis_no);
                        $("#car_class").val(response.data.data.car_class);
                        $("#branch").val(response.data.data.branch);
                        $("#engine_no").val(response.data.data.engine_no);
                        $("#chassis_no").val(response.data.data.chassis_no);
                        $("#car_status").val(response.data.data.status);
                        // $("#car_id").val(response.data.data.license_plate);
                    } else {
                        $("#engine_no").val();
                        $("#chassis_no").val();
                        $("#car_class").val();
                        $("#branch").val();
                        $("#engine_no").val();
                        $("#chassis_no").val();
                        $("#car_status").val();
                        // $("#car_id").val();
                    }
                }
            });
        }

        $(".btn-save-form-sign-yellow-ticket-paid-fine").on("click", function() {
            let storeUri = "{{ route('admin.sign-yellow-tickets.store-paid-fine') }}";
            var formData = appendFormData();
            var status = $(this).attr('data-status');
            if (status) {
                formData.append('status', status);
            }
            saveForm(storeUri, formData);
        });

        $("#car_id").on('select2:select', function(e) {
            var data = e.params.data;
            $('#car_id_hidden').val(data.id)
            updateCarData(data.id);

        });

        $("#car_id").on('change', function() {
            var carId = $(this).val();
            $('#car_id_hidden').val(carId)
            console.log(carId);
            updateCarData(carId);
            if (carId) {
                $('#openModal').show();
            } else {
                $('#openModal').hide();
            }
        });

        $(document).ready(function() {
            var carId = $("#car_id_hidden").val();
            console.log('carId')
            if (carId) {
                $('#car_id_hidden').val(carId)
                updateCarData(carId);
            }
            if (carId) {
                $('#openModal').show();
            } else {
                $('#openModal').hide();
            }
        });


        $("#incident_date").on("change", function() {
            var incident_date = $(this).val();
            var car_id = $("#car_id").val();
            if (incident_date) {
                axios.get("{{ route('admin.sign-yellow-tickets.driving-job') }}", {
                    params: {
                        incident_date: incident_date,
                        car_id: car_id,
                    }
                }).then(response => {
                    if (response.data.success) {
                        // if (response.data.data) {
                        $("#driver_type").val(response.data.type);
                        $("#driver").val(response.data.name);
                        $("#tel").val(response.data.tel);
                        // $("#branch").val(response.data.data.branch);
                        // } else {
                        //     $("#engine_no").val();
                        //     $("#chassis_no").val();
                        //     $("#car_class").val();
                        //     $("#branch").val();
                        // }
                    }
                });
            }
        });
    </script>
@endpush
