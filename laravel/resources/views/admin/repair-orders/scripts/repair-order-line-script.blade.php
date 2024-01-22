@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        let addRepairOrderLineVue = new Vue({
            el: '#repair-order-line',
            data: {
                repair_order_line_list: @if (isset($repair_order_line_list))
                    @json($repair_order_line_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_repair_order_line_ids: [],
                repair_list_ids: [],
                repair_lists: @if (isset($repair_lists))
                    @json($repair_lists)
                @else
                    []
                @endif ,
                master: '{{ RepairEnum::MASTER }}',
                add_on: '{{ RepairEnum::ADD_ON }}',
                total_amount: 0,
                total_discount: 0,
                total_sum: 0,
            },
            methods: {
                display: function() {
                    this.setTotal();
                    $("#repair-order-line").show();
                },
                addData: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editData: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#repair-order-line-modal-label").html('แก้ไขรายการตรวจเช็ก');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#code_name_field").val(null).change();
                    $("#amount_field").val('1');
                    $("#check_field").val(null).change();
                    $("#price_field").val('').prop('disabled', true);
                    $("#total_field").val('').prop('disabled', true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.repair_order_line_list[index];
                    $("#code_name_field").val(temp.repair_list_id);
                    $("#check_field").val(temp.check);
                    $("#amount_field").val(temp.amount);
                    $("#price_field").val(temp.price);
                    $("#total_field").val(temp.total);
                    $("#id").val(temp.id);
                },
                openModal: function() {
                    $("#modal-repair-order-line").modal("show");
                },
                hideModal: function() {
                    $("#modal-repair-order-line").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var repair_order_line = _this.getDataFromModal();
                    if (_this.validateObject(repair_order_line)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(repair_order_line, index);
                        } else {
                            _this.saveAdd(repair_order_line);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getDataFromModal: function() {
                    var repair_list_id = document.getElementById("code_name_field").value;
                    var code_name = (repair_list_id) ? document.getElementById('code_name_field')
                        .selectedOptions[0].text : '';
                    var check = document.getElementById("check_field").value;
                    var check_text = (check) ? document.getElementById('check_field')
                        .selectedOptions[0].text : '';
                    var amount = document.getElementById("amount_field").value;
                    var price = document.getElementById("price_field").value;
                    var total = document.getElementById("total_field").value;
                    var discount = '0.00';
                    var vat = parseFloat(total * 7 / 107).toFixed(2);
                    var id = document.getElementById("id").value;
                    if (this.mode == 'edit') {
                        var date = document.getElementById("date").value;
                    } else {
                        var date = new Date().toJSON().slice(0, 10);
                    }

                    return {
                        repair_list_id: repair_list_id,
                        code_name: code_name,
                        check: check,
                        check_text: check_text,
                        amount: amount,
                        price: price,
                        total: total,
                        discount: discount,
                        vat: vat,
                        date: date,
                        repair_type: this.add_on,
                        add_item: true,
                        // id: id,
                    };
                },
                validateObject: function(repair_order_line) {
                    if (repair_order_line.repair_list_id && repair_order_line.check) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(repair_order_line) {
                    this.repair_order_line_list.push(repair_order_line);
                },
                saveEdit: function(repair_order_line, index) {
                    addRepairOrderLineVue.$set(this.repair_order_line_list, index, repair_order_line);
                },
                removeData: function(index) {
                    if (this.repair_order_line_list[index].id) {
                        this.pending_repair_order_line_ids.push(this.repair_order_line_list[index].id);
                    }
                    this.repair_order_line_list.splice(index, 1);
                },
                addDefault: function(e) {
                    var _this = this;
                    var repair_order_default = {};
                    var total = 0;
                    if (e.repair_list_id) {
                        repair_order_default.check = e.check;
                        repair_order_default.check_text = e.check_text;
                        repair_order_default.repair_list_id = e.repair_list_id;
                        repair_order_default.code_name = e.code_name;
                        repair_order_default.remark = e.remark;
                        repair_order_default.date = e.date;
                        repair_order_default.price = e.price;
                        repair_order_default.amount = 1;
                        repair_order_default.discount = '0.00';
                        total = parseFloat(e.price * repair_order_default.amount).toFixed(2);
                        repair_order_default.total = total;
                        repair_order_default.vat = parseFloat(total * 7 / 107).toFixed(2);
                        repair_order_default.repair_type = this.master;
                        this.repair_list_ids.push(e.repair_list_id);

                        _this.repair_order_line_list.push(repair_order_default);
                        $("#repair-order-line").show();
                        this.setTotal();
                    }
                },
                setPriceRepair: function(e) {
                    var self = this;
                    var amount = document.getElementById("amount_field").value;
                    $("#price_field").val(e).trigger("change");
                    $("#total_field").val(parseFloat(e * amount).toFixed(2)).trigger(
                        "change");
                },
                setTotalRepair: function(amount) {
                    var price = document.getElementById("price_field").value;
                    var total = 0;
                    total = parseFloat(price * amount).toFixed(2);
                    $("#total_field").val(total).trigger("change");
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                getDefaultId: function() {
                    return this.repair_list_ids;
                },
                setLastIndex: function() {
                    return this.repair_order_line_list.length;
                },
                formatDate(x) {
                    if (x) {
                        return moment(x).format('DD/MM/YYYY');
                    }
                },
                removeAll: function() {
                    this.repair_order_line_list = [];
                },
                numberWithCommas: function(x) {
                    return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
                },
                setTotal: function() {
                    total_amount = 0;
                    total_discount = 0;
                    total_sum = 0;
                    this.repair_order_line_list.forEach(element => {
                        total_amount += parseFloat(element.amount);
                        total_discount += parseFloat(element.discount);
                        total_sum += parseFloat(element.total);
                    });
                    this.total_amount = total_amount;
                    this.total_discount = total_discount.toFixed(2);
                    this.total_sum = total_sum.toFixed(2);
                },
            },
            props: ['title'],
        });
        addRepairOrderLineVue.display();

        function addData() {
            addRepairOrderLineVue.addData();
        }

        function saveData() {
            addRepairOrderLineVue.save();
        }

        function addDefault(e) {
            addRepairOrderLineVue.addDefault(e);
        }

        function setPriceRepair(e) {
            addRepairOrderLineVue.setPriceRepair(e);
        }

        function setTotalRepair(amount) {
            addRepairOrderLineVue.setTotalRepair(amount);
        }

        function removeData() {
            addRepairOrderLineVue.removeAll();
        }


        $("#code_name_field").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            dropdownParent: $("#modal-repair-order-line"),
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.repair-orders.select-repair') }}";
                },
                type: 'GET',
                data: function(params) {
                    parent_id = addRepairOrderLineVue.getDefaultId();
                    return {
                        parent_id: parent_id,
                        s: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            }
        });
        $("#code_name_field").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.repair-orders.price-repair') }}", {
                params: {
                    id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    setPriceRepair(response.data.price);
                }
            });
        });

        $("#amount_field").on("input", function() {
            amount = $(this).val();
            setTotalRepair(amount);
        });
    </script>
@endpush
