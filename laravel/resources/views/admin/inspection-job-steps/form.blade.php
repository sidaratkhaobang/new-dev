@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
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
    </style>
@endpush

@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('inspection_cars.sheet_detail'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-steps.sections.worksheet-info')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('inspection_cars.car_table'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-steps.sections.car-info')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('inspection_cars.inspection'),
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            @include('admin.inspection-job-steps.sections.inspection')

            <br>
            @if (
                $job_check_condition->is_need_customer_sign_out == STATUS_ACTIVE &&
                    $job_check_condition->transfer_type == TransferTypeEnum::OUT)
                @include('admin.inspection-job-steps.sections.delivery')
            @endif
            @if (
                $job_check_condition->is_need_customer_sign_in == STATUS_ACTIVE &&
                    $job_check_condition->transfer_type == TransferTypeEnum::IN)
                @include('admin.inspection-job-steps.sections.delivery')
            @endif

            <x-forms.hidden id="id" :value="$d->id" />
            <x-forms.hidden id="job_id" :value="$inspection_job->id" />
            <x-forms.hidden id="worksheet_no" :value="$d->worksheet_no" />
            <x-forms.hidden id="is_need_customer_sign_in" :value="$job_check_condition->is_need_customer_sign_in" />
            <x-forms.hidden id="is_need_customer_sign_out" :value="$job_check_condition->is_need_customer_sign_out" />
            <x-forms.hidden id="transfer_type" :value="$job_check_condition->transfer_type" />

            <br>
            <x-forms.submit-group :optionals="['url' => 'admin.inspection-jobs.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::CarInspection]"/>
        </div>
    </div>

    @include('admin.inspection-job-steps.modals.signature')
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.inspection-job-steps.store'),
])



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        $('#opener_sheet').prop('disabled', true);
        $('#department').prop('disabled', true);
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
        $('#customer').prop('disabled', true);
        $('#creditor').prop('disabled', true);

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

        $("#inspection_type").change(function() {

            var id = document.getElementById("inspection_type").value;
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.inspection-job-steps.get-data-inspection-type') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id
                },
                success: function(data) {
                    $('#step_table').empty();
                    console.log(data.step_form);
                    $arr_acc = data.accessory;
                    if (data.step_form.length > 0 && data.step_form[0].inspection_step_name != null) {
                        data.step_form.forEach((element, index) => {
                            $('#step_table').append(`<tr><td>${index+1}</td>
                            <td>${element.inspection_step_name}</td>`)
                        });
                    } else if (data.step_form[0].inspection_step_name == null) {
                        $('#step_table').append(`<tr class="table-empty"><td class="text-center" colspan="5">
                            “{{ __('lang.no_list') }}“
                            </td></tr>`)
                    }
                }
            });


        });
        var condition_show_in = '{{ $job_check_condition->is_need_customer_sign_in }}';
        var condition_show_out = '{{ $job_check_condition->is_need_customer_sign_out }}';
        var transfer_type = '{{ $job_check_condition->transfer_type }}';
        var check = null;
        if (transfer_type == {{ TransferTypeEnum::IN }}) {
            if (condition_show_in == {{ STATUS_ACTIVE }}) {
                check = true;
            }
        } else {
            if (condition_show_out == {{ STATUS_ACTIVE }}) {
                check = true;
            }
        }
        if (check) {
            function signature() {
                $("#modal-signature").modal("show");
            }

            var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });
            var saveButton = document.getElementById('save');
            var cancelButton = document.getElementById('clear1');

            var signature_delete_id = '';
            var data_signature = '';
            saveButton.addEventListener('click', function(event) {
                event.preventDefault()
                $("#modal-signature").modal("hide");
                signature_delete = @json($signature_get_media);
                if (signature_delete.length != 0) {
                    signature_delete_id = signature_delete.media_id;
                }
                var data = signaturePad.toDataURL('image/png');
                data_signature = data;
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
                        hourCycle: 'h23',
                        ca: 'buddhist'
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

        $(".btn-save-form2").on("click", function() {
            if (check) {
                if (!signaturePad.isEmpty()) {
                    if ($("#modal-signature").hasClass("add_sig")) {
                        var data = signaturePad.toDataURL('image/png');
                        console.log(signaturePad.isEmpty());
                        var file = dataURLtoFile(data, "image.png");
                    }
                }
            }
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            let storeUri = "{{ route('admin.inspection-job-steps.store') }}";
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
            if (check) {
                if (!signaturePad.isEmpty()) {
                    if ($("#modal-signature").hasClass("add_sig")) {
                        formData.append('signature__pending_delete_ids', signature_delete_id);
                        formData.append('signature', file);
                    }
                }
            }
            saveForm(storeUri, formData);
        });



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
    </script>
@endpush
