<div class="modal fade" id="avance-select-car-modal" data-target="avance-select-car"
    aria-labelledby="avance-select-car-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avance-modal-select-car-label"> <i class="icon-printer"></i>เลือกรถที่ต้องการเบิกเงิน Advance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.registers.section-avances.select-car')
                <div id="avance" v-cloak data-detail-uri="" data-title="">
                    @include('admin.registers.section-avances.car-list')
                </div>

            </div>
            <div class="modal-footer">
                <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                <button type="button" class="btn btn-primary" onclick="openModalAvance()"><i
                        class="icon-menu-money"></i>
                    {{ __('registers.avance_withdraw') }}</button>
            </div>
        </div>
    </div>
</div>
@include('admin.registers.scripts.avance-script')
@include('admin.registers.scripts.avance-selected-script')
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

        function checkCar() {
            var status = document.getElementById("status_avance").value;
            var lot_no = document.getElementById("lot_no_avance").value;
            var car_id = document.getElementById("car_id_avance").value;
            var car_class = document.getElementById("car_class_avance").value;
            var leasing = document.getElementById("leasing_avance").value;
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
                        addAvanceVue.addCar(response.data.register);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

        function exportExcel() {
            // var excel_type_id = document.getElementById("excel_type_id").value;
            // var from_install_date = document.getElementById("from_install_date").value;
            // var to_install_date = document.getElementById("to_install_date").value;
            // var from_revoke_date = document.getElementById("from_revoke_date").value;
            // var to_revoke_date = document.getElementById("to_revoke_date").value;
            // if (!excel_type_id) {
            //     return warningAlert("{{ __('lang.required_field_inform') }}");
            // }
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
                    // from_install_date: from_install_date,
                    // to_install_date: to_install_date,
                    // from_revoke_date: from_revoke_date,
                    // to_revoke_date: to_revoke_date,
                },
                success: function(result, status, xhr) {
                    var fileName = 'เบิกเงิน Avance.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;



                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#face-sheet-modal').modal('hide');
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

        $(".btn-save-form-avance-modal").on("click", function() {
            // let storeUri = "{{ route('admin.registers.store') }}";
            var avance_list = addAvanceSelectedVue.face_sheet_list;
            var avance_list_arr = {};

            avance_list.forEach((avance, index) => {
                if (!avance_list_arr[avance.id]) {
                    avance_list_arr[avance.id] = {
                        memo_no: [],
                        operation_fee_avance: [],
                        receipt_avance: [],
                        total: [],
                    };
                }

                avance_list_arr[avance.id].memo_no = avance.memo_no;
                avance_list_arr[avance.id].operation_fee_avance = avance.operation_fee_avance;
                avance_list_arr[avance.id].receipt_avance = avance.receipt_avance;
                avance_list_arr[avance.id].total = avance.total;
            });
            // var formData = new FormData(document.querySelector('#save-form-modal'));
            // console.log(avance_list_arr)
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.registers.export-excel-avance') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    avance_list_arr: avance_list_arr,
                    // from_install_date: from_install_date,
                    // to_install_date: to_install_date,
                    // from_revoke_date: from_revoke_date,
                    // to_revoke_date: to_revoke_date,
                },
                success: function(result, status, xhr) {
                    var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
                    var customFileName = 'custom_filename.xlsx';

                    if (contentDispositionHeader) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = contentDispositionHeader.match(filenameRegex);
                        
                        if (matches && matches.length > 1) {
                            customFileName = matches[1].replace(/['"]/g, '');
                        }
                    }

                    var fileName = 'เบิกเงิน Advance.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = customFileName;



                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#avance-modal').modal('hide');
                    // window.location.href = "{{ route('admin.registers.index') }}";

                },
                error: function(result, status, xhr) {
                    console.log(result, status, xhr)
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
        });
    </script>
@endpush
