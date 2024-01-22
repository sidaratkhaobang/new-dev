@push('scripts')
<script>  
    $('#inspection_type_field').prop('disabled', true);
    $('#_inspection_type_field').prop('disabled', true);
    $('#car_code_field').prop('disabled', true);
    $('#license_plate_field').prop('disabled', true);
    $('#engine_no_field').prop('disabled', true);
    $('#chassis_no_field').prop('disabled', true);
    $('#install_worksheet_field').prop('disabled', true);
    $('#group_id_field').prop('disabled', true);
    $("#send-inspection-modal").prop('disabled', true);

    function callInspection(group_id)
    {
        if (!group_id) {
            return false;
        }

        axios.get("{{ route('admin.install-equipments.group-detail') }}", {
            params: {
                group_id: group_id
            }
        }).then(response => {
            if (response.data) {
                var data = response.data;
                $('#car_code_field').val(data.car_code);
                $('#license_plate_field').val(data.license_plate);
                $('#engine_no_field').val(data.engine_no);
                $('#chassis_no_field').val(data.chassis_no);
                $('#install_worksheet_field').val(data.worksheet_no);
                $('#group_id_field').val(data.group_id);
                $("#send-inspection-modal").modal("show");
            }
        });
    }

    function callAllInspection(group_id)
    {
        $("#send-all-inspection-modal").modal("show");
    }

    function createInspection()
    {
        group_id = $('#group_id_field').val();
        inspection_date = $('#inspection_date_field').val();
        data = {};
        data.group_id = group_id;
        data.inspection_date = inspection_date;
        callAPICreateInspection(data);
    }

    function callAPICreateInspection(data)
    {
        var route_uri = "{{ route('admin.install-equipments.create-inspection') }}";
        axios.post(route_uri, data).then(response => {
            if (response.data.success) {
                $("#send-inspection-modal").modal("hide");
                mySwal.fire({
                    title: "{{ __('lang.store_success_title') }}",
                    text: "{{ __('lang.store_success_message') }}",
                    icon: 'success',
                    confirmButtonText: "{{ __('lang.ok') }}"
                }).then(value => {
                    window.location.reload();
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

    function createAllInspection()
    {
        inspection_date = $('#inspection_all_date__field').val();
        if (!inspection_date) {
            return warningAlert("{{ __('lang.required_field_inform') }}");
        }
        data = {};
        data.inspection_date = inspection_date;
        data.inspect_all = true;
        callAPICreateInspection(data);
    }

</script>
@endpush