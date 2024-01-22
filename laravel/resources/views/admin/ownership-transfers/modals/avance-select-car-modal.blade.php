<div class="modal fade" id="avance-select-car-modal" data-target="avance-select-car"
    aria-labelledby="avance-select-car-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="avance-modal-select-car-label"> <i
                        class="icon-printer"></i>เลือกรถที่ต้องการเบิกเงิน Advance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.ownership-transfers.section-avances.select-car')
                <div id="avance" v-cloak data-detail-uri="" data-title="">
                    @include('admin.ownership-transfers.section-avances.car-list')
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
@include('admin.ownership-transfers.scripts.avance-script')
@include('admin.ownership-transfers.scripts.avance-selected-script')
@push('scripts')
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script> --}}
    <script>
        // $status = '{{ isset($view) }}';

        // if ($status) {
        //     $('.form-control').prop('disabled', true);
        //     $("input[type=radio]").attr('disabled', true);
        // }


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



        // $(".btn-save-form-register").on("click", function() {
        //     let storeUri = "{{ route('admin.registers.store') }}";
        //     var formData = appendFormData();
        //     var status = $(this).attr('data-status');
        //     formData.append('status', status);
        //     saveForm(storeUri, formData);
        // });

        function checkCar() {
            var status = document.getElementById("status_avance").value;
            var month_last_payment_avance = document.getElementById("month_last_payment_avance").value;
            var car_id = document.getElementById("car_id_avance").value;
            var car_class = document.getElementById("car_class_avance").value;
            var leasing = document.getElementById("leasing_avance").value;
            if (!status) {
                return warningAlert("{{ __('registers.required_status') }}")
            }
            axios.get("{{ route('admin.ownership-transfers.check-car') }}", {
                params: {
                    status: status,
                    month_last_payment: month_last_payment_avance,
                    car_id: car_id,
                    car_class: car_class,
                    leasing: leasing,
                }
            }).then(response => {
                if (response.data.success) {
                    // $("#zip_code").val(response.data.data.zip_code);
                    // console.log(response.data.register)
                    // console.log(response.data.register.length)
                    if (response.data.ownership_transfer.length > 0) {
                        addAvanceVue.addCar(response.data.ownership_transfer);
                    } else {
                        warningAlert("{{ __('registers.no_data') }}");
                    }
                }
            });
        }


        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form-modal'));
            return formData;
        }

        $(".btn-save-form-avance-modal").on("click", function() {
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
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.ownership-transfers.export-excel-avance') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    avance_list_arr: avance_list_arr,
                },
                success: function(result, status, xhr) {
                    var contentDispositionHeader = xhr.getResponseHeader('Content-Disposition');
                    var customFileName = 'custom_filename.xlsx';
                    if (contentDispositionHeader) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = contentDispositionHeader.match(filenameRegex);

                        if (matches && matches.length > 1) {
                            customFileName = matches[1].replace(/['"]/g, '');
                            customFileName = "รายการรถเบิกเงิน" + customFileName;
                        }
                    }

                    var fileName = 'รายการรถเบิกเงินAdvance.xlsx';
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

        jQuery(function() {
            let monthExpireInput = document.querySelector("#month_last_payment_avance");
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
