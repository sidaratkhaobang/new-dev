@push('scripts')
    <script>
        $('#refund, #refund_stamp, #refund_vat').on('input', function() {
            var refund = parseFloat($('#refund').val().replace(/,/g, ''));
            var refund_stamp = parseFloat($('#refund_stamp').val().replace(/,/g, ''));
            var refund_vat = parseFloat($('#refund_vat').val().replace(/,/g, ''));

            if (isNaN(refund)) {
                $('#refund').val(0);
            }

            if (isNaN(refund_stamp)) {
                $('#refund_stamp').val(0);
            }

            if (isNaN(refund_vat)) {
                $('#refund_vat').val(0);
            }

 
            if (!isNaN(refund) && !isNaN(refund_stamp) && !isNaN(refund_vat)) {
                var sum_refund = parseFloat(refund) + parseFloat(refund_stamp) + parseFloat(refund_vat);
                sum_refund = parseFloat(sum_refund).toFixed(2).toLocaleString();
                sum_text = sum_refund.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#refund_total').val(sum_text);

                var one_percent_tax = (1 / 100) * sum_refund;
                one_percent_tax = parseFloat(one_percent_tax).toFixed(2).toLocaleString();
                one_percent_tax_text = one_percent_tax.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#refund_withholding_tax').val(one_percent_tax_text);
            }
        });
    </script>
@endpush
