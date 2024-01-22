@push('scripts')
    <script>
        const show_replacement_arr = ['SEND_MAIN_RECEIVE_REPLACE', 'RECEIVE_REPLACE'];
        // let replacement_type = '{{ $d->replacement_type }}';
        // if (replacement_type) {
        //     if (show_replacement_arr.includes(replacement_type)) {
        //         $('.short-replacement-car-section').removeClass('hide');
        //         $('.short-replacement-car-section').addClass('show');
        //     }
        // }

        $('#exist_replace_car_license').prop('disabled', true);
        $('#exist_replace_car_car_class').prop('disabled', true);
        $('#exist_replace_car_car_color').prop('disabled', true);

        $("#replacement_type").on('select2:select', function(e) {
            var data = e.params.data;
            if (show_replacement_arr.includes(data.id)) {
                $('.short-replacement-car-section').removeClass('hide');
                $('.short-replacement-car-section').addClass('show');
            } else {
                $('.short-replacement-car-section').removeClass('show');
                $('.short-replacement-car-section').addClass('hide');
            }
        });
        
    </script>
@endpush