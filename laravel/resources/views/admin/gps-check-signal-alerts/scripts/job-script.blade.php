@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#job_type').prop('disabled', true);
            $('#job_id').prop('disabled', true);
            $('#car_id').prop('disabled', true);
            $('#must_check_date').prop('disabled', true);
        }

        var job_type = document.getElementById("job_type").value;
        if (job_type === 'App\\Models\\Rental') {
            document.getElementById("default-car").style.display = "block"
        } else if (job_type === 'App\\Models\\LongTermRental') {
            document.getElementById("default-car").style.display = "block"
        } else if (job_type === 'App\\Models\\ReplacementCar') {
            document.getElementById("default-car").style.display = "block"
        } else {
            document.getElementById("default-car").style.display = "none"
        }

        $('#job_type').on('select2:select', function(e) {
            var job_type = document.getElementById("job_type").value;
            $("#short_worksheet_no").text('');
            $("#service_type").text('');
            $("#short_customer").text('');
            $("#pickup_date").text('');
            $("#return_date").text('');
            $("#long_worksheet_no").text('');
            $("#rental_duration").text('');
            $("#long_type").text('');
            $("#delivery_date").text('');
            $("#delivery_place").text('');
            $("#replacement_no").text('');
            $("#replacement_type").text('');
            $("#replacement_date").text('');
            $("#replacement_place").text('');
            $("#replacement_customer").text('');
            $("#must_check_date").val('');
            $("#engine_no").text('');
            $("#chassis_no").text('');
            $("#car_class").text('');
            $("#car_color").text('');
            $("#fleet").text('');
            $("#vid").text('');
            $("#sim").text('');
            $("#car_id").val(null).trigger('change');
            if (job_type === 'App\\Models\\Rental') {
                document.getElementById("short_rental").style.display = "block"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "block"
            } else if (job_type === 'App\\Models\\LongTermRental') {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "block"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "block"
            } else if (job_type === 'App\\Models\\ReplacementCar') {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "block"
                document.getElementById("default-car").style.display = "none"
            } else {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "none"
            }
        });

        $('#job_id').on('select2:select', function(e) {
            $("#short_worksheet_no").text('');
            $("#service_type").text('');
            $("#short_customer").text('');
            $("#pickup_date").text('');
            $("#return_date").text('');
            $("#long_worksheet_no").text('');
            $("#rental_duration").text('');
            $("#long_type").text('');
            $("#delivery_date").text('');
            $("#delivery_place").text('');
            $("#replacement_no").text('');
            $("#replacement_type").text('');
            $("#replacement_date").text('');
            $("#replacement_place").text('');
            $("#replacement_customer").text('');
            $("#must_check_date").val('');
            $("#engine_no").text('');
            $("#chassis_no").text('');
            $("#car_class").text('');
            $("#car_color").text('');
            $("#fleet").text('');
            $("#vid").text('');
            $("#sim").text('');
            $("#car_id").val(null).trigger('change');
            var job_type = document.getElementById("job_type").value;
            if (job_type === 'App\\Models\\Rental') {
                document.getElementById("short_rental").style.display = "block"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "block"
            } else if (job_type === 'App\\Models\\LongTermRental') {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "block"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "block"
            } else if (job_type === 'App\\Models\\ReplacementCar') {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "block"
                document.getElementById("default-car").style.display = "block"
            } else {
                document.getElementById("short_rental").style.display = "none"
                document.getElementById("long_rental").style.display = "none"
                document.getElementById("replacement_car").style.display = "none"
                document.getElementById("default-car").style.display = "none"
            }
        });

        $("#job_id").on('select2:select', function(e) {
            var data = e.params.data;
            var job_type = document.getElementById("job_type").value;
            axios.get("{{ route('admin.gps-check-signals.default-data-job') }}", {
                params: {
                    job_id: data.id,
                    job_type: job_type
                }
            }).then(response => {
                if (response.data.success) {
                    console.log(response.data.data);
                    if (response.data.data) {
                        if (response.data.job_type === 'App\\Models\\Rental') {
                            $("#short_worksheet_no").text(response.data.data.worksheet_no);
                            $("#service_type").text(response.data.data.service_type);
                            $("#short_customer").text(response.data.data.customer);
                            $("#pickup_date").text(response.data.data.pickup_date);
                            $("#return_date").text(response.data.data.return_date);
                            flatpickr("#must_check_date", {
                                defaultDate: response.data.data.must_check_date,
                            });
                        }
                        if (response.data.job_type === 'App\\Models\\LongTermRental') {
                            $("#long_worksheet_no").text(response.data.data.worksheet_no);
                            $("#rental_duration").text(response.data.data.rental_duration);
                            $("#long_type").text(response.data.data.long_type);
                            $("#delivery_date").text(response.data.data.delivery_date);
                            $("#delivery_place").text(response.data.data.delivery_place);
                            flatpickr("#must_check_date", {
                                defaultDate: response.data.data.must_check_date,
                            });
                        }
                        if (response.data.job_type === 'App\\Models\\ReplacementCar') {
                            $("#replacement_no").text(response.data.data.worksheet_no);
                            $("#replacement_type").text(response.data.data.replacement_type);
                            $("#replacement_customer").text(response.data.data.replacement_customer);
                            $("#replacement_date").text(response.data.data.replacement_date);
                            $("#replacement_place").text(response.data.data.replacement_place);
                            flatpickr("#must_check_date", {
                                defaultDate: response.data.data.must_check_date,
                            });
                        }
                    }
                }
            });
        });

        $("#car_id").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.gps-check-signals.default-car-id') }}";
                },
                type: 'GET',
                data: function(params) {
                    parent_id = $("#job_id").val();
                    parent_type = $("#job_type").val();
                    return {
                        parent_id: parent_id,
                        parent_type: parent_type,
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

        $("#car_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.gps-check-signals.default-data-car') }}", {
                params: {
                    car_id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        $("#engine_no").text(response.data.data.engine_no);
                        $("#chassis_no").text(response.data.data.chassis_no);
                        $("#car_class").text(response.data.data.car_class);
                        $("#car_color").text(response.data.data.car_color);
                        $("#fleet").text(response.data.data.fleet);
                        $("#vid").text(response.data.data.vid);
                        $("#sim").text(response.data.data.sim);
                    }
                }
            });
        });
    </script>
@endpush
