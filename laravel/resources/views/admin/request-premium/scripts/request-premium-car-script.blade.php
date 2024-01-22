@push('scripts')
    <script>
        $(document).on('click', ".btn-coverage-apply-all", function () {
            var block_content = $(this).closest('.block-content')
            let input_insurance_life_person = $(block_content).find('.input-insurance-life-person').val()
            let input_insurance_life_total = $(block_content).find('.input-insurance-life-total').val()
            let input_insurance_property = $(block_content).find('.input-insurance-property').val()
            let input_insurance_first = $(block_content).find('.input-insurance-first').val()
            let input_insurance_car_damage = $(block_content).find('.input-insurance-car-damage').val()
            let input_insurance_car_accident = $(block_content).find('.input-insurance-car-accident').val()
            let input_insurance_car_body = $(block_content).find('.input-insurance-car-body').val()
            let input_insurance_driver = $(block_content).find('.input-insurance-driver').val()
            let input_insurance_passenger = $(block_content).find('.input-insurance-passenger').val()
            let input_insurance_healthcare = $(block_content).find('.input-insurance-healthcare').val()
            let input_insurance_bail = $(block_content).find('.input-insurance-bail').val()
            $('.block-car-coverage').not(block_content).each(function (e) {
                $(this).find('.input-insurance-life-person').val(input_insurance_life_person)
                $(this).find('.input-insurance-life-total').val(input_insurance_life_total)
                $(this).find('.input-insurance-property').val(input_insurance_property)
                $(this).find('.input-insurance-first').val(input_insurance_first)
                $(this).find('.input-insurance-car-damage').val(input_insurance_car_damage)
                $(this).find('.input-insurance-car-accident').val(input_insurance_car_accident)
                $(this).find('.input-insurance-car-body').val(input_insurance_car_body)
                $(this).find('.input-insurance-driver').val(input_insurance_driver)
                $(this).find('.input-insurance-passenger').val(input_insurance_passenger)
                $(this).find('.input-insurance-healthcare').val(input_insurance_healthcare)
                $(this).find('.input-insurance-bail').val(input_insurance_bail)
            })
        })

    </script>
@endpush
