@push('scripts')
    <script>
        let addExcelVue = new Vue({
            el: '#install-equipment-excel',
            data: {
                excel_list: @if (isset($excel_list))
                    @json($excel_list)
                @else
                    []
                @endif ,
                supplier_id: null,
                supplier_name: null,
            },
            watch: {
                excel_list: function(_excel_list) {
                    if (_excel_list.length == 0) {
                        $("#excel_supplier_id").prop('disabled', false);
                    }
                },
            },
            methods: {
                display: function() {
                    $("#install-equipments").show();
                },
                addExcel: async function() {
                    var excel_supplier_id = $("#excel_supplier_id").val();
                    var excel_install_equipment_id = $("#excel_install_equipment_id").val();
                    if (!excel_supplier_id) {
                        return warningAlert("กรุณาเลือก Supplier");
                    }
                    if (!excel_install_equipment_id) {
                        return warningAlert("กรุณาเลือก เลขที่ใบสั่งซื้ออุปกรณ์");
                    }
                    if (this.supplier_id) {
                        if (this.supplier_id != excel_supplier_id) {
                            return warningAlert("Supplier ต้องเป็น Supplier เดียวกัน");
                        }
                    }

                    install_equipment_detail = await this.getInstallEquipmentDetail(excel_install_equipment_id);
                    if (!install_equipment_detail) {
                        return warningAlert("{{ DATA_NOT_FOUND }}");
                    }
                    var item = {};
                    item.id = install_equipment_detail.id;
                    item.po_worksheet_no = install_equipment_detail.po_worksheet_no;
                    item.worksheet_no = install_equipment_detail.worksheet_no;
                    item.supplier_name = install_equipment_detail.supplier_name;
                    const already_exist = this.excel_list.some(function(el) {
                        return el.id === install_equipment_detail.id;
                    });
                    if (already_exist) {
                        return warningAlert("มีเลขที่ใบสั่งซื้ออุปกรณ์นี้อยู่แล้ว");
                    }
                    this.excel_list.push(item);
                    this.supplier_id = excel_supplier_id;
                    this.supplier_name = install_equipment_detail.supplier_name;
                    if (this.supplier_id) {
                        $("#excel_supplier_id").prop('disabled', true);
                    }
                },
                async getInstallEquipmentDetail(id) {
                    const url = "{{ route('admin.install-equipments.install-equipment-detail') }}";
                    const response = await axios.get(url, {
                        params: {
                            install_equipment_id: id,
                        }
                    });
                    return response.data.data;
                },
                exportExcel: function() {

                },
                clearModel: function() {
                    this.excel_list = [];
                },
                clearModalData: function() {
                    $("#excel_supplier_id").val(null).change();
                    $("#excel_install_equipment_id").val(null).change();
                },
                openModal: function() {
                    $("#excel-modal").modal("show");
                },
                hideModal: function() {
                    clearModalData();
                    $("#excel-modal").modal("hide");
                },
                spliecItem: function(index) {
                    this.excel_list.splice(index, 1);
                },
                remove: function(index) {
                    this.spliecItem(index);
                    if (this.excel_list.length == 0) {
                        this.supplier_id = null;
                        this.supplier_name = null;
                    }
                },
                getInstallEquipmentIds: function() {
                    return this.excel_list.map((obj) => obj.id);
                },
                getSupplierName: function() {
                    return this.supplier_name;
                }
            },
            props: ['title'],
        });
        addExcelVue.display();
        window.addExcelVue = addExcelVue;

        function openExcelModal() {
            addExcelVue.clearModalData();
            addExcelVue.clearModel();
            $("#excel-modal").modal("show");
        }

        $('#excel-modal').on('hidden.bs.modal', function() {
            addExcelVue.clearModalData();
            addExcelVue.clearModel();
        });

        function addExcel() {
            addExcelVue.addExcel();
        }

        function exportExcel() {
            var install_equipment_ids = addExcelVue.getInstallEquipmentIds();
            var supplier_id = addExcelVue.getInstallEquipmentIds();
            var supplier_name = addExcelVue.getSupplierName();
            if (install_equipment_ids.length <= 0) {
                return warningAlert("{{ __('lang.required_field_inform') }}");
            }

            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.install-equipments.export-excel') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    install_equipment_ids: install_equipment_ids,
                },
                success: function(result, status, xhr) {
                    var fileName = 'รายการติดตั้งอุปกรณ์เสริม.xlsx';
                    if (supplier_name) {
                        var fileName = supplier_name + '.xlsx';
                    }
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(result, status, xhr) {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
            $("#excel-modal").modal("hide");
        }

    $("#excel_install_equipment_id").on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.install-equipments.install-equipment-detail') }}", {
            params: {
                install_equipment_id: data.id,
            }
        }).then(response => {
            if (response.data) {
                var response = response.data;
                if (response.data) {
                    var supplier = response.data.supplier;
                    var defaultSupplierOption = {
                        id: supplier.id,
                        text: supplier.name,
                    };
                    var tempSupplierOption = new Option(defaultSupplierOption.text, defaultSupplierOption.id, true, true);
                    $("#excel_supplier_id").append(tempSupplierOption).trigger('change');
                    var tempInstallEquipOption = new Option(data.text, data.id, true, true);
                    $("#excel_install_equipment_id").append(tempInstallEquipOption).trigger('change');
                }
            }
        });
    });

    </script>
@endpush
