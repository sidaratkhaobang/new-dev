@push('scripts')
    <script>
        jQuery(function() {
            let expected_end_date_init = flatpickr("#expected_end_date");

            let start_date_init = flatpickr("#start_date", {
                minDate: "today",
                onClose: async function(selectedDates, dateStr, instance) {
                    setExpectedEndDate(selectedDates);
                }
            });

            function setExpectedEndDate(selectedDates) {
                var install_day_amount = $('#install_day_amount').val();
                const expected_end_date_input = flatpickr("#expected_end_date");
                let cloneDates = Object.assign({}, selectedDates);
                cloneDates = { ...selectedDates }; 
                cloneDates = new Date(cloneDates[0]);
                parsedDate = cloneDates.setDate(cloneDates.getDate() + parseInt(install_day_amount));
                let _expected_end_date = new Date(parsedDate);
                expected_end_date_input.setDate(_expected_end_date, false, "Y-m-d");
            }

            $("#install_day_amount").change(async function() {
                var val = $(this).val();
                if (start_date_init.selectedDates.length > 0 && parseInt(val) > 0 ) {
                    setExpectedEndDate(start_date_init.selectedDates)
                }
            });
        });
    </script>
@endpush
