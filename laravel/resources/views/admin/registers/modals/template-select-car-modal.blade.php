<div class="modal fade" id="template-select-car-modal" data-target="template-select-car"
    aria-labelledby="template-select-car-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avance-modal-select-car-label"> <i
                        class="icon-printer"></i>เลือกรถที่ต้องการดาวน์โหลดไฟล์
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.registers.section-templates.select-car')
                <div id="template" v-cloak data-detail-uri="" data-title="">
                    @include('admin.registers.section-templates.car-list')
                    {{-- @include('admin.registers.modals.avance-modal') --}}
                </div>
                {{-- @include('admin.registers.modals.face-sheet-modal') --}}
                {{-- <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                @if (isset($status_confirm))
                    <x-forms.hidden id="status_confirm" :value="$status_confirm" />
                @endif --}}

            </div>
            <div class="modal-footer">
                {{-- <div class="row push me-1">
                    <div class="col-sm-12 text-end"> --}}
                <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                {{-- @if (!isset($view)) --}}
                {{-- <button type="submit" class="btn btn-primary btn-custom-size btn-save-form"><i class="icon-printer"></i> {{ __('registers.select_car_face_sheet') }}</button> --}}
                {{-- <button type="button" class="btn btn-primary" onclick="openModalAvance()"><i
                    class="icon-menu-money"></i>
                {{ __('registers.avance_withdraw') }}</button> --}}
                <button type="button" class="btn btn-primary" onclick="exportExcelTemplate()"><i
                        class="icon-printer"></i>
                    {{ __('registers.download_file') }}</button>
                {{-- @endif --}}
                {{-- </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@include('admin.registers.scripts.template-script')
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

        function checkCarTemplate() {
            var status = document.getElementById("status_template").value;
            var lot_no = document.getElementById("lot_no_template").value;
            var car_id = document.getElementById("car_id_template").value;
            var car_class = document.getElementById("car_class_template").value;
            var leasing = document.getElementById("leasing_template").value;
            if(!status){
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
                    // $("#zip_code").val(response.data.data.zip_code);
                    // console.log(response.data.register)
                    // console.log(response.data.register.length)
                    if (response.data.register.length > 0) {
                        addTemplateVue.addCar(response.data.register);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

        function exportExcelTemplate() {

            var register_lists = [];
            var face_sheet_list = addTemplateVue.face_sheet_list;
            face_sheet_list.forEach((item, index) => {
                register_lists.push(item.id);
            });

            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.registers.export-excel-template') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    register_lists: register_lists,

                },
                success: function(result, status, xhr) {
                    var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
                    var customFileName = 'file_template.xlsx';

                    if (contentDispositionHeader) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = contentDispositionHeader.match(filenameRegex);

                        if (matches && matches.length > 1) {
                            customFileName = decodeURIComponent(escape(matches[1].replace(/['"]/g, '')));
                        }
                    }
                    console.log(customFileName)
                    console.log('11111111')
                    var fileName = 'file_template.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = customFileName;


                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#template-select-car-modal').modal('hide');
                    // window.location.href = "{{ route('admin.registers.index') }}";

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

        // function openModalAvance() {

        //     $('#avance-modal').modal('show');
        // }

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form-modal'));
            // if (window.myDropzone) {
            //     var dropzones = window.myDropzone;
            //     dropzones.forEach((dropzone) => {
            //         let dropzone_id = dropzone.options.params.elm_id;
            //         let files = dropzone.getQueuedFiles();
            //         files.forEach((file) => {
            //             formData.append(dropzone_id + '[]', file);
            //         });
            //         // delete data
            //         let pending_delete_ids = dropzone.options.params.pending_delete_ids;
            //         if (pending_delete_ids.length > 0) {
            //             pending_delete_ids.forEach((id) => {
            //                 formData.append(dropzone_id + '__pending_delete_ids[]', id);
            //             });
            //         }
            //     });
            // }
            return formData;
        }
    </script>
@endpush
