@push('scripts')
    <script>
        $("#car_id").on('select2:select', function(e) {
            $("#license_plate").text('');
            $("#chassis_no").text('');
            $("#car_class").text('');
            $("#current_mileage").text('');
            $("#rental_id").val('');
            $("#rental_type").val('');
            $("#rental_no").text('');
            $("#rental_name").text('');
            $("#contract_no").text('');
            $("#contract_start_date").text('');
            $("#contract_end_date").text('');
            var data = e.params.data;
            axios.get("{{ route('admin.repairs.data-car') }}", {
                params: {
                    car_id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    document.getElementById("car-detail-section").style.display = "block"
                    document.getElementById("car-btn-modal").style.display = "block"
                    if (response.data.data) {
                        $("#license_plate").text(response.data.data.license_plate);
                        $("#chassis_no").text(response.data.data.chassis_no);
                        $("#car_class").text(response.data.data.car_class_name);
                        $("#current_mileage").text(response.data.data.current_mileage);
                        document.getElementById("car_status").style.display = "block";
                        var car_status = document.getElementById("car_status");
                        car_status.innerHTML =
                            `<span class="badge badge-custom badge-bg-${response.data.data.status_class}" >${ response.data.data.status_text }</span>`;
                        if (response.data.data.rental > 0) {
                            document.getElementById("rental_show").style.display = "block"
                            $("#rental_id").val(response.data.data.rental_id);
                            $("#rental_type").val(response.data.data.rental_type);
                            $("#rental_no").text(response.data.data.rental_worksheet_no);
                            $("#rental_name").text(response.data.data.rental_customer_name);
                            $("#contract_no").text(response.data.data.contract_worksheet_no);
                            $("#contract_start_date").text(response.data.data.contract_pick_up_date);
                            $("#contract_end_date").text(response.data.data.contract_return_date);
                        } else {
                            document.getElementById("rental_show").style.display = "none"
                        }
                    }
                }
            });
        });
    </script>
@endpush
