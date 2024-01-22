@extends('admin.layouts.layout')
@section('page_title', $page_title . ' ' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('transfer_cars.status_' . $d->status . '_class'),
            __('transfer_cars.status_' . $d->status . '_text'),
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
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />


    <form id="save-form">
        @include('admin.transfer-cars.sections.transfer-detail')
        @include('admin.transfer-cars.sections.transfer-confirm')
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="status" :value="$d->status" />
        @if (isset($status_confirm))
            <x-forms.hidden id="status_confirm" :value="$status_confirm" />
        @endif
    </form>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.transfer-cars.store'),
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
    'id' => 'optional_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $optional_files,
])

@include('admin.components.select2-ajax', [
    'id' => 'parent_id',
    'url' => route('admin.util.select2.pr-parent'),
])

@push('scripts')
    <script>
        // Readonly Field
        $('#car_class').prop('disabled', true);
        $('#car_color').prop('disabled', true);
        $('#reason').prop('disabled', true);
        $status = '{{ isset($view) }}';
        if ($status) {
            $(".form-control").attr('disabled', true);
            $("input[name=is_need_driver]").attr('disabled', true);
            $("input[name=status_transfer]").attr('disabled', true);
            $("input[name=is_driver]").attr('disabled', true);
        }

        $status_check = '{{ $d->status }}';
        $branch_check = '{{ $d->branch_id }}';
        $branch_auth = '{{ Auth::user()->branch_id }}';

        if ($status_check == '{{ TransferCarEnum::WAITING_RECEIVE }}' || $status_check ==
            '{{ TransferCarEnum::CONFIRM_RECEIVE }}' || $status_check ==
            '{{ TransferCarEnum::IN_PROCESS }}') {

            $('#car_id').prop('disabled', true);
            $('#optional_files').prop('disabled', true);
            $('#transfer_branch_id').prop('disabled', true);
            $('#remark').prop('disabled', true);
        }

        if ($status_check == '{{ TransferCarEnum::IN_PROCESS }}') {

            $("input[name=is_driver]").attr('disabled', true);
            $('#contact').prop('disabled', true);
            $('#tel').prop('disabled', true);
            $('#place').prop('disabled', true);
            $('#delivery_date').prop('disabled', true);
            $('#driver_worksheet').prop('disabled', true);
            $('#car_transfer_sheet').prop('disabled', true);

        }

        if ($status_check == '{{ TransferCarEnum::CONFIRM_RECEIVE }}') {
            $("input[name=status_transfer]").attr('disabled', true);
            $("#reason").attr('disabled', true);
        }

        if ($status_check == '{{ TransferCarEnum::REJECT_RECEIVE }}') {

            $('#car_id').prop('disabled', true);
            $('#optional_files').prop('disabled', true);
            // $('#transfer_branch_id').prop('disabled', true);
            $('#remark').prop('disabled', true);

        }

        if ($branch_check == $branch_auth) {
            $('input[name=status]').prop('disabled', true);
        }

        $(".btn-show-cancel-modal").on("click", function() {
            document.getElementById("cancel_status").value = $(this).attr('data-status');
            document.getElementById("cancel_id").value = document.getElementById("id").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
            $('#modal-cancel').modal('show');
        });



        $(".btn-save-draft").on("click", function() {
            let storeUri = "{{ route('admin.purchase-requisitions.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }

                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }
            formData.append('status_draft', true);

            saveForm(storeUri, formData);
        });

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
            $('#confirm_date').hide();
            $('#confirm_user').hide();
            var status_default = '{{ $d->status }}';
            if (status_default == '{{ TransferCarEnum::REJECT_RECEIVE }}') {
                $('#remark_reason').show();
            } else {
                $('#remark_reason').hide();
            }
            if (status_default == '{{ TransferCarEnum::CONFIRM_RECEIVE }}') {
                $('#confirm_date').show();
                $('#confirm_user').show();
            }
            $('input[name="status_transfer"]').change(function() {
                var status = $('input[name="status_transfer"]:checked').val();
                // console.log(status);
                if (status != '{{ TransferCarEnum::CONFIRM_RECEIVE }}') {
                    $('#remark_reason').show();
                } else {
                    $('#remark_reason').hide();
                }
            });

            $('#place_label').hide();
            $('#delivery_date_label').hide();
            $('#driver_worksheet_label').hide();
            $('#car_transfer_sheet_label').hide();
            var status_driver = '{{ $d->is_driver }}';
            if (status_driver == '{{ TransferCarEnum::CONFIRM_RECEIVE }}') {
                $('#place_label').show();
                $('#delivery_date_label').show();
                $('#driver_worksheet_label').show();
                $('#car_transfer_sheet_label').show();
                $('#qa_sheet_pickup_label').show();
                $('#qa_sheet_return_label').show();
            } else {
                $('#place_label').hide();
                $('#delivery_date_label').hide();
                $('#driver_worksheet_label').hide();
                $('#car_transfer_sheet_label').hide();
                $('#qa_sheet_pickup_label').hide();
                $('#qa_sheet_return_label').hide();
            }

            $('input[name="is_driver"]').change(function() {
                var status = $('input[name="is_driver"]:checked').val();
                console.log(status);
                if (status == '{{ TransferCarEnum::CONFIRM_RECEIVE }}') {
                    $('#place_label').show();
                    $('#delivery_date_label').show();
                    $('#driver_worksheet_label').show();
                    $('#car_transfer_sheet_label').show();
                    $('#qa_sheet_pickup_label').show();
                    $('#qa_sheet_return_label').show();
                } else {
                    $('#place_label').hide();
                    $('#delivery_date_label').hide();
                    $('#driver_worksheet_label').hide();
                    $('#car_transfer_sheet_label').hide();
                    $('#qa_sheet_pickup_label').hide();
                    $('#qa_sheet_return_label').hide();
                }
            });
        });
    </script>
@endpush
