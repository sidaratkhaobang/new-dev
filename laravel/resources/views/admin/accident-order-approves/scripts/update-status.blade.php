@push('scripts')
    <script>
        function updateBorrowCarStatus(data) {
            var updateUri = "{{ route('admin.accident-order-approves.update-status') }}";
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

        $(".btn-update-pr-status").on("click", function() {
            var id = $(this).attr('id');
            var status = $(this).attr('data-status');
            var data = {
                pr_status: status,
                purchase_requisitions: [id],
            };
            mySwal.fire({
                title: "{{ __('lang.close_job') }}",
                text: "{{ __('lang.close_job_message_confirm') }}",
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                confirmButtonText: "{{ __('lang.ok') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                html: false,
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }
            }).then(result => {
                if (result.value) {
                    updateBorrowCarStatus(data);
                }
            });
        });



        $(".btn-not-approve-status").on("click", function() {
            var data = {
                rp_status: $(this).attr('data-status'),
                rp_id: $(this).attr('data-id'),
                redirect: "{{ route('admin.accident-order-approves.index') }}",
                approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันไม่อนุมัติรายการซ่อม',
                html: 'กรุณาให้เหตุผลการไม่อนุมัติรายการซ่อมในครั้งนี้ <span class="text-danger">*</span>',
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
                    updateBorrowCarStatus(data);
                }
            })
        });

        $(".btn-approve-status").on("click", function() {
            var data = {
                rp_status: $(this).attr('data-status'),
                rp_id: $(this).attr('data-id'),
                redirect: "{{ route('admin.accident-order-approves.index') }}",
                approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันอนุมัติรายการซ่อม',
                html: 'เมื่อยืนยันรายการซ่อม ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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
                    updateBorrowCarStatus(data);
                }
            })
        });
    </script>
@endpush
