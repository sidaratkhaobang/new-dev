@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        let addCheckRepairVue = new Vue({
            el: '#check-repair',
            data: {
                check_repair_list: @if (isset($check_repair_list))
                    @json($check_repair_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_check_repair_ids: [],
                line_id: null,
            },
            methods: {
                display: function() {
                    $("#check-repair").show();
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
                    $("#check-repair-modal-label").html('แก้ไขข้อมูลการซ่อม');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#description_field").val('');
                    $("#check_field").val(null).change();
                    $("#qc_field").val('');
                    $("#line_id").val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.check_repair_list[index];
                    $("#check_field").val(temp.check).change();
                    $("#description_field").val(temp.description);
                    $("#qc_field").val(temp.qc);
                    $("#line_id").val(temp.id);
                    this.line_id = temp.id;
                },
                openModal: function() {
                    $("#modal-check-repair").modal("show");
                },
                hideModal: function() {
                    $("#modal-check-repair").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var check_repair = _this.getDataFromModal();
                    if (_this.validateObject(check_repair)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(check_repair, index);
                        } else {
                            _this.saveAdd(check_repair);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getDataFromModal: function() {
                    var check = document.getElementById("check_field").value;
                    var check_text = (check) ? document.getElementById('check_field')
                        .selectedOptions[0].text : '';
                    var description = document.getElementById("description_field").value;
                    var qc = document.getElementById("qc_field").value;
                    var id = document.getElementById("line_id").value;
                    if (this.mode == 'edit') {
                        var date = document.getElementById("date").value;
                    } else {
                        var date = new Date().toJSON().slice(0, 10);
                    }
                    return {
                        check: check,
                        check_text: check_text,
                        description: description,
                        qc: qc,
                        date: date,
                        id: id,
                    };
                },
                validateObject: function(check_repair) {
                    if (check_repair.description && check_repair.check) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(check_repair) {
                    this.check_repair_list.push(check_repair);
                },
                saveEdit: function(check_repair, index) {
                    addCheckRepairVue.$set(this.check_repair_list, index, check_repair);
                },
                removeData: function(index) {
                    if (this.check_repair_list[index].id) {
                        this.pending_check_repair_ids.push(this.check_repair_list[index].id);
                    }
                    this.check_repair_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.check_repair_list.length;
                },
                formatDate(x) {
                    if (x) {
                        return moment(x).format('DD/MM/YYYY');
                    }
                },
            },
            props: ['title'],
        });
        addCheckRepairVue.display();

        function addData() {
            addCheckRepairVue.addData();
        }

        function saveData() {
            addCheckRepairVue.save();
        }
    </script>
@endpush
