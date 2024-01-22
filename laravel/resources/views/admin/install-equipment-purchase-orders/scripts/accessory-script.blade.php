@push('scripts')
<script>
    $("#accessory_field").prop('disabled', true);
    $("#accessory_amount_field").prop('disabled', true);
    const mode = @if (isset($mode)) @json($mode) @else null @endif;

    let addInstallEquipmentPOLineVue = new Vue({
        el: '#install-equipment-pos',
        data: {
            install_equipment_po_line_list: @if (isset($install_equipment_po_line_list)) @json($install_equipment_po_line_list) @else [] @endif,
            edit_index: null,
            mode: null,
            pending_delete_install_equipment_po_line_ids: [],
            summary : @if (isset($summary)) 
                    @json($summary) 
                @else {
                    total: 0,
                    subtotal: 0, 
                    amount: 0 
                }@endif
        },
        watch: {
            install_equipment_po_line_list: function(_install_equipment_po_line_list) {
                var sum_amount = 0;
                var sum_overall_total = 0;
                var sum_overall_subtotal = 0;
                var sum_overall_discount = 0;
                _install_equipment_po_line_list.forEach(function(item) {
                    sum_overall_total += parseFloat(item.overall_total);
                    sum_overall_subtotal += parseFloat(item.overall_subtotal);
                    sum_overall_discount += parseFloat(item.discount);
                    sum_amount += parseInt(item.amount);
                })
                this.summary.total = parseFloat(sum_overall_total).toFixed(2);
                this.summary.discount = parseFloat(sum_overall_discount).toFixed(2);
                this.summary.total_after_discount = parseFloat(sum_overall_total).toFixed(2);
                this.summary.subtotal = parseFloat(sum_overall_subtotal).toFixed(2);
                this.summary.amount = parseInt(sum_amount);
            }
        },
        methods: {
            display: function() {
                $("#install-equipment-po-lines").show();
            },
            addInstallEquipmentPOLine: function() {
                this.setIndex(this.setLastIndex());
                this.clearModalData();
                this.mode = 'add';
                this.openModal();
            },
            edit: function(index) {
                this.setIndex(index);
                this.loadModalData(index);
                this.mode = 'edit';
                $("#install-equipment-po-line-modal-label").html('แก้ไขข้อมูลอุปกรณ์ที่สั่งซื้อ');
                this.openModal();
            },
            clearModalData: function() {
                $("#accessory_field").val(null).change();
                $("#accessory_class_field").val(null).change();
                $("#accessory_amount_field").val('');
                $("#accessory_price_field").val('');
                $("#accessory_discount_field").val('');
            },
            loadModalData: function(index) {
                var temp = null;
                temp = this.install_equipment_po_line_list[index];
                $("#accessory_class_field").val(temp.accessory_class);
                $("#accessory_amount_field").val(temp.amount);
                $("#accessory_price_field").val(temp.total);
                $("#accessory_discount_field").val(temp.discount);

                $("#accessory_field").val(temp.accessory_id).change();
                var tempAccessoryOption = new Option(temp.accessory_text, temp.accessory_id, true, true);
                $("#accessory_field").append(tempAccessoryOption).trigger('change');
            },
            openModal: function() {
                $("#install-equipment-po-line-modal").modal("show");
            },
            hideModal: function() {
                $("#install-equipment-po-line-modal").modal("hide");
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
                var accessory_class = $("#accessory_class_field").val();
                var amount_string = $("#accessory_amount_field").val();
                var total_string = $("#accessory_price_field").val();
                var discount_string = $("#accessory_discount_field").val();
                
                var amount = parseFloat(amount_string.replaceAll(',', ''));
                var total = parseFloat(total_string.replaceAll(',', ''));
                var discount = parseFloat(discount_string.replaceAll(',', ''));
                sum = this.calculate(total, amount, discount);
                return {
                    // id: id,
                    accessory_id: accessory_id,
                    accessory_text: accessory_text,
                    accessory_class: accessory_class,
                    amount: sum.amount,
                    total: sum.total,
                    vat: sum.vat,
                    subtotal: sum.subtotal,
                    discount: sum.discount,
                    overall_total: sum.overall_total,
                    overall_subtotal: sum.overall_subtotal,
                    overall_vat: sum.overall_vat,
                };
            },
            calculate: function(total = 0, amount = 0, discount = 0) {
                sum = {
                    total: total ? parseFloat(total).toFixed(2) : parseFloat(0).toFixed(2),
                    amount: amount ? parseInt(amount) : 0,
                    discount: discount ? parseFloat(discount).toFixed(2) : parseFloat(0).toFixed(2),
                };
                sum.vat = parseFloat(sum.total * 7 / 107).toFixed(2);
                sum.subtotal = parseFloat(sum.total - sum.vat).toFixed(2);
                sum.overall_subtotal = parseFloat(sum.subtotal * sum.amount).toFixed(2);
                sum.overall_vat = parseFloat(sum.vat * sum.amount).toFixed(2);
                sum.overall_total = parseFloat((sum.total * sum.amount) - sum.discount).toFixed(2);
                return sum;
            },
            validateDataObject: function(install_equipment_po_line) {

                if (!install_equipment_po_line.accessory_id) {
                    return {'status': false, 'message': "{{ __('lang.required_field_inform') }}" };
                } 
                if (!install_equipment_po_line.total || install_equipment_po_line.total <= 0) {
                    return {'status': false, 'message': "{{ __('install_equipments.price_invalid') }}" };
                }

                if (!install_equipment_po_line.amount || install_equipment_po_line.amount <= 0) {
                    return {'status': false, 'message': "{{ __('install_equipments.amount_invalid') }}" };
                }

                if (!install_equipment_po_line.overall_total || install_equipment_po_line.overall_total <= 0) {
                    return {'status': false, 'message': "{{ __('install_equipments.price_invalid') }}" };
                }
                return {'status': true};
            },
            saveAdd: function() {
                var install_equipment_po_line = this.getDataFromModalAdd();
                var validate_result = this.validateDataObject(install_equipment_po_line);
                if (!validate_result.status) {
                    return warningAlert(validate_result.message);
                }
                install_equipment_po_line.id = null;
                this.install_equipment_po_line_list.push(install_equipment_po_line);
                this.edit_index = null;
                this.display();
                this.hideModal();
            },
            saveEdit: function(index) {
                var total = 0;
                var accessory_id = $("#accessory_field").val();
                var accessory_text = $("#accessory_field option:selected").text();
                var accessory_class = $("#accessory_class_field").val();                
                var supplier_id = $("#accessory_supplier_field").val();
                var supplier_text = $("#accessory_supplier_field option:selected").text();
                var remark = $("#accessory_remark_field").val();
                
                var amount_string = $("#accessory_amount_field").val();
                var total_string = $("#accessory_price_field").val();
                var discount_string = $("#accessory_discount_field").val();
                
                var amount = parseFloat(amount_string.replaceAll(',', ''));
                var total = parseFloat(total_string.replaceAll(',', ''));
                var discount = parseFloat(discount_string.replaceAll(',', ''));
                let sum = this.calculate(total, amount, discount);

                var install_equipment_po_line = this.install_equipment_po_line_list[index];
                install_equipment_po_line.id = install_equipment_po_line.id;
                install_equipment_po_line.accessory_id = accessory_id;
                install_equipment_po_line.accessory_text = accessory_text;
                install_equipment_po_line.accessory_class = accessory_class;
                install_equipment_po_line.amount = sum.amount;
                install_equipment_po_line.total = sum.total;
                install_equipment_po_line.vat = sum.vat;
                install_equipment_po_line.subtotal = sum.subtotal;
                install_equipment_po_line.discount = sum.discount;
                install_equipment_po_line.overall_total = sum.overall_total;
                install_equipment_po_line.overall_subtotal = sum.overall_subtotal;
                install_equipment_po_line.overall_vat = sum.overall_vat;
                var validate_result = this.validateDataObject(install_equipment_po_line);
                if (!validate_result.status) {
                    return warningAlert(validate_result.message);
                }

                addInstallEquipmentPOLineVue.$set(this.install_equipment_po_line_list, index, install_equipment_po_line);
                this.edit_index = null;
                this.display();
                this.hideModal();
            },
            remove: function(index) {
                if (this.install_equipment_po_line_list[index] && this.install_equipment_po_line_list[index].id) {
                    this.pending_delete_install_equipment_po_line_ids.push(this.install_equipment_po_line_list[index].id);
                }
                this.install_equipment_po_line_list.splice(index, 1);
            },
            setIndex: function(index) {
                this.edit_index = index;
            },
            getIndex: function() {
                return this.edit_index;
            },
            setLastIndex: function() {
                return this.install_equipment_po_line_list.length;
            },
            getNumberWithCommas(x) {
                return numberWithCommas(x);
            },
            truncateString: function(string, limit) {
                return string.substring(0, limit) + '...';
            }
        },
        props: ['title'],
    });
    addInstallEquipmentPOLineVue.display();
    window.addInstallEquipmentPOLineVue = addInstallEquipmentPOLineVue;

    function openAccessoryModal() {
        addInstallEquipmentPOLineVue.setIndex();
        $("#install-equipment-po-line-modal-label").html('เพิ่มอุปกรณ์ที่สั่งซื้อ');
        $("#install-equipment-po-line-modal").modal("show");
    }

    function deleteInstallEquipment() {
        addInstallEquipmentPOLineVue.remove();
    }

    function addInstallEquipmentPOLine() {
        addInstallEquipmentPOLineVue.addInstallEquipmentPOLine();
    }

    function saveInstallEquipmentPOLIne() {
        addInstallEquipmentPOLineVue.save();
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
@endpush