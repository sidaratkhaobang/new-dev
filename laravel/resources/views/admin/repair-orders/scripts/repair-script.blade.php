@push('scripts')
    <script>
        $("#repair_type").prop('disabled', true);
        $("#alert_date").prop('disabled', true);
        $("#repair_create_by").prop('disabled', true);
        $("#mileage").prop('disabled', true);
        $("#place").prop('disabled', true);
        $("#remark").prop('disabled', true);
        $("#car_license").prop('disabled', true);
        $("#address").prop('disabled', true);
        $("#amount_day").prop('disabled', true);
        $("#mileage_order").prop('disabled', true);

        $("#repair_no").on('select2:select', function(e) {
            $("#center").val('').change();
            $("#address").val('');
            $("#expected_date").val('');
            $("#completed_date").val('');
            $("#remark_center").val('');
            $("#check_distance").val('').change();
            $("#repair_documents").hide();
            $('.dz-remove').hide();
            removeData();
            var data = e.params.data;
            axios.get("{{ route('admin.repair-orders.data-repair') }}", {
                params: {
                    repair_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    $("#repair_type").val(response.data.data.repair_type);
                    $("#alert_date").val(response.data.data.repair_date);
                    $("#repair_create_by").val(response.data.data.repair_create_by);
                    $("#mileage").val(response.data.data.mileage);
                    $("#mileage_order").val(response.data.data.mileage);
                    $("#place").val(response.data.data.place);
                    $("#remark").val(response.data.data.remark);
                    $('input[name="in_center"][value="' + response.data.data.in_center + '"]').prop(
                        'checked', true);
                    $('input[name="out_center"][value="' + response.data.data.out_center + '"]').prop(
                        'checked', true);
                    if (response.data.data.in_center === 0) {
                        document.getElementById("driver_in_center").style.display = "block";
                        $('input[name="is_driver_in_center"][value="' + response.data.data
                            .is_driver_in_center + '"]').prop(
                            'checked', true);
                    }

                    if (response.data.data.out_center === 0) {
                        document.getElementById("driver_out_center").style.display = "block";
                        $('input[name="is_driver_out_center"][value="' + response.data.data
                            .is_driver_out_center + '"]').prop(
                            'checked', true);
                    }
                    $("#in_center_date").val(response.data.data.in_center_date);
                    flatpickr("#in_center_date", {
                        defaultDate: response.data.data.in_center_date,
                    });
                    $("#center_date").val(response.data.data.in_center_date);
                    flatpickr("#center_date", {
                        defaultDate: response.data.data.in_center_date,
                    });
                    $("#out_center_date").val(response.data.data.out_center_date);
                    flatpickr("#out_center_date", {
                        defaultDate: response.data.data.out_center_date,
                    });

                    $('input[name="is_replacement"][value="' + response.data.data.is_replacement + '"]')
                        .prop(
                            'checked', true);
                    if (response.data.data.is_replacement === 1) {
                        document.getElementById("re_date").style.display = "block"
                        document.getElementById("re_type").style.display = "block"
                        document.getElementById("re_place").style.display = "block"
                        $("#replacement_date").text(response.data.data.replacement_date);
                        flatpickr("#replacement_date", {
                            defaultDate: response.data.data.replacement_date,
                        });
                        $("#replacement_type").val(response.data.data.replacement_type).change();
                        $("#replacement_place").val(response.data.data.replacement_place);
                    }
                    removeAll();
                    if (response.data.repair_line.length > 0) {
                        response.data.repair_line.forEach((e, index) => {
                            showRepairLineDefault(e);
                        });
                    }

                    document.getElementById("car-detail-section").style.display = "block"
                    console.log(response.data.car_data);
                    if (response.data.car_data) {
                        $("#car_id").val(response.data.car_data.id);
                        $("#main_car_id").val(response.data.car_data.id);
                        $("#car_license").val(response.data.car_data.car_license);
                        $("#license_plate").text(response.data.car_data.license_plate);
                        $("#chassis_no").text(response.data.car_data.chassis_no);
                        $("#car_class").text(response.data.car_data.car_class_name);
                        $("#current_mileage").text(response.data.car_data.current_mileage);
                        document.getElementById("car_status").style.display = "block";
                        var car_status = document.getElementById("car_status");
                        car_status.innerHTML =
                            `<span class="badge badge-custom badge-bg-${response.data.status_class}" >${ response.data.status_text }</span>`;
                        if (response.data.car_data.rental > 0) {
                            document.getElementById("rental_show").style.display = "block"
                            $("#rental_id").val(response.data.car_data.rental_id);
                            $("#rental_type").val(response.data.car_data.rental_type);
                            $("#rental_no").text(response.data.car_data.rental_worksheet_no);
                            $("#rental_name").text(response.data.car_data.rental_customer_name);
                            $("#contract_no").text(response.data.car_data.contract_worksheet_no);
                            $("#contract_start_date").text(response.data.car_data.contract_pick_up_date);
                            $("#contract_end_date").text(response.data.car_data.contract_return_date);
                        } else {
                            document.getElementById("rental_show").style.display = "none"
                        }
                    }

                    if (response.data.data.replacement_list.length > 0) {
                        addReplacementVue.setData(response.data.data.replacement_list); 
                        $("#replacment_section").show();
                    } else {
                        addReplacementVue.setData([]); 
                        $("#replacment_section").show();
                    }

                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];
                    var repair_documents_files = response.data.data.repair_documents_files;
                    if (repair_documents_files.length > 0) {
                        $("#repair_documents").show();
                        $('.dz-remove').show();
                        repair_documents_files.forEach((item, index) => {
                            let mockFile = {
                                ...item
                            };
                            window.myDropzone[0].emit("addedfile", mockFile);
                            window.myDropzone[0].emit("thumbnail", mockFile, item.url_thumb);
                            window.myDropzone[0].files.push(mockFile);
                            var preview_link = $(
                                '.dz-repair_documents-preview-content > .dz-content > [data-dz-name]'
                            ).eq(index);
                            preview_link.attr('href', item.url);
                            preview_link.attr('target', '_blank');
                        });
                    }
                }
            });
        });


        $("#center").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.repair-orders.data-center') }}", {
                params: {
                    center_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    $("#address").val(response.data.data.address);
                }
            });
        });

        $("#check_distance").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.repair-orders.select-distance') }}";
                },
                type: 'GET',
                data: function(params) {
                    parent_id = $("#mileage_order").val();
                    parent_id_2 = $("#car_id").val();
                    return {
                        parent_id: parent_id,
                        parent_id_2: parent_id_2,
                        s: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            }
        });

        $("#check_distance").on('select2:select', function(e) {
            var data = e.params.data;
            var repair_type = $("#repair_type").val();
            axios.get("{{ route('admin.repair-orders.get-default-distance-line') }}", {
                params: {
                    distance_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    removeData();
                    if (response.data.distance_line.length > 0) {
                        if (repair_type === 'ซ่อมทั่วไป') {
                            removeData();
                        } else {
                            response.data.distance_line.forEach((e, index) => {
                                addDefault(e);
                            });
                        }

                    }
                }
            });
        });
    </script>
@endpush
