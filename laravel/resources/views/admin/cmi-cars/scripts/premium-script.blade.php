@push('scripts')
    <script>
        $('#premium, #discount, #stamp_duty, #tax').on('input', function(e) {
            var premium = parseFloat($('#premium').val().replace(/,/g, ''));
            var discount = parseFloat($('#discount').val().replace(/,/g, ''));
            var stamp_duty = parseFloat($('#stamp_duty').val().replace(/,/g, ''));
            var tax = parseFloat($('#tax').val().replace(/,/g, ''));

            if (isNaN(premium)) {
                $('#premium').val(0.00);
            }

            if (isNaN(discount)) {
                $('#discount').val(0.00);
            }

            if (isNaN(tax)) {
                $('#tax').val(0.00);
            }

            if (isNaN(stamp_duty)) {
                $('#stamp_duty').val(0.00);
            }

            if (parseFloat(discount) >= parseFloat(premium)) {
                if (e.originalEvent.inputType != 'deleteContentBackward') {
                    discount = premium;
                    discount = parseFloat(discount).toFixed(2).toLocaleString();
                    discount_text = discount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    $('#discount').val(discount_text);
                }
            }

            if (!isNaN(premium) && !isNaN(discount) && !isNaN(stamp_duty)  && !isNaN(tax)) {
                var _sum_premium = parseFloat(premium) - parseFloat(discount) + parseFloat(stamp_duty) + parseFloat(tax);
                sum_premium = parseFloat(_sum_premium).toFixed(2).toLocaleString();
                sum_text = sum_premium.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#premium_total').val(sum_text);

                var one_percent_tax = (1 / 100) * sum_premium;
                one_percent_tax = _sum_premium - one_percent_tax;
                one_percent_tax = parseFloat(one_percent_tax).toFixed(2).toLocaleString();
                one_percent_tax_text = one_percent_tax.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $('#withholding_tax').val(one_percent_tax_text);
            }
        });
    </script>
@endpush
