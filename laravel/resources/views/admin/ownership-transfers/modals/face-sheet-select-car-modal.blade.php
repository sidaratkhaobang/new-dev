<div class="modal fade" id="face-sheet-select-car-modal" data-target="face-sheet-select-car"
    aria-labelledby="face-sheet-select-car-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="face-sheet-modal-select-car-label"> <i
                        class="icon-printer"></i>เลือกรถที่ต้องการพิมพ์ใบปะหน้า
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.ownership-transfers.section-face-sheets.select-car')
                <div id="face-sheet" v-cloak data-detail-uri="" data-title="">
                    @include('admin.ownership-transfers.section-face-sheets.car-list')
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                <button type="button" class="btn btn-primary" onclick="openModalFaceSheet()"><i
                        class="icon-printer"></i>
                    {{ __('registers.select_car_face_sheet') }}</button>
            </div>
        </div>
    </div>
</div>
@include('admin.ownership-transfers.scripts.face-sheet-script')
@push('scripts')
    <script>
        // function appendFormData() {
        //     var formData = new FormData(document.querySelector('#save-form'));
        //     if (window.myDropzone) {
        //         var dropzones = window.myDropzone;
        //         dropzones.forEach((dropzone) => {
        //             let dropzone_id = dropzone.options.params.elm_id;
        //             let files = dropzone.getQueuedFiles();
        //             files.forEach((file) => {
        //                 formData.append(dropzone_id + '[]', file);
        //             });
        //             // delete data
        //             let pending_delete_ids = dropzone.options.params.pending_delete_ids;
        //             if (pending_delete_ids.length > 0) {
        //                 pending_delete_ids.forEach((id) => {
        //                     formData.append(dropzone_id + '__pending_delete_ids[]', id);
        //                 });
        //             }
        //         });
        //     }
        //     return formData;
        // }

        $("#facesheet_status").change(function() {
            var facesheet_status = $(this).val();
            if (facesheet_status == "{{ OwnershipTransferFaceSheetTypeEnum::OWNERSHIP_TRANSFER }}") {
                $("#topic_face_sheet").val('ใบนำส่งเอกสารโอนกรรมสิทธิ์รถยนต์');
            } else if (facesheet_status == "{{ OwnershipTransferFaceSheetTypeEnum::RETURN_REGISTER_BOOK }}") {
                $("#topic_face_sheet").val('ใบนำส่งเอกสารคืนเล่มทะเบียน');
            } else {
                $("#topic_face_sheet").val('');
            }

        });

        function checkCarFaceSheet() {
            var status = document.getElementById("status").value;
            var month_last_payment = document.getElementById("month_last_payment").value;
            var car_id = document.getElementById("car_id").value;
            var car_class = document.getElementById("car_class").value;
            var leasing = document.getElementById("leasing").value;
            if(!status){
                return warningAlert("{{ __('registers.required_status') }}")
            }
            axios.get("{{ route('admin.ownership-transfers.check-car') }}", {
                params: {
                    status: status,
                    month_last_payment: month_last_payment,
                    car_id: car_id,
                    car_class: car_class,
                    leasing: leasing,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.ownership_transfer.length > 0) {
                        addFaceSheetVue.addCar(response.data.ownership_transfer);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }

        function exportExcelFaceSheet() {
            var topic_face_sheet = document.getElementById("topic_face_sheet").value;
            var facesheet_status = document.getElementById("facesheet_status").value;
            if (!topic_face_sheet || !facesheet_status) {
                return warningAlert("{{ __('lang.required_field_inform') }}");
            }
            var ownership_transfer_lists = [];
            var face_sheet_list = addFaceSheetVue.face_sheet_list;

            face_sheet_list.forEach((item, index) => {
                ownership_transfer_lists.push(item.id);
            });

            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.ownership-transfers.export-excel-face-sheet') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ownership_transfer_lists: ownership_transfer_lists,
                    topic_face_sheet: topic_face_sheet,
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
        
        jQuery(function() {
            let monthExpireInput = document.querySelector("#month_last_payment");
            let defaultDate = "{{ $month_last_payment ?? '' }}";

            let flatpickrInstance = flatpickr(monthExpireInput, {
                plugins: [
                    new monthSelectPlugin({
                        dateFormat: "m/Y",
                        shorthand: true,
                        theme: "light",
                    })
                ],
                onReady: function(selectedDates, dateStr, instance) {
                    instance.calendarContainer.classList.add('flatpickr-monthYear');
                },
                onChange: function(selectedDates, dateStr) {

                },
                defaultDate: defaultDate,
            });
            if (this.value) {
                flatpickrInstance.setDate(this.value);
            }
        });
    </script>
@endpush
