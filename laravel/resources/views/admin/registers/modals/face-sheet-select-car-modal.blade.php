<x-modal :id="'face-sheet-select-car'" :title="'เลือกรถที่ต้องการพิมพ์ใบปะหน้า'">
    @include('admin.registers.section-face-sheets.select-car')
    <div id="face-sheet" v-cloak data-detail-uri="" data-title="">
        @include('admin.registers.section-face-sheets.car-list')
    </div>
    <x-forms.hidden id="id" :value="$d->id" />
    <x-forms.hidden id="status" :value="$d->status" />
    @if (isset($status_confirm))
    <x-forms.hidden id="status_confirm" :value="$status_confirm" />
    @endif
    <x-slot name="footer">
        <a href="{{ URL::current() }}" class="btn">{{ __('lang.back') }}</a>
        <button type="button" class="btn btn-primary" onclick="openModalFaceSheet()"><i class="icon-printer"></i>
            {{ __('registers.select_car_face_sheet') }}</button>
    </x-slot>
</x-modal>


@include('admin.registers.scripts.face-sheet-script')
@push('scripts')
<script>
    $status = '{{ isset($view) }}';

    if ($status) {
        $('.form-control').prop('disabled', true);
        $("input[type=radio]").attr('disabled', true);
    }


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
                // delete data
                let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                if (pending_delete_ids.length > 0) {
                    pending_delete_ids.forEach((id) => {
                        formData.append(dropzone_id + '__pending_delete_ids[]', id);
                    });
                }
            });
        }
        return formData;
    }

    $("#facesheet_status").change(function() {
        var facesheet_status = $(this).val();
        if (facesheet_status == "{{ FaceSheetTypeEnum::REGISTER_NEW_CAR }}") {
            $("#topic_face_sheet").val('ใบนำส่งเอกสารจดทะเบียนรถใหม่');
        } else if (facesheet_status == "{{ FaceSheetTypeEnum::RETURN_LEASING }}") {
            $("#topic_face_sheet").val('ใบนำส่งเอกสารคืนเล่มทะเบียน');
        } else {
            $("#topic_face_sheet").val('');
        }

    });

    $(".btn-save-form-register").on("click", function() {
        let storeUri = "{{ route('admin.registers.store') }}";
        var formData = appendFormData();
        var status = $(this).attr('data-status');
        formData.append('status', status);
        saveForm(storeUri, formData);
    });

    function checkCarFaceSheet() {
        var status = document.getElementById("status").value;
        var lot_no = document.getElementById("lot_no").value;
        var car_id = document.getElementById("car_id").value;
        var car_class = document.getElementById("car_class").value;
        var leasing = document.getElementById("leasing").value;
        if (!status) {
            return warningAlert("{{ __('registers.required_status') }}")
        }
        axios.get("{{ route('admin.registers.check-car') }}", {
            params: {
                status: status,
                lot_no: lot_no,
                car_id: car_id,
                car_class: car_class,
                leasing: leasing,
            }
        }).then(response => {
            if (response.data.success) {
                if (response.data.register.length > 0) {
                    addFaceSheetVue.addCar(response.data.register);
                } else {
                    warningAlert("{{ __('registers.no_data') }}");
                }
            }
        });
    }

    function exportExcelFaceSheet() {
        // var excel_type_id = document.getElementById("excel_type_id").value;
        // var from_install_date = document.getElementById("from_install_date").value;
        // var to_install_date = document.getElementById("to_install_date").value;
        // var from_revoke_date = document.getElementById("from_revoke_date").value;
        // var to_revoke_date = document.getElementById("to_revoke_date").value;

        var topic_face_sheet = document.getElementById("topic_face_sheet").value;
        var facesheet_status = document.getElementById("facesheet_status").value;
        if (!topic_face_sheet || !facesheet_status) {
            return warningAlert("{{ __('lang.required_field_inform') }}");
        }
        var register_lists = [];
        var face_sheet_list = addFaceSheetVue.face_sheet_list;

        face_sheet_list.forEach((item, index) => {
            // line.rental_line_extra.forEach((item, index) => {
            register_lists.push(item.id);
            // formData.append(`face_sheet_list[${index}][subtotal]`, item
            //     .subtotal);
            // formData.append(`face_sheet_list[${index}][amount]`, item
            //     .amount);
            // formData.append(`face_sheet_list[${index}][id]`, item.id ??
            //     null);
            // });
        });

        $.ajax({
            xhrFields: {
                responseType: 'blob'
            },
            type: 'GET',
            url: "{{ route('admin.registers.export-excel-face-sheet') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                register_lists: register_lists,
                topic_face_sheet: topic_face_sheet,
                // from_install_date: from_install_date,
                // to_install_date: to_install_date,
                // from_revoke_date: from_revoke_date,
                // to_revoke_date: to_revoke_date,
            },
            success: function(result, status, xhr) {
                var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
                var customFileName = 'file_template.xlsx';

                if (contentDispositionHeader) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = contentDispositionHeader.match(filenameRegex);

                    if (matches && matches.length > 1) {
                        customFileName = decodeURIComponent(matches[1].replace(/['"]/g, ''));
                    }
                }

                var fileName = $('#topic_face_sheet').val();
                fileName = fileName + '.xlsx';
                var blob = new Blob([result], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });

                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = fileName;

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                $('#face-sheet-modal').modal('hide');
            },
            error: function(result, status, xhr) {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: 'ไม่พบข้อมูล',
                    icon: 'warning',
                    confirmButtonText: "{{ __('lang.ok') }}",
                });
            }
        });

    }
</script>
@endpush