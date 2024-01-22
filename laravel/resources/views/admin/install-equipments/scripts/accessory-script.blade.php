@push('scripts')
<script>
    let addInstallEquipmentVue = new Vue({
        el: '#install-equipments',
        data: {
            install_equipment_line_list: @if (isset($install_equipment_line_list)) @json($install_equipment_line_list) @else [] @endif,
            edit_index: null,
            mode: null,
            pending_delete_install_equipment_ids: [],
            supplier: @if (isset($supplier)) @json($supplier) @else null @endif,
            bom_list: []
        },
        methods: {
            display: function() {
                $("#install-equipments").show();
            },
            addInstallEquipment: function() {
                this.setIndex(this.setLastIndex());
                this.clearModalData();
                this.mode = 'add';
                this.openModal();
            },
            edit: function(index) {
                this.setIndex(index);
                this.loadModalData(index);
                this.mode = 'edit';
                $("#install-equipment-modal-label").html('แก้ไขข้อมูลอุปกรณ์เสริม');
                this.openModal();
            },
            clearModalData: function() {
                $("#accessory_field").val(null).change();
                $("#accessory_class_field").val(null).change();
                $("#accessory_amount_field").val('');
                $("#accessory_price_field").val('');
                $("#accessory_supplier_field").val(null).change();
                $("#accessory_remark_field").val('');
                $("#remark_field").val('');
                if (this.supplier) {
                    $("#accessory_supplier_field").val(this.supplier.id).change();
                    var tempSupplierOption = new Option(this.supplier.name, this.supplier.id, true, true);
                    $("#accessory_supplier_field").append(tempSupplierOption).trigger('change');
                }
            },
            loadModalData: function(index) {
                var temp = null;
                temp = this.install_equipment_line_list[index];
                
                $("#accessory_class_field").val(temp.accessory_class);
                $("#accessory_amount_field").val(temp.amount);
                $("#accessory_price_field").val(temp.price);
                $("#accessory_remark_field").val(temp.remark);

                $("#accessory_field").val(temp.accessory_id).change();
                var tempAccessoryOption = new Option(temp.accessory_text, temp.accessory_id, true, true);
                $("#accessory_field").append(tempAccessoryOption).trigger('change');

                $("#accessory_supplier_field").val(temp.supplier_id).change();
                var tempSupplierOption = new Option(temp.supplier_text, temp.supplier_id, true, true);
                $("#accessory_supplier_field").append(tempSupplierOption).trigger('change');
            },
            openModal: function() {
                $("#install-equipment-modal").modal("show");
                if (this.supplier) {
                    $("#accessory_supplier_field").val(this.supplier.id).change();
                    var tempSupplierOption = new Option(this.supplier.name, this.supplier.id, true, true);
                    $("#accessory_supplier_field").append(tempSupplierOption).trigger('change');
                    $("#accessory_supplier_field").prop('disabled', true);
                }
            },
            hideModal: function() {
                $("#install-equipment-modal").modal("hide");
            },
            save: function() {
                var _this = this;
                if (_this.mode == 'edit') {
                    var index = _this.edit_index;
                    _this.saveEdit(index);
                } else {
                    _this.saveAdd();
                }
            },
            getDataFromModalAdd: function() {
                var _this = this;
                var total = 0;
                var accessory_id = $("#accessory_field").val();
                var accessory_text = $("#accessory_field option:selected").text();
                // var accessory_code = $("#accessory_code_field option:selected").text();
                var accessory_class = $("#accessory_class_field").val();
                var amount = $("#accessory_amount_field").val();
                var price = $("#accessory_price_field").val();
                var supplier_id = $("#accessory_supplier_field").val();
                var supplier_text = $("#accessory_supplier_field option:selected").text();
                var remark = $("#accessory_remark_field").val();

                return {
                    // id: id,
                    accessory_id: accessory_id,
                    accessory_text: accessory_text,
                    accessory_class: accessory_class,
                    amount: amount,
                    price: price,
                    supplier_id: supplier_id,
                    supplier_text: supplier_text,
                    remark: remark
                };
            },
            validateDataObject: function(install_equipment) {
                if (!install_equipment.accessory_id) {
                    return {'status': false, 'message': "{{ __('lang.required_field_inform') }}" };
                } 
                if (!install_equipment.price || install_equipment.price <= 0) {
                    return {'status': false, 'message': "{{ __('install_equipments.price_invalid') }}" };
                }

                if (!install_equipment.amount || install_equipment.amount <= 0) {
                    return {'status': false, 'message': "{{ __('install_equipments.amount_invalid') }}" };
                }

                if (!install_equipment.supplier_id) {
                    return {'status': false, 'message': "{{ __('install_equipments.required_supplier') }}" };
                }
                return {'status': true};
            },
            saveAdd: function() {
                var install_equipment = this.getDataFromModalAdd();
                var validate_result = this.validateDataObject(install_equipment);
                if (!validate_result.status) {
                    return warningAlert(validate_result.message);
                }
                install_equipment.id = null;
                this.install_equipment_line_list.push(install_equipment);
                this.edit_index = null;
                this.display();
                this.hideModal();
            },
            saveEdit: function(index) {
                var total = 0;
                var dealer_price_list = [];
                var accessory_id = $("#accessory_field").val();
                var accessory_text = $("#accessory_field option:selected").text();
                // var accessory_code = $("#accessory_code_field option:selected").text();
                var accessory_class = $("#accessory_class_field").val();
                var amount = $("#accessory_amount_field").val();
                var price = $("#accessory_price_field").val();
                var supplier_id = $("#accessory_supplier_field").val();
                var supplier_text = $("#accessory_supplier_field option:selected").text();
                var remark = $("#accessory_remark_field").val();
                var install_equipment = this.install_equipment_line_list[index];

                install_equipment.id = install_equipment.id;
                install_equipment.accessory_id = accessory_id;
                install_equipment.accessory_text = accessory_text;
                install_equipment.accessory_class = accessory_class;
                install_equipment.amount = amount;
                install_equipment.price = price;
                install_equipment.supplier_id = supplier_id;
                install_equipment.supplier_text = supplier_text;
                install_equipment.remark = remark;
                var validate_result = this.validateDataObject(install_equipment);
                if (!validate_result.status) {
                    return warningAlert(validate_result.message);
                }

                addInstallEquipmentVue.$set(this.install_equipment_line_list, index, install_equipment);
                this.edit_index = null;
                this.display();
                this.hideModal();
            },
            remove: function(index) {
                if (this.install_equipment_line_list[index] && this.install_equipment_line_list[index].id) {
                    this.pending_delete_install_equipment_ids.push(this.install_equipment_line_list[index].id);
                }
                this.install_equipment_line_list.splice(index, 1);
            },
            setIndex: function(index) {
                this.edit_index = index;
            },
            getIndex: function() {
                return this.edit_index;
            },
            setLastIndex: function() {
                return this.install_equipment_line_list.length;
            },
            getNumberWithCommas(x) {
                return numberWithCommas(x);
            },
            truncateString: function(string, limit) {
                return string.substring(0, limit) + '...';
            },
            removeBOMAccessory: function() {
                this.bom_list = [];
            },
            addBOMAccessories: function(bom_accessories) {
                this.bom_list = bom_accessories;
            },
            importBOMAccessories: function() {
                this.install_equipment_line_list = this.install_equipment_line_list.concat(this.bom_list);
                $("#modal-bom").modal("hide");
            },
            addAccessorylist: function(list) {
                this.install_equipment_line_list = list;
            }

        },
        props: ['title'],
    });
    addInstallEquipmentVue.display();
    window.addInstallEquipmentVue = addInstallEquipmentVue;

    function openAccessoryModal() {
        addInstallEquipmentVue.setIndex();
        $("#install-equipment-modal-label").html('เพิ่มอุปกรณ์เสริม');
        $("#install-equipment-modal").modal("show");
    }

    function openBOMModal() {
        $("#modal-bom").modal("show");
    }

    function deleteInstallEquipment() {
        addInstallEquipmentVue.remove();
    }

    function addInstallEquipment() {
        addInstallEquipmentVue.addInstallEquipment();
    }

    function saveInstallEquipment() {
        addInstallEquipmentVue.save();
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
    }

    $('#modal-bom').on('hidden.bs.modal', function (e) {
        addInstallEquipmentVue.removeBOMAccessory();
        $('#bom_id').val(null).trigger('change');
    })

    $("#bom_id").on('select2:select', function(e) {
        var data = e.params.data;
        axios.get("{{ route('admin.install-equipments.bom-accessories') }}", {
            params: {
                bom_id: data.id
            }
        }).then(response => {
            if (response.data && response.data.length > 0) {
                var bom_accessories = response.data;
                addInstallEquipmentVue.removeBOMAccessory();
                addInstallEquipmentVue.addBOMAccessories(bom_accessories);
            }
        });
    });
</script>
@endpush