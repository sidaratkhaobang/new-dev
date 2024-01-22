<div class="modal fade" id="template-import-car-modal" data-target="face-sheet-select-car"
    aria-labelledby="face-sheet-select-car-modal" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="min-width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="template-import-modal-select-car-label"> <i
                        class="icon-printer"></i>ข้อมูลอัปโหลด
                </h5>
                <a href="{{ URL::current() }}" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div id="template-import" v-cloak data-detail-uri="" data-title="">
                    @include('admin.registers.section-template-imports.car-list')
                </div>
            </div>
            <div class="modal-footer">

                <a href="{{ URL::current() }}"
                    class="btn btn-outline-secondary btn-custom-size">{{ __('lang.back') }}</a>
                <button type="button" class="btn btn-primary btn-save-form-draft-modal"><i class="icon-save"></i>
                    {{ __('registers.save_draft') }}</button>
                <button type="button" class="btn btn-primary btn-save-form-import-modal"
                    data-status-import="{{ RegisterStatusEnum::REGISTERED }}"><i class="icon-check"></i>
                    {{ __('registers.save_registered') }}</button>
            </div>
        </div>
    </div>
</div>
@include('admin.registers.scripts.template-import-script')
@push('scripts')
    <script>
        $(".btn-save-form-import-modal").on("click", function() {
            var import_list = addTemplateImportVue.face_sheet_list;
            var import_list_arr = {};
            var status = $(this).attr('data-status-import');

            import_list.forEach((
                import_data, index) => {
                if (!import_list_arr[import_data.id]) {
                    import_list_arr[import_data.id] = {
                        id: [],
                        car_characteristic_transport: [],
                        color_registered: [],
                        registered_date: [],
                        receive_information_date: [],
                        license_plate: [],
                        car_tax_exp_date: [],
                        receipt_date: [],
                        receipt_no: [],
                        tax: [],
                        service_fee: [],
                        link: [],
                        is_registration_book: [],
                        is_license_plate: [],
                        is_tax_sign: [],
                        status: [],
                    };
                }

                import_list_arr[import_data.id].id = import_data.id;
                import_list_arr[import_data.id].car_characteristic_transport = import_data
                    .car_characteristic_transport;
                import_list_arr[import_data.id].color_registered = import_data.color_registered;
                import_list_arr[import_data.id].registered_date = import_data.registered_date;
                import_list_arr[import_data.id].receive_information_date = import_data.receive_information_date;
                import_list_arr[import_data.id].license_plate = import_data.license_plate;
                import_list_arr[import_data.id].car_tax_exp_date = import_data.car_tax_exp_date;
                import_list_arr[import_data.id].receipt_date = import_data.receipt_date;
                import_list_arr[import_data.id].receipt_no = import_data.receipt_no;
                import_list_arr[import_data.id].tax = import_data.tax;
                import_list_arr[import_data.id].service_fee = import_data.service_fee;
                import_list_arr[import_data.id].link = import_data.link;
                import_list_arr[import_data.id].is_registration_book = import_data.is_registration_book;
                import_list_arr[import_data.id].is_license_plate = import_data.is_license_plate;
                import_list_arr[import_data.id].is_tax_sign = import_data.is_tax_sign;
                import_list_arr[import_data.id].status = status;
            });

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: "{{ route('admin.registers.store-import-excel') }}",

                data: {
                    import_list_arr: import_list_arr,
                },
                success: function(response) {
                    if (response.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    }

                },
            });


        });

        $(".btn-save-form-draft-modal").on("click", function() {
            var import_list = addTemplateImportVue.face_sheet_list;
            var import_list_arr = {};
            // var status = $(this).attr('data-status-import');

            import_list.forEach((
                import_data, index) => {
                if (!import_list_arr[import_data.id]) {
                    import_list_arr[import_data.id] = {
                        id: [],
                        car_characteristic_transport: [],
                        color_registered: [],
                        registered_date: [],
                        receive_information_date: [],
                        license_plate: [],
                        car_tax_exp_date: [],
                        receipt_date: [],
                        receipt_no: [],
                        tax: [],
                        service_fee: [],
                        link: [],
                        is_registration_book: [],
                        is_license_plate: [],
                        is_tax_sign: [],
                        // status: [],
                    };
                }

                import_list_arr[import_data.id].id = import_data.id;
                import_list_arr[import_data.id].car_characteristic_transport = import_data
                    .car_characteristic_transport;
                import_list_arr[import_data.id].color_registered = import_data.color_registered;
                import_list_arr[import_data.id].registered_date = import_data.registered_date;
                import_list_arr[import_data.id].receive_information_date = import_data.receive_information_date;
                import_list_arr[import_data.id].license_plate = import_data.license_plate;
                import_list_arr[import_data.id].car_tax_exp_date = import_data.car_tax_exp_date;
                import_list_arr[import_data.id].receipt_date = import_data.receipt_date;
                import_list_arr[import_data.id].receipt_no = import_data.receipt_no;
                import_list_arr[import_data.id].tax = import_data.tax;
                import_list_arr[import_data.id].service_fee = import_data.service_fee;
                import_list_arr[import_data.id].link = import_data.link;
                import_list_arr[import_data.id].is_registration_book = import_data.is_registration_book;
                import_list_arr[import_data.id].is_license_plate = import_data.is_license_plate;
                import_list_arr[import_data.id].is_tax_sign = import_data.is_tax_sign;
                // import_list_arr[import_data.id].status = status;
            });

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: "{{ route('admin.registers.store-import-excel') }}",

                data: {
                    import_list_arr: import_list_arr,
                },
                success: function(response) {
                    if (response.success) {
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.reload();
                            }
                        });
                    }

                },
            });


        });
    </script>
@endpush
