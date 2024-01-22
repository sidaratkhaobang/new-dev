@push('scripts')
    <script>
    function openModalPrintRentalRequisition(id) {
        $('#_temp_id').val(id);
        $('#modal-print-rental').modal('show');
    }

    $('#modal-print-rental').on('hidden.bs.modal', function () {
        $('#lt_rental_car_class').val(null).trigger('change');
        $("#lt_rental_car_class_amount").val(null);
        $('#lt_months').val(null).trigger('change');        
    });

    $("#lt_rental_car_class").select2({
        placeholder: "{{ __('lang.select_option') }}",
        allowClear: true,
        dropdownParent: $("#modal-print-rental"),
        ajax: {
            delay: 250,
            url: function (params) {
                return "{{ route('admin.util.select2-rental.lt-rental-line-car-classes') }}";
            },
            type: 'GET',
            data: function (params) {
                long_term_rental_id = $('#_temp_id').val();
                return {
                    long_term_rental_id: long_term_rental_id,
                    s: params.term
                }
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
        }
    });

    $("#lt_months").select2({
        placeholder: "{{ __('lang.select_option') }}",
        allowClear: true,
        dropdownParent: $("#modal-print-rental"),
        ajax: {
            delay: 250,
            url: function (params) {
                return "{{ route('admin.util.select2-rental.lt-rental-months') }}";
            },
            type: 'GET',
            data: function (params) {
                long_term_rental_id = $('#_temp_id').val();
                return {
                    long_term_rental_id: long_term_rental_id,
                    s: params.term
                }
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
        }
    });


    $('#lt_rental_car_class').on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.util.select2-rental.lt-rental-line-car-amount') }}", {
            params: {
                id: data.id
            }
        }).then(response => {
            if (response.data) {
                maxlength = parseInt(response.data);
                $("#lt_rental_car_class_amount").val(maxlength);
                $("#lt_rental_car_class_amount").attr('max', maxlength); 
            }
        });
    });

    function printRentalRequisition() {
        var lt_rental_line_id = $('#lt_rental_car_class').val();
        var lt_rental_car_class_amount = $('#lt_rental_car_class_amount').val();
        var lt_month_id = $('#lt_months').val();
        var long_term_rental_id = $('#_temp_id').val();

        axios.get("{{ route('admin.long-term-rentals.print-rental-requisition') }}", {
            params: {
                long_term_rental: long_term_rental_id,
                lt_rental_line_id: lt_rental_line_id,
                lt_rental_car_class_amount: lt_rental_car_class_amount,
                lt_month_id: lt_month_id,
            }
        }).then(response => {
            if (response.data.success) {
                window.open(response.data.redirect, "_blank");
                $('#modal-print-rental').modal('hide');
            } else {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: response.data.message,
                    icon: 'warning',
                    confirmButtonText: "{{ __('lang.ok') }}",
                });
            }
        });
    }
    </script>
@endpush