@push('scripts')
    <script>
        $("#short_worksheet_no").prop('disabled', true);
        $("#long_worksheet_no").prop('disabled', true);
        $("#import_worksheet_no").prop('disabled', true);
        $("#service_type").prop('disabled', true);
        $("#short_customer").prop('disabled', true);
        $("#long_customer").prop('disabled', true);
        $("#rental_start_date").prop('disabled', true);
        $("#rental_end_date").prop('disabled', true);
        $("#rental_origin").prop('disabled', true);
        $("#rental_destination").prop('disabled', true);
        $("#contract_start_date").prop('disabled', true);
        $("#contract_end_date").prop('disabled', true);
        $("#long_delivery_place").prop('disabled', true);
        $("#import_delivery_place").prop('disabled', true);
        $("#dealer").prop('disabled', true);
        $("#delivery_date").prop('disabled', true);
        $("#install_equipment_no").prop('disabled', true);
        $("#supplier_name").prop('disabled', true);
        $("#ie_destination").prop('disabled', true);

        var job_type = document.getElementById("job_type").value;
        showJobInfoSection(job_type);

        $('#job_type').on('select2:select', function(e) {
            var rental = document.getElementById("job_type").value;
            $("#short_worksheet_no").val('');
            $("#service_type").val('');
            $("#short_customer").val('');
            $("#rental_start_date").val('');
            $("#rental_end_date").val('');
            $("#rental_origin").val('');
            $("#rental_destination").val('');
            $("#install_equipment_no").val('');
            $("#supplier_name").val('');
            $("#ie_destination").val('');
            showJobInfoSection(rental);
        });

        $("#job_id").on('select2:select', function(e) {
            var data = e.params.data;
            var job_type = document.getElementById("job_type").value;
            axios.get("{{ route('admin.driving-jobs.default-service-type-rental') }}", {
                params: {
                    job_id: data.id,
                    job_type: job_type
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        if (response.data.job_type === 'App\\Models\\Rental') {
                            $("#short_worksheet_no").val(response.data.data.parent_worksheet_no);
                            $("#service_type").val(response.data.data.service_type);
                            $("#short_customer").val(response.data.data.parent_customer);
                            $("#rental_start_date").val(response.data.data.rental_start_date);
                            $("#rental_end_date").val(response.data.data.rental_end_date);
                            $("#rental_origin").val(response.data.data.rental_origin);
                            $("#rental_destination").val(response.data.data.rental_destination);
                        }
                        if (response.data.job_type === 'App\\Models\\LongTermRental') {
                            $("#long_worksheet_no").val(response.data.data.parent_worksheet_no);
                            $("#long_customer").val(response.data.data.parent_customer);
                            $("#contract_start_date").val(response.data.data.contract_start_date);
                            $("#contract_end_date").val(response.data.data.contract_end_date);
                        }
                        if (response.data.job_type === 'App\\Models\\ImportCar') {
                            $("#import_worksheet_no").val(response.data.data.parent_worksheet_no);
                            $("#dealer").val(response.data.data.dealer);
                        }
                        if (response.data.job_type === 'App\\Models\\InstallEquipment') {
                            $("#install_equipment_no").val(response.data.data.parent_worksheet_no);
                            $("#supplier_name").val(response.data.data.supplier);
                            $("#ie_destination").val(response.data.data.destination);
                        }
                    }
                    // if (response.data.service_type_rental.length > 0) {
                    // $("#service_type_rental").val(response.data.service_type_rental);
                    // if (response.data.service_type === '{{ ServiceTypeEnum::SELF_DRIVE }}') {
                    //     document.getElementById("self_drive_show").style.display = "block"
                    // } else {
                    //     document.getElementById("self_drive_show").style.display = "none"
                    //     $('input[name="self_drive_type"]').val('{{ SelfDriveTypeEnum::OTHER }}');
                    // }

                    // }
                }
            });
        });

        function showJobInfoSection(section)
        {
            $("#job_parent_table").show();
            $("#short_rental").hide();
            $("#long_rental").hide();
            $("#import_car").hide();
            $("#install_equipment").hide();

            if (section === 'App\\Models\\Rental') {
                $("#short_rental").show();
            } else if (section === 'App\\Models\\LongTermRental') {
                $("#long_rental").show();
            } else if (section === 'App\\Models\\ImportCarLine') {
                $("#import_car").show();
            } else if (section === 'App\\Models\\InstallEquipment') {
                $("#install_equipment").show();
            } else {
                $("#job_parent_table").hide();
            }
        }
    </script>
@endpush
