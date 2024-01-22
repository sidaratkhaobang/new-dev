@push('scripts')
    <script>
        function openCustomerModal() {
            $("#modal-customer").modal("show");
        }

        $(".btn-save-customer").on("click", function() {
            let storeUri = "{{ route('admin.customers.store') }}";
            var formData = new FormData();
            customer_type = $('#customer_type_temp').val();
            customer_code = $('#customer_code_temp').val();
            customer_name = $('#customer_name_temp').val();
            customer_email = $('#customer_email_temp').val();
            customer_tel = $('#customer_tel_temp').val();
            customer_province_id = $('#customer_province_id_temp').val();
            customer_zipcode = $('#customer_zipcode_temp').val();
            customer_address = $('#customer_address_temp').val();

            formData.append('customer_type', customer_type);
            formData.append('customer_code', customer_code);
            formData.append('name', customer_name);
            formData.append('email', customer_email);
            formData.append('tel', customer_tel);
            formData.append('province_id', customer_province_id);
            formData.append('zipcode', customer_zipcode);
            formData.append('address', customer_address);
            formData.append('no_redirect', true);
            if (customer_type && customer_code && customer_name) {
                axios.post(storeUri, formData).then(response => {
                    if (response.data.success) {
                        $("#modal-customer").modal("hide");
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
                });
            } else {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: "{{ __('lang.required_field_inform') }}",
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            }

        });
    </script>
@endpush
