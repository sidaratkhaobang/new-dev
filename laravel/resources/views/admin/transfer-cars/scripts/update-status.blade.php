@push('scripts')
    <script>
        function updateTransferCarStatus(data) {
            var updateUri = "{{ route('admin.transfer-cars.update-status') }}";
            axios.post(updateUri, data).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }

        $(".btn-not-approve-status").on("click", function() {
            var data = {
                tc_status: $(this).attr('data-status'),
                transfer_car: $(this).attr('data-id'),
                redirect: "{{ route('admin.transfer-car-receives.index') }}",
                // approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันไม่อนุมัติการรับโอนรถ',
                html: 'กรุณาให้เหตุผลการไม่อนุมัติการรับโอนรถในครั้งนี้ <span class="text-danger">*</span>',
                input: 'text',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                if (result.value) {
                    var reason = result.value;
                    data.reason = reason;
                    updateTransferCarStatus(data);
                }
            })
        });

        $(".btn-approve-status").on("click", function() {
            var data = {
                tc_status: $(this).attr('data-status'),
                transfer_car: $(this).attr('data-id'),
                redirect: "{{ route('admin.transfer-car-receives.index') }}",
                // approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันอนุมัติการรับโอนรถ',
                html: 'เมื่อยืนยันการรับโอนรถ ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then((result) => {
                if (result.value) {
                    updateTransferCarStatus(data);
                }
            })
        });
    </script>
@endpush
