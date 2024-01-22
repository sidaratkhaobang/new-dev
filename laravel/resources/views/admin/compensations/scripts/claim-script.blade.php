@push('scripts')
    <script src="https://raw.githack.com/jojoee/bahttext/master/src/index.js"></script>
    <script>
        $('#claim_amount, #claim_days').on('input', function(e) {
            var claim_amount = parseFloat($('#claim_amount').val().replace(/,/g, ''));
            var claim_days = parseFloat($('#claim_days').val().replace(/,/g, ''));
            if (isNaN(claim_amount)) {
                $('#claim_amount').val(0.00);
            }

            if (isNaN(claim_days)) {
                $('#claim_days').val(0.00);
            }

            if ((!isNaN(claim_amount) && !isNaN(claim_days))) {
                var sum_claim = parseFloat(claim_amount) * parseFloat(claim_days);
                baht_text = bahttext(sum_claim);
                sum_claim = parseFloat(sum_claim).toFixed(2).toLocaleString();
                sum_claim_text = sum_claim.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#claim_amount_total').html(sum_claim_text);
                $('#claim_amount_total_text').html(baht_text);
            }
        });
    </script>
@endpush