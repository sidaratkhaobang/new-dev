@push('scripts')
    <script>
        $("#insurance_package_id").on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.vmi-cars.insurance-package-detail') }}", {
            params: {
                id: data.id,
            }
        }).then(response => {
            if (response.data) {
                __log(response.data);
                var insurance_package = response.data;
                if (insurance_package) { 
                    $("#tpbi_person").val(insurance_package.tpbi_person);
                    $("#bail_bond").val(insurance_package.bail_bond);
                    $("#medical_exp").val(insurance_package.medical_exp);
                    $("#pa_driver").val(insurance_package.pa_driver);
                    $("#pa_passenger").val(insurance_package.pa_passenger);
                    $("#tpbi_aggregate").val(insurance_package.tpbi_aggregate);
                    $("#tpbi_person").val(insurance_package.tpbi_person);
                    $("#tppd_aggregate").val(insurance_package.tppd_aggregate);
                }
                // if (response.data) {
                //     var insurance_package = response.data.data;
                //     var defaultSupplierOption = {
                //         id: supplier.id,
                //         text: supplier.name,
                //     };
                //     var tempSupplierOption = new Option(defaultSupplierOption.text, defaultSupplierOption.id, true, true);
                //     $("#excel_supplier_id").append(tempSupplierOption).trigger('change');
                //     var tempInstallEquipOption = new Option(data.text, data.id, true, true);
                //     $("#excel_install_equipment_id").append(tempInstallEquipOption).trigger('change');
                // }
            }
        });
    });

    </script>
@endpush