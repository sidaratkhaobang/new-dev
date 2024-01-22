@push('scripts')
    <script>
        $(document).on('click', ".btn-premium-apply-all", function () {
            var block_premium = $(this).closest('.block-premium')
            var element_premium = $(block_premium).find('.premium_data')
            var class_name = $(element_premium).data('premium')
            var data = [];
            $(element_premium).each(function (index) {
                var data_premium = $(this).find('input').map(function () {
                    return $(this).val()
                }).get();
                data.push(data_premium)
            })
            copyPremiumData(data, class_name)
        })

        $(document).on('keyup', ".input-premium-first-year,.input-compulsory-motor-insurance-premium", function () {
            let parent = $(this).closest('.premium_data');
            let premium_first_year_data = $(parent).find('.input-premium-first-year').val()
            let compulsory_motor_insurance_premium = $(parent).find('.input-compulsory-motor-insurance-premium').val()
            if (premium_first_year_data && compulsory_motor_insurance_premium) {
                let insurance_premium_sum_data = parseInt(premium_first_year_data.replace(',', '')) + parseInt(compulsory_motor_insurance_premium.replace(',', ''));
                $(parent).find('#insurance_premium_sum').val(insurance_premium_sum_data.toLocaleString())
            }
        })

        $(document).on('keyup', ".input-premium-per-year,.input-compulsory-motor-insurance-premium", function () {
            let parent = $(this).closest('.premium_data');
            let input_premium_per_year = $(parent).find('.input-premium-per-year').val().replace(/,/g, '')
            let compulsory_motor_insurance_premium = $(parent).find('.input-compulsory-motor-insurance-premium').val().replace(/,/g, '')
            if (input_premium_per_year && compulsory_motor_insurance_premium) {
                let insurance_premium_sum_data = parseInt(input_premium_per_year) + parseInt(compulsory_motor_insurance_premium);
                $(parent).find('#insurance_premium_year_sum').val(insurance_premium_sum_data.toLocaleString())
            }
        })

        function copyPremiumData(premiumdata, element_premium) {
            if (premiumdata) {
                premiumdata.forEach(function (e, i) {
                    let class_name = `premium_data${i}`

                    $(`.${class_name}`).each(function () {
                        $(this).find('input').eq(0).val(e[0])
                        $(this).find('input').eq(1).val(e[1])
                        $(this).find('input').eq(2).val(e[2])
                        $(this).find('input').eq(3).val(e[3])
                        $(this).find('input').eq(4).val(e[4])

                    })
                })
            }
        }
    </script>
@endpush
