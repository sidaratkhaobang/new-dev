@push('scripts')
    <script>
        let addAccidentCostVue = new Vue({
            el: '#cost-vue',
            data: {
                cost_list: @if (isset($cost_list))
                    @json($cost_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_cost_ids: [],
            },
            methods: {
                display: function() {
                    $("#cost-vue").show();
                },
                addCost: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editCost: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#cost-modal-label").html('แก้ไขค่าใช้จ่าย');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#cost_name").val('').change();
                    $("#cost_price").val('').change();
                    $("#cost_remark").val('').change();
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.cost_list[index];
                    // console.log(temp)
                    $("#cost_name").val(temp.cost_name).change();
                    $("#cost_price").val(temp.cost_price).change();
                    $("#cost_remark").val(temp.cost_remark).change();
                },
                openModal: function() {
                    $("#modal-cost").modal("show");
                },
                hideModal: function() {
                    $("#modal-cost").modal("hide");
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
                    var cost_name = document.getElementById("cost_name").value;
                    var cost_price = document.getElementById("cost_price").value;
                    var cost_remark = document.getElementById("cost_remark").value;
                    var id = null;
                    var current_date = new Date();
                    var year = current_date.getFullYear();
                    var month = ('0' + (current_date.getMonth() + 1)).slice(-2);
                    var day = ('0' + current_date.getDate()).slice(-2);
                    var hours = ('0' + current_date.getHours()).slice(-2);
                    var minutes = ('0' + current_date.getMinutes()).slice(-2);
                    var cost_date = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
                    return {
                        id: id,
                        cost_name: cost_name,
                        cost_price: parseFloat(cost_price.replace(/,/g, '')).toFixed(2),
                        cost_remark: cost_remark,
                        cost_date: cost_date,
                    };
                },
                validateDataObject: function(cost) {
                    if (cost.cost_name && cost.cost_price && !isNaN(cost.cost_price)) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function() {
                    var cost = this.getDataFromModalAdd();
                    if (this.validateDataObject(cost)) {
                        this.cost_list.push(cost);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var cost_name = document.getElementById("cost_name").value;
                    var cost_price = document.getElementById("cost_price").value;
                    var cost_remark = document.getElementById("cost_remark").value;
                    var cost = this.cost_list[index];

                    cost['cost_name'] = cost_name;
                    cost['cost_price'] = parseFloat(cost_price.replace(/,/g, '')).toFixed(2);;
                    cost['cost_remark'] = cost_remark;
                    if (this.validateDataObject(cost)) {
                        addAccidentCostVue.$set(this.cost_list, index, cost);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },

                removeCost: function(index) {
                    if (this.cost_list[index].id) {
                        this.pending_delete_cost_ids.push(this.cost_list[index].id);
                    }
                    this.cost_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.cost_list.length;
                },

                format_date: function(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-SG', options);
                    return formattedDate;
                },

                getNumberWithCommas(x) {
                    return numberWithCommas(x);
                },

            },
            props: ['title'],
        });
        addAccidentCostVue.display();
        window.addAccidentCostVue = addAccidentCostVue;

        function addCost() {
            addAccidentCostVue.addCost();
        }

        function saveCost() {
            addAccidentCostVue.save();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
