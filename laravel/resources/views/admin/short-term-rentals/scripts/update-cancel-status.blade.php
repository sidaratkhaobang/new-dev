@push('scripts')
    <script>
        $(".btn-cancel-status").on("click", function() {
            var route_update = '{{ route('admin.short-term-rentals.update-status') }}';
            var data = {
                status: '{{ \App\Enums\RentalStatusEnum::CANCEL }}',
                rental_id: document.getElementById("rental_id").value,
                redirect_route: '{{ route('admin.short-term-rentals.index') }}',
            };
            mySwal.fire({
                title: "{{ __('lang.cancel_confirm') }}",
                // text: "{{ __('lang.delete_message_confirm') }}",
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
                    axios.post(route_update, data).then(response => {
                        if (response.data.success) {
                            mySwal.fire({
                                title: "{{ __('lang.cancel_success') }}",
                                // text: "{{ __('lang.deleted_message') }}",
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
                                title: "{{ __('lang.cancel_fail') }}",
                                text: response.data.message,
                                icon: 'error',
                                confirmButtonText: "{{ __('lang.ok') }}",
                            }).then(value => {
                                if (value) {
                                    //
                                }
                            });
                        }
                    });
                }
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.cancel_fail') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        });
    </script>
@endpush
