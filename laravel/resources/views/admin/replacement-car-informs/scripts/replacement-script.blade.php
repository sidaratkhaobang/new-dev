@push('scripts')
    <script>
    let job_id = '{{ $d->job_id }}';
    if (job_id) {
        // Fetch the preselected item
        fetchJobDetail(job_id);
    }

    $("#replacement_type").on('select2:select', function() {
            $('#job_id').val(null).trigger('change');;
            $('#job_type').val(null).trigger('change');;
            clearInputs();
        });

    $("#job_id").on('select2:select', function(e) {
        var data = e.params.data;
        fetchJobDetail(data.id);
    });

    $("#job_id").on('select2:clearing', function(e) {
        clearInputs();
    });

    $("#job_type").on('select2:select',function(){
        clearInputs();
    });

    function clearInputs() {
        $('#contract_no').val(null);
        $('#main_license_plate').val(null);
        $('#main_license_plate_id').val(null);
        $('#exist_replace_car_license').val(null);
        $('#exist_replace_car_car_class').val(null);
        $('#exist_replace_car_car_color').val(null);
    }

    function fetchJobDetail(id) {
        var job_type = document.getElementById("job_type").value;
        axios.get("{{ route('admin.util.select2-replacement-car.job-detail') }}", {
            params: {
                id: id,
                job_type: job_type,
            }
        }).then(response => {
            clearInputs();
            if (response.data) {
                data = response.data;
                $('#contract_no').val(data.contract_no);
                $('#main_license_plate').val(data.main_car);
                $('#main_car_id').val(data.car_id);
                $('#exist_replace_car_license').val(data.replacement_car);
                $('#exist_replace_car_car_class').val(data.replacement_car_class);
                $('#exist_replace_car_car_color').val(data.replacement_car_color);
                $('#job_type').val(data.job_type).trigger('change');
                var tempJobIdOption = new Option(data.worksheet_no, data.id, true, true);
                $("#job_id").append(tempJobIdOption).trigger('change');
            }
        });
    }

    let required_lower_spec = @if (isset($required_lower_spec)) @json($required_lower_spec) @else false @endif;
    let replacement_car = @if (isset($replacement_car)) @json($replacement_car) @else null @endif;
    if (required_lower_spec) {
        var newOption = new Option("{{ __('replacement_cars.no_same_spec') }}", 1, true, true);
        // $('#replacement_car_id').append(newOption).trigger('change');
        $('#replacement_car_id').prop('disabled', true);
        // if (replacement_car && replacement_car.length > 0) {
        //     __log(replacement_car);
        //     var newOption = new Option(replacement_car.license_plate, replacement_car.id, true, true);
        //     $('#replacement_car_id').append(newOption).trigger('change');
        // }

    }

    if (replacement_car && Object.keys(replacement_car).length !== 0) {
        var newOption = new Option(replacement_car.license_plate, replacement_car.id, true, true);
        $('#replacement_car_id').append(newOption).trigger('change');
    }


    $('#is_spec_low_0').change(function() {
        if(this.checked) {
            $('#replacement_car_id').prop('disabled', false);
        }
    });

    $('#replacement_car_id').prop('disabled', false);
    // $("#replacement_car_id").select2({
    //         placeholder: "{{ __('lang.select_option') }}",
    //         allowClear: true,
    //         ajax: {
    //             delay: 250,
    //             url: function (params) {
    //                 return "{{ route('admin.replacement-cars.get-replacement-cars') }}";
    //             },
    //             type: 'GET',
    //             data: function (params) {
    //                 long_term_rental_id = $('#id').val();
    //                 return {
    //                     long_term_rental_id: long_term_rental_id,
    //                     s: params.term
    //                 }
    //             },
    //             processResults: function (data) {
    //                 return {
    //                     results: data
    //                 };
    //             },
    //         }
    //     });

    </script>
@endpush
