@push('scripts')
    <script>
        let addRepairVue = new Vue({
            el: '#repair-vue',
            data: {
                repair_list: @if (isset($claim_list_data))
                    @json($claim_list_data)
                @else
                    []
                @endif ,
                // accident_inform: @if (isset($accident_inform))
                //     @json($accident_inform)
                // @else
                //     []
                // @endif ,
                edit_index: null,
                mode: null,
                pending_delete_before_ids: [],
                pending_delete_after_ids: [],
                transfer_type: 2,
                selectedImageUrl: '',
                pending_delete_claim_ids: [],
                before_files_delete: [],
                after_files_delete: [],
                repair: [{
                    wound_characteristics_id: null,
                }],
            },

            methods: {
                display: function() {

                    $("#repair-vue").show();
                },



                addRepair: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },

                editRepair: function(index) {

                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#product-transport-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $('input[name="supplier"][value="1"]').prop("checked", true)
                        .change();
                    $("#tls_cost_modal").val('');
                    $("#accident_claim_id").val('').change();
                    $("#wound_characteristics").val('').change();
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[1].removeAllFiles(true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.repair_list[index];
                    $("#accident_claim_id").val(temp.accident_claim_id).change();
                    $('input[name="supplier"][value="' + temp.supplier + '"]').prop("checked", true)
                        .change();
                    $("#tls_cost_modal").val(temp.tls_cost);
                    $("#wound_characteristics").val(temp.wound_characteristics_id).change();
                    $("#is_withdraw_true_0").prop("checked", (temp.is_withdraw_true === true || temp
                        .is_withdraw_true === 1)).val(temp
                        .is_withdraw_true).change();

                    if (temp.is_withdraw_true === true || temp.is_withdraw_true === 1) {
                        $("#tls_cost_modal_label").show();
                    } else {
                        $("#tls_cost_modal").val('');
                        $("#tls_cost_modal_label").hide();
                    }

                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];
                    window.myDropzone[0].options.params.pending_delete_ids = [];
                    var before_files = temp.before_files;
                    if (before_files != undefined) {
                        if (before_files.length > 0) {
                            window.myDropzone[0].emit("addedfile", before_files[0]);
                            window.myDropzone[0].emit("thumbnail", before_files[0], before_files[0]
                                .url_thumb);
                            window.myDropzone[0].files.push(before_files[0]);
                        }
                    }
                    window.myDropzone[1].removeAllFiles(true);
                    window.myDropzone[1].options.params.js_delete_files = [];
                    window.myDropzone[1].options.params.pending_delete_ids = [];
                    var after_files = temp.after_files;
                    if (after_files != undefined) {
                        if (after_files.length > 0) {
                            window.myDropzone[1].emit("addedfile", after_files[0]);
                            window.myDropzone[1].emit("thumbnail", after_files[0], after_files[0]
                                .url_thumb);
                            window.myDropzone[1].files.push(after_files[0]);
                        }
                    }
                },
                openModalImage(event) {
                    this.selectedImageUrl = event;
                    $('#imageModal').modal('show');
                },
                openModal: function() {
                    $("#modal-repair").modal("show");
                },
                hideModal: function() {
                    $("#modal-repair").modal("hide");
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
                    var tls_cost_modal = document.getElementById("tls_cost_modal").value;
                    var supplier = document.querySelector('input[name="supplier"]:checked').value;
                    var is_withdraw_true = document.getElementById("is_withdraw_true_0").checked;
                    var accident_claim_id = document.getElementById("accident_claim_id").value;
                    var accident_claim_text = (accident_claim_id) ? document.getElementById('accident_claim_id')
                        .selectedOptions[0].text : '';
                    var supplier_id = document.querySelector('input[name="supplier"]:checked').value;
                    var supplier_text = (supplier_id) ? document.querySelector(
                        'input[name="supplier"]:checked + label').textContent : '';
                    var wound_characteristics_id = document.getElementById("wound_characteristics").value;
                    var wound_characteristics_text = (accident_claim_id) ? document.getElementById(
                            'wound_characteristics')
                        .selectedOptions[0].text : '';
                    var before_image_raw_files = window.myDropzone[0].files;
                    var before_files = before_image_raw_files.map(item => this.formatFile(item));
                    var after_image_raw_files = window.myDropzone[1].files;
                    var after_files = after_image_raw_files.map(item => this.formatFile(item));
                    var id = null;
                    return {
                        id: id,
                        supplier: supplier,
                        tls_cost: tls_cost_modal,

                        supplier_text: supplier_text,
                        supplier_id: supplier_id,
                        wound_characteristics_id,
                        wound_characteristics_text,
                        accident_claim_id: accident_claim_id,
                        accident_claim_text: accident_claim_text,
                        is_withdraw_true: is_withdraw_true,
                        before_files: before_files,
                        pending_delete_before_files: [],
                        after_files: after_files,
                        pending_delete_after_files: [],
                    };
                },
                validateDataObject: function(repair) {
                    if (repair.supplier && repair.accident_claim_id && repair.wound_characteristics_id && repair
                        .before_files.length > 0) {
                        if (repair.is_withdraw_true == true) {
                            if (repair.tls_cost) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return true;
                        }
                    } else {
                        return false;
                    }
                },
                saveAdd: function() {
                    var repair = this.getDataFromModalAdd();
                    if (this.validateDataObject(repair)) {
                        this.repair_list.push(repair);
                        var count_withdraw = 0;
                        let cost_total = 0;
                        this.repair_list.forEach(function(repair_data) {
                            if (repair_data.is_withdraw_true == true) {
                                count_withdraw += 1;
                                cost_total += parseFloat(repair_data.tls_cost && typeof repair_data
                                    .tls_cost === 'string' ? repair_data.tls_cost.replace(/,/g,
                                        '') : 0);
                            }
                        });
                        if (count_withdraw > 0) {
                            $("#tls_cost").val(cost_total.toLocaleString());
                            $("#tls_cost_label").show();
                        } else {
                            $("#tls_cost_label").hide();
                        }
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var repair = this.repair_list[index];
                    var tls_cost_modal = document.getElementById("tls_cost_modal").value;
                    var supplier = document.querySelector('input[name="supplier"]:checked').value;
                    var is_withdraw_true = document.getElementById("is_withdraw_true_0").checked;
                    var accident_claim_id = document.getElementById("accident_claim_id").value;
                    var accident_claim_text = (accident_claim_id) ? document.getElementById('accident_claim_id')
                        .selectedOptions[0].text : '';
                    var supplier_id = document.querySelector('input[name="supplier"]:checked').value;
                    var supplier_text = (supplier_id) ? document.querySelector(
                        'input[name="supplier"]:checked + label').textContent : '';
                    var wound_characteristics_id = document.getElementById("wound_characteristics").value;
                    var wound_characteristics_text = (accident_claim_id) ? document.getElementById(
                            'wound_characteristics')
                        .selectedOptions[0].text : '';
                    var before_image_raw_files = window.myDropzone[0].files;
                    var before_files = before_image_raw_files.map(item => this.formatFile(item));
                    var after_image_raw_files = window.myDropzone[1].files;
                    var after_files = after_image_raw_files.map(item => this.formatFile(item));

                    // get all deleted files
                    var deleted_before_image_files = window.myDropzone[0].options.params.js_delete_files;
                    deleted_before_image_files = deleted_before_image_files.filter((file) => {
                        return (file.media_id);
                    });

                    var deleted_before_image_media_ids = deleted_before_image_files.map((file) => {
                        return file.media_id;
                    });

                    // get all deleted files
                    var deleted_after_image_files = window.myDropzone[1].options.params.js_delete_files;
                    deleted_after_image_files = deleted_after_image_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_after_image_media_ids = deleted_after_image_files.map((file) => {
                        return file.media_id;
                    });

                    repair.tls_cost = tls_cost_modal;
                    repair.supplier = supplier;
                    repair.is_withdraw_true = is_withdraw_true;
                    repair.accident_claim_id = accident_claim_id;
                    repair.accident_claim_text = accident_claim_text;
                    repair.supplier_id = supplier_id;
                    repair.supplier_text = supplier_text;
                    repair.wound_characteristics_id = wound_characteristics_id;
                    repair.wound_characteristics_text = wound_characteristics_text;
                    repair.before_files = before_files;
                    // repair.pending_delete_before_files = deleted_before_image_media_ids;
                    repair.after_files = after_files;
                    // repair.pending_delete_after_files = deleted_after_image_media_ids;

                    if (this.validateDataObject(repair)) {
                        addRepairVue.$set(this.repair_list, index, repair);
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }

                    var count_withdraw = 0;
                    let cost_total = 0;
                    this.repair_list.forEach(function(repair_data) {
                        if (repair_data.is_withdraw_true == true) {
                            count_withdraw += 1;
                            cost_total += parseFloat(repair_data.tls_cost && typeof repair_data
                                .tls_cost === 'string' ? repair_data.tls_cost.replace(/,/g,
                                    '') : 0);
                        }
                    });

                    if (count_withdraw > 0) {
                        $("#tls_cost").val(cost_total.toLocaleString());
                        $("#tls_cost_label").show();
                    } else {
                        $("#tls_cost_label").hide();
                    }

                    if (window.myDropzone[0].options.params.pending_delete_ids.length > 0) {
                        this.before_files_delete.push(window.myDropzone[0].options.params.pending_delete_ids);

                    }
                    if (window.myDropzone[1].options.params.pending_delete_ids.length > 0) {
                        this.after_files_delete.push(window.myDropzone[1].options.params.pending_delete_ids);

                    }

                },
                removeRepair: function(index) {
                    if (this.repair_list[index].id) {
                        this.pending_delete_claim_ids.push(this.repair_list[index].id);
                    }
                    this.repair_list.splice(index, 1);
                    var count_withdraw = 0;
                    let cost_total = 0;
                    this.repair_list.forEach(function(repair_data) {
                        if (repair_data.is_withdraw_true == true) {
                            count_withdraw += 1;
                            cost_total += parseFloat(repair_data.tls_cost && typeof repair_data
                                .tls_cost === 'string' ? repair_data.tls_cost.replace(/,/g, '') : 0);
                        }
                    });

                    if (count_withdraw > 0) {
                        $("#tls_cost").val(cost_total.toLocaleString());
                        $("#tls_cost_label").show();
                    } else {
                        $("#tls_cost_label").hide();
                    }
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.repair_list.length;
                },
                formatFile: function(file) {
                    if (file.formated) {
                        return file;
                    }
                    return {
                        media_id: null,
                        url: file.dataURL,
                        url_thumb: file.dataURL,
                        file_name: file.name,
                        name: file.name,
                        size: file.size,
                        raw_file: file,
                        saved: false, // check is save on server
                        formated: true
                    }
                },
                getFiles: function() {
                    return this.repair_list.map(function(repair, index) {
                        return {
                            repair: repair,
                            before_files: repair.before_files,
                            after_files: repair.after_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.repair_list.map(function(repair, index) {
                        return {
                            repair: repair,
                            pending_delete_before_files: repair.pending_delete_before_files,
                            pending_delete_after_files: repair.pending_delete_after_files,
                            index: index
                        }
                    });
                },
                getFilesPendingCount: function(files) {
                    return (files ? files.filter((file) => {
                        return (!file.saved)
                    }).length : '---');
                },
            },
            props: ['title'],
        });
        addRepairVue.display();
        window.addRepairVue = addRepairVue;

        function addRepair() {
            addRepairVue.addRepair();
        }

        function saveRepair() {
            addRepairVue.save();
        }
    </script>
@endpush
