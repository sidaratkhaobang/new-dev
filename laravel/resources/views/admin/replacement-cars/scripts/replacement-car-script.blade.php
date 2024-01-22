@push('scripts')
    <script>
        $("#replacement_car_id").on('select2:select', function(e) {
            var data = e.params.data;
            fetchReplacementCarDetail(data.id);
            $('div#replace-car-detail-section').addClass('show');
            $('div#replace-car-detail-section').removeClass('hide');
        });

        $("#replacement_car_id").on('select2:clearing', function(e) {
            $('div#replace-car-detail-section').removeClass('show');
            $('div#replace-car-detail-section').addClass('hide');
        });


        function fetchReplacementCarDetail(id) {
            axios.get("{{ route('admin.replacement-cars.replacement-car-detail') }}", {
                params: {
                    id: id
                }
            }).then(response => {
                if (response.data) {
                    data = response.data;
                    if (data.image) {
                        $('#replace-img').attr('src', data.image.url);
                    }
                    $('#replace-class-name').html(data.class_name);
                    $('#replace-chassis-no').html(data.chassis_no);
                    $('#replace-license-plate').html(data.license_plate);
                    $('#replace-policy-number').html(data.plicy_number);
                    $('#replace-policy-start-date').html(data.policy_start_date);
                    $('#replace-policy-end-date').html(data.policy_end_date);
                    $('#replace-insurance-no').html(data.insurance_no);
                    $('#replace-insurance-company').html(data.insurance_company);
                    $('#replace-insurance-start-date').html(data.insurance_start_date);
                    $('#replace-insurance-end-date').html(data.insurance_end_date);
                }
            });
        }
    </script>
@endpush
