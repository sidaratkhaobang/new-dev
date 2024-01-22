@push('scripts')
    <script>
    $('#accessory_class_field').prop('disabled', true);
    $("#accessory_field").select2({
        placeholder: "{{ __('lang.select_option') }}",
        allowClear: true,
        dropdownParent: $("#install-equipment-modal"),
        ajax: {
            delay: 250,
            url: function (params) {
                return "{{ route('admin.util.select2-install-equipment.accessories') }}";
            },
            type: 'GET',
            data: function (params) {
                supplier_id = $("#accessory_supplier_field").val();
                return {
                    supplier_id: supplier_id,
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

    $("#accessory_supplier_field").select2({
        placeholder: "{{ __('lang.select_option') }}",
        allowClear: true,
        dropdownParent: $("#install-equipment-modal"),
        ajax: {
            delay: 250,
            url: function (params) {
                return "{{ route('admin.util.select2-install-equipment.suppliers') }}";
            },
            type: 'GET',
            data: function (params) {
                accessory_id = $("#accessory_field").val();
                return {
                    accessory_id: accessory_id,
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

    $("#accessory_field").on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.install-equipments.accessory-detail') }}", {
            params: {
                accessory_id: data.id
            }
        }).then(response => {
            clearAccessoryInputs();
            if (response.data) {
                accessory = response.data;
                $('#accessory_class_field').val(accessory.version);
                $('#accessory_amount_field').val(1);
                $('#accessory_price_field').val(numberWithCommas(accessory.price));
                $("#accessory_supplier_field").val(null).change();
                if (accessory.creditor_id) {
                    var defaultSupplierOption = {
                        id: accessory.creditor_id,
                        text: accessory.creditor_text,
                    };
                    var tempSupplierOption = new Option(defaultSupplierOption.text, defaultSupplierOption.id, true, true);
                    $("#accessory_supplier_field").append(tempSupplierOption).trigger('change');
                }
            }
        });
    });

    $("#accessory_field").on('select2:clearing', function(e) {
        clearAccessoryInputs();
    });


    function clearAccessoryInputs()
    {
        $('#accessory_class_field').val(null);
        $('#accessory_amount_field').val(0);
        $('#accessory_price_field').val(null);
        $("#accessory_supplier_field").val(null).change();
    }

    </script>
@endpush