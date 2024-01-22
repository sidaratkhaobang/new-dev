@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .input-group-text {
            /* padding: 0.7rem .75rem; */
            background-color: transparent;
            /* border-radius: 0; */
            color: #6c757d;
        }

        .wrapper {
            position: relative;
            width: 400px;
            height: 200px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        img {
            position: absolute;
            left: 0;
            top: 0;
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width: 760px;
            height: 200px;
        }

        .hidden {
            display: none;
        }

        table>thead>tr>th>a {
            pointer-events: none;
        }

        .dz-remove {
            min-width: unset !important;
        }
    </style>
@endpush

@section('content')
@section('block_options_list')
    <div class="block-options">
        {{-- <div class="ms-auto"> --}}
            <a target="_blank"
                href="{{ route('admin.inspection-job-step-forms.pdf', ['inspection_job_step_form' => $d, 'type' => 'PDF']) }}"
                class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
                {{ __('inspection_cars.print') }}
            </a>
        {{-- </div> --}}
    </div>
@endsection

<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('inspection_cars.inspector_detail'),
            // 'is_toggle' => true,
            'block_option_id' => '_list',
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-step-forms.sections.inspector-detail')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('inspection_cars.basic_info'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-step-forms.sections.default-detail')
        </div>
    </div>

    @if ($step_form_check_condition->is_need_images == STATUS_ACTIVE)
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('inspection_cars.image_detail'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.inspection-job-step-forms.sections.car-image')
            </div>
        </div>
    @endif

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('inspection_cars.checklist'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-step-forms.sections.inspection-checklist')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('inspection_cars.result_inspection'),
            'is_toggle' => true,
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-step-forms.sections.result-inspection')
        </div>
    </div>

    @if ($step_form_check_condition->is_need_inspector_sign == STATUS_ACTIVE)
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('inspection_cars.delivery_car'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.inspection-job-step-forms.sections.delivery')
            </div>
        </div>
    @endif

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <x-forms.hidden id="is_need_inspector_sign" :value="$step_form_check_condition->is_need_inspector_sign" />
            <x-forms.hidden id="job_id" :value="$job_id" />
            <x-forms.hidden id="job_step_id" :value="$job_step_id" />
            <x-forms.hidden id="job_form_id" :value="$step_form_status->inspection_form_id" />

            @can(Actions::Manage . '_' . Resources::CarInspection)
            <x-forms.submit-group :optionals="['input_class_submit' => 'btn-save-form-inspection','fullurl' => route('admin.inspection-job-steps.edit', ['inspection_job_step' => $job_id]), 'view' => empty($view) ? null : $view]" />
            @endcan
        </div>
    </div>

    @include('admin.inspection-job-step-forms.modals.signature')
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.inspection-job-step-forms.store'),
])
@if ($step_form_check_condition->is_need_images == STATUS_ACTIVE)
@include('admin.components.upload-image-scripts')


@php
    if (isset($view)) {
        $upload = [
            'id' => 'front_car_images_out',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $front_image_files_out,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'front_car_images_out',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $front_image_files_out,
            'preview_files' => true,
        ];
    }
@endphp


@include('admin.components.upload-image', $upload)
@php
    
    if (isset($view)) {
        $upload = [
            'id' => 'front_car_images_in',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $front_image_files_in,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'front_car_images_in',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $front_image_files_in,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    
    if (isset($view)) {
        $upload = [
            'id' => 'back_car_images_out',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $back_image_files_out,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'back_car_images_out',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $back_image_files_out,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'back_car_images_in',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $back_image_files_in,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'back_car_images_in',
            'max_files' => 20,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $back_image_files_in,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'right_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $right_image_files_out,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'right_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $right_image_files_out,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'right_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $right_image_files_in,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'right_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $right_image_files_in,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'left_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $left_image_files_out,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'left_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $left_image_files_out,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'left_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $left_image_files_in,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'left_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $left_image_files_in,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'top_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $top_image_files_out,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'top_car_images_out',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $top_image_files_out,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)

@php
    if (isset($view)) {
        $upload = [
            'id' => 'top_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $top_image_files_in,
            'preview_files' => true,
            'view_only' => true,
        ];
    } else {
        $upload = [
            'id' => 'top_car_images_in',
            'max_files' => 10,
            'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
            'mock_files' => $top_image_files_in,
            'preview_files' => true,
        ];
    }
@endphp

@include('admin.components.upload-image', $upload)
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
    $('#opener_sheet').prop('disabled', true);
    $('#driver').prop('disabled', true);
    $('#car_category').prop('disabled', true);
    $('#car_class').prop('disabled', true);
    $('#car_color').prop('disabled', true);
    $('#user_car').prop('disabled', true);
    $('#engine_no').prop('disabled', true);
    $('#chassis_no').prop('disabled', true);
    $('#engine_size').prop('disabled', true);
    $('#gear').prop('disabled', true);
    $('#fuel_type').prop('disabled', true);
    $('#tire').prop('disabled', true);
    $('#car_status_id').prop('disabled', true);
    $('#car_id').prop('disabled', true);
    $('#work_type').prop('disabled', true);
    $('#worksheet_no').prop('disabled', true);
    $('#open_worksheet').prop('disabled', true);
    $('#inspection_type').prop('disabled', true);
    $('#dealer').prop('disabled', true);
    $('#inspector_department').prop('disabled', true);
    $('#inspection_location').prop('readonly', true);
    $('.remark_log').prop('disabled', true);
    $('.remark_reason_log').prop('disabled', true);
    $('.radio-log').prop('disabled', true);

    $view = '{{ isset($view) }}';
    if ($view) {
        $('#transfer_type').prop('disabled', true);
        $('#reason').prop('disabled', true);
        $('#est_transfer_date').prop('disabled', true);
        $('#start_date').prop('disabled', true);
        $('#end_date').prop('disabled', true);
        $('#car_status_id').prop('disabled', true);
        $('#car_id').prop('disabled', true);
        $('#car_zone_id').prop('disabled', true);
        $('#car_park_id').prop('disabled', true);
        $('#cancel_reason').prop('disabled', true);
        $('.form-control').prop('disabled', true);
        $("input[type='range']").attr('disabled', true);
        $("input[type='radio']").attr('disabled', true);
        $("input[type='checkbox']").attr('disabled', true);
    }
    $("#car_id").on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.inspection-jobs.default-car') }}", {
            params: {
                car_id: data.id
            }
        }).then(response => {
            if (response.data.success) {
                if (response.data.data.length > 0) {
                    response.data.data.forEach((e) => {
                        $("#engine_no").val(e.engine_no);
                        $("#chassis_no").val(e.chassis_no);
                        $("#car_class").val(e.car_class_name);
                        $("#car_color").val(e.car_colors_name);
                        $("#car_category").val(e.car_categories_name);
                        $("#engine_size").val(e.engine_size);
                        $("#gear").val(e.car_gear_name);
                        $("#tire").val(e.car_tire_name);
                        $("#fuel_type").val(e.oil_type);
                    });
                }
            }
        });
    });

    var condition_show = '{{ $step_form_check_condition->is_need_inspector_sign }}';
    if (condition_show == 1) {
        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)',
            fillText: 'dsaasdsad',
        });

        var saveButton = document.getElementById('save');
        var cancelButton = document.getElementById('clear1');
        var signature_delete_id = '';
        saveButton.addEventListener('click', function(event) {

            event.preventDefault()
            $("#modal-signature").modal("hide");
            signature_delete = @json($signature_get_media);
            if (signature_delete.length != 0) {
                signature_delete_id = signature_delete.media_id;
            }
            var data = signaturePad.toDataURL('image/png');

            if (!signaturePad.isEmpty()) {
                var a = document.createElement("a");

                a.download = "image.png";
                a.setAttribute("type", "file");
                a.href = data;
                a.setAttribute("name", "sig1");
                a.setAttribute("id", "sig1");
                a.setAttribute("class", "old-signature");
                a.setAttribute("target", "_blank");
                const date = new Date();
                var date_new = date.toLocaleString("th", {
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hourCycle: 'h23'
                });

                $("#signature").hide();
                $("#signature_date").hide();

                if ($("#sig1").hasClass("old-signature")) {
                    $(".old-signature").hide();
                    $(a).html("image.png ").prependTo($("#sig"));
                    $("#date_detail").last().html(date_new);

                } else {
                    $(a).html("image.png ").prependTo($("#sig"));
                    $("#date_detail").last().html(date_new);

                }
            } else {
                $("#sig1").remove();
                $("#date_detail").empty();
                signaturePad.clear();
            }
        });

        function dataURLtoFile(dataurl, filename) {

            var arr = dataurl.split(','),
                mime = arr[0].match(/:(.*?);/)[1],
                bstr = atob(arr[1]),
                n = bstr.length,
                u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], filename, {
                type: mime
            });
        }

        cancelButton.addEventListener('click', function(event) {
            event.preventDefault()
            if ($('#signature').length > 0) {
                if ($("#signature").val().length == 0) {
                    $("#signature").remove();
                    $("#signature_date").remove();
                }
            }

            if ($('#sig1').length > 0) {
                $("#sig1").remove();
                $("#date_detail").empty();
            }
            signaturePad.clear();
        });
    }

    $(".btn-save-form-inspection").on("click", function() {
        if (condition_show == 1) {
            if (!signaturePad.isEmpty()) {
                if ($("#modal-signature").hasClass("add_sig")) {
                    var data = signaturePad.toDataURL('image/png');
                    var file = dataURLtoFile(data, "image.png");
                }
            }
        }
        var formData = new FormData(document.querySelector('#save-form'));
        var status = $(this).attr('data-status');
        let storeUri = "{{ route('admin.inspection-job-step-forms.store') }}";
        if (window.myDropzone) {
            var dropzones = window.myDropzone;
            dropzones.forEach((dropzone) => {
                let dropzone_id = dropzone.options.params.elm_id;
                let files = dropzone.getQueuedFiles();
                files.forEach((file) => {
                    formData.append(dropzone_id + '[]', file);
                });
                let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                if (pending_delete_ids.length > 0) {
                    pending_delete_ids.forEach((id) => {
                        formData.append(dropzone_id + '__pending_delete_ids[]', id);
                    });
                }
            });
        }
        if (condition_show == 1) {
            if (!signaturePad.isEmpty()) {
                if ($("#modal-signature").hasClass("add_sig")) {
                    formData.append('signature__pending_delete_ids', signature_delete_id);
                    formData.append('signature', file);
                }
            }
        }
        saveForm(storeUri, formData);
    });

    function signature() {
        $("#modal-signature").modal("show");
    }

    $(".btn-close").click(function() {
        if (!$("#modal-signature").hasClass("add_sig")) {
            $("#sig1").remove();
            $("#date_detail").empty();
            signaturePad.clear();
        }

        if (signaturePad.isEmpty()) {
            $("#sig1").remove();
            $("#date_detail").empty();
            signaturePad.clear();
        }
    });

    $("#save").click(function() {
        if (!signaturePad.isEmpty()) {
            $("#modal-signature").addClass("add_sig");
        }
    });

    $("#reason").addClass('hidden');
    $("#remark").hide();


    $("#inspection_status_not_pass").click(function() {
        $("#reason").removeClass('hidden');
        $("#remark").show();
    });

    $("#inspection_status_pass").click(function() {
        $("#reason").addClass('hidden');
        $("#remark").hide();
    });
</script>
@endpush
