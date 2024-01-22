@push('scripts')
    <script>
        let addAccidentRepairOpenVue = new Vue({
            el: '#accident-repair-open-vue',
            data: {
                accident_list: @if (isset($accident_list))
                    @json($accident_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                selectedImageUrl: '',
                accident_open_list: [],
                pending_delete_cost_ids: [],
                accident_list_selected: [],
                accident_list_unselected: [],
                test: [1, 2, 3],
                RepairClaimEnum: {
                    HARD_BUMP: 'HARD_BUMP',
                    SOFT_BUMP: 'SOFT_BUMP',
                    TTL: 'TTL',
                },
            },
            methods: {
                display: function() {
                    $("#cost-vue").show();
                },
                addToRight: function() {
                    accident_list_select = this.accident_list_unselected.filter(item => {
                        return item.is_check == true;
                    });
                    this.accident_list_selected = this.accident_list_selected.concat(accident_list_select);

                    this.accident_list_selected.forEach(item => {
                        item.is_check = false;
                    });

                    // remove from left
                    this.accident_list_unselected = this.accident_list_unselected.filter(item => {
                        return !this.accident_list_selected.some(element => element.id == item.id);
                    });
                    $('#report_id').prop('disabled', true);
                    $('#selectAll').prop('checked', false);
                },

                addToLeft: function() {
                    delete_list = this.accident_list_selected.filter(item => {
                        return item.is_check == true;
                    });

                    filter_delete_list = delete_list.filter(item => {
                        return this.accident_list.some(element => element.id == item.id);
                    });

                    this.accident_list_unselected = this.accident_list_unselected.concat(filter_delete_list);

                    this.accident_list_unselected.forEach(item => {
                        item.is_check = false;
                        item.garage = {};
                        item.send_repair_date = null;
                        item.due_date = null;
                    });

                    // remove from right
                    this.accident_list_selected = this.accident_list_selected.filter(item => {
                        return !delete_list.some(element => element.id == item.id);
                    });

                    $('#selectAll2').prop('checked', false);
                },
                leftFilteredRows: function() {
                    return this.procedure_search_list;

                },

                addOrder: function() {
                    $('#report_id').prop('disabled', false);
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    $("#repair-accident-modal-label").html('เพิ่มใบสั่งซ่อม');
                    this.openModal();
                },
                editRepairOpen: function(index) {

                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#repair-accident-modal-label").html('แก้ไขใบสั่งซ่อม');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#report_id").val('').change();
                    $("#send_repair_date").val('').change();
                    $("#due_date").val('').change();
                    $("#garage_id").val('').change();
                    this.accident_list = [];
                    this.accident_list_unselected = [];
                    this.accident_list_selected = [];
                },
                loadModalData: function(index) {
                    this.accident_list_selected = [];
                    accident_list_selected_index = this.accident_open_list[index];
                    this.accident_list_selected = this.accident_list_selected.concat(
                        accident_list_selected_index.line);
                    $("#report_id").val(accident_list_selected_index.report_id).change();
                    $("#send_repair_date").val(accident_list_selected_index.line[0].send_repair_date).change();
                    $("#due_date").val(accident_list_selected_index.line[0].due_date).change();
                    $("#garage_id").val(accident_list_selected_index.line[0].garage.id).change();
                },
                openModal: function() {
                    $("#modal-repair-accident").modal("show");
                },
                hideModal: function() {
                    $("#modal-repair-accident").modal("hide");
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
                    //
                },
                validateDataObject: function(accident_list_selected) {
                    var validate_count = 0;
                    accident_list_selected.forEach(element => {
                        if (typeof element.garage !== 'undefined' && typeof element.due_date !==
                            'undefined' && element.due_date !=
                            "" && typeof element.send_repair_date !== 'undefined') {
                            //
                        } else {
                            validate_count = parseInt(validate_count) + 1;
                        }
                    });
                    if (parseInt(validate_count) > 0) {
                        return false;
                    } else {
                        return true;
                    }

                },
                saveAdd: async function() {


                    if (this.validateDataObject(this.accident_list_selected) && this.accident_list_selected
                        .length > 0) {
                        var report_id = document.getElementById("report_id").value;
                        var send_repair_date = document.getElementById("send_repair_date").value;
                        var due_date = document.getElementById("due_date").value;
                        var garage_id = document.getElementById("garage_id").value;

                        accident_open = {};
                        accident_open.line = this.accident_list_selected;
                        accident_open.report_id = report_id;
                        accident_open.send_repair_date = send_repair_date;
                        accident_open.due_date = due_date;
                        accident_open.garage_id = garage_id;

                        await axios.get("{{ route('admin.accident-orders.data-car-accident') }}", {
                            params: {
                                report_id: report_id,
                            }
                        }).then(response => {

                            if (response.data.accident_data) {
                                accident_open.case = response.data.accident_data.case;
                                accident_open.claim_no = response.data.accident_data.claim_no;
                                accident_open.accident_date = response.data.accident_data.accident_date;
                                accident_open.worksheet_no = response.data.accident_data.worksheet_no;
                                accident_open.accident_description = response.data.accident_data
                                    .accident_description;
                                accident_open.repair_type = response.data.accident_data.repair_type;
                                accident_open.count_accident_line = this.accident_list_selected.length;
                            }


                        });

                        this.accident_open_list.push(accident_open);
                        this.display();
                        this.hideModal();

                    } else {
                        return warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: async function(index) {
                    if (this.accident_list_selected.length > 0) {
                        if (this.validateDataObject(this.accident_list_selected)) {
                            var report_id = document.getElementById("report_id").value;
                            var send_repair_date = document.getElementById("send_repair_date").value;
                            var due_date = document.getElementById("due_date").value;
                            var garage_id = document.getElementById("garage_id").value;

                            accident_open = {};
                            accident_open.line = this.accident_list_selected;
                            accident_open.report_id = report_id;
                            accident_open.send_repair_date = send_repair_date;
                            accident_open.due_date = due_date;
                            accident_open.garage_id = garage_id;

                            await axios.get("{{ route('admin.accident-orders.data-car-accident') }}", {
                                params: {
                                    report_id: report_id,
                                }
                            }).then(response => {

                                if (response.data.accident_data) {
                                    accident_open.case = response.data.accident_data.case;
                                    accident_open.claim_no = response.data.accident_data.claim_no;
                                    accident_open.accident_date = response.data.accident_data
                                        .accident_date;
                                    accident_open.worksheet_no = response.data.accident_data
                                        .worksheet_no;
                                    accident_open.accident_description = response.data.accident_data
                                        .accident_description;
                                    accident_open.repair_type = response.data.accident_data.repair_type;
                                    accident_open.count_accident_line = this.accident_list_selected
                                        .length;
                                }

                            });

                            addAccidentRepairOpenVue.$set(this.accident_open_list, index, accident_open);

                            this.display();
                            this.hideModal();
                        } else {
                            return warningAlert("{{ __('lang.required_field_inform') }}");
                        }
                    } else {
                        this.accident_open_list.splice(index, 1);
                        this.display();
                        this.hideModal();

                    }
                },
                openModalImage(event) {
                    this.selectedImageUrl = event;
                    $('#imageModalOpen').modal('show');
                },

                removeCost: function(index) {
                    if (this.accident_list[index].id) {
                        this.pending_delete_cost_ids.push(this.accident_list[index].id);
                    }
                    this.accident_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.accident_list.length;
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


                addAccidentList(data) {
                    this.accident_list = data;
                    this.accident_list = this.accident_list.filter(item => {
                        return !this.accident_list_selected.some(element => element.id == item.id);
                    });


                    this.accident_list_unselected = this.accident_list.filter(item => {
                        accident_check = [];
                        this.accident_open_list.forEach(item => {
                            item.line.forEach(item2 => {
                                accident_check.push(item2);
                            });
                        });
                        return !accident_check.some(element => element.id == item.id);
                    });

                },

                useAll() {
                    var garage = {};
                    garage.text = $("#garage_id option:selected").text();
                    garage.id = $("#garage_id option:selected").val();

                    var send_repair_date = $("#send_repair_date").val();
                    var due_date = $("#due_date").val();
                    var due_date_int = parseInt(due_date.replace(/,/g, ''));
                    
                    if (garage.id != undefined && send_repair_date && due_date && due_date_int >= 0) {
                        accident_list_selected = this.accident_list_selected;
                        accident_list_selected.forEach(item => {
                            Vue.set(item, 'garage', garage);
                            Vue.set(item, 'send_repair_date', send_repair_date);
                            Vue.set(item, 'due_date', due_date);

                        });
                    } else {
                        return warningAlert("{{ __('lang.required_field_inform') }}");
                    }

                    return this.accident_list_selected;
                },
                print_text(text) {
                    // 
                },
                getDataAll() {
                    // 
                    return this.accident_open_list;
                },
                selectAllLeft(is_check) {
                    this.accident_list_unselected.forEach(item => {
                        item.is_check = is_check;
                    });
                },

                selectAllRight(is_check) {
                    this.accident_list_selected.forEach(item => {
                        item.is_check = is_check;
                    });
                },


            },
            props: ['title'],
        });
        addAccidentRepairOpenVue.display();
        window.addAccidentRepairOpenVue = addAccidentRepairOpenVue;

        function useAll() {
            addAccidentRepairOpenVue.useAll();
        }

        function addOrder() {
            addAccidentRepairOpenVue.addOrder();
        }

        function save() {
            addAccidentRepairOpenVue.save();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
