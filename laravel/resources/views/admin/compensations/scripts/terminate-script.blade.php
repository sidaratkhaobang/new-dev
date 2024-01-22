@push('scripts')
    <script>
        $('#termination_amount, #termination_days').on('input', function(e) {
            var termination_amount = parseFloat($('#termination_amount').val().replace(/,/g, ''));
            var termination_days = parseFloat($('#termination_days').val().replace(/,/g, ''));
            if (isNaN(termination_amount)) {
                $('#termination_amount').val(0.00);
            }

            if (isNaN(termination_days)) {
                $('#termination_days').val(1);
            }

            if ((!isNaN(termination_amount) && !isNaN(termination_days)) && (termination_days > 0)) {
                var termination_avg = parseFloat(termination_amount) / parseFloat(termination_days);
                termination_avg = parseFloat(termination_avg).toFixed(2).toLocaleString();
                termination_avg_text = termination_avg.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#termination_avg').val(termination_avg_text);
            }
        });
    </script>
@endpush