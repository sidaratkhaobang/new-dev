@push('scripts')
    <script>
        function saveForm(storeUri, formData, modalCallback) {
            showLoading();
            axios.post(storeUri, formData).then(response => {
                if (response.data.success) {
                    hideLoading();
                    @if (isset($save_callback))
                        if (typeof(modalCallback) == "function") {
                            modalCallback(response.data);
                        } else {
                            saveCallback(response.data);
                        }
                    @else
                        mySwal.fire({
                            title: "{{ __('lang.store_success_title') }}",
                            text: "{{ __('lang.store_success_message') }}",
                            icon: 'success',
                            confirmButtonText: "{{ __('lang.ok') }}"
                        }).then(value => {
                            if (response.data.redirect) {
                                if (response.data.redirect === 'false') {
                                    if (typeof(modalCallback) == "function") {
                                        modalCallback(response.data);
                                    }
                                } else {
                                    window.location.href = response.data.redirect;
                                }
                            } else {
                                window.location.reload();
                            }
                        });
                    @endif
                } else {
                    hideLoading();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                hideLoading();
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        }

        let addAccidentReplacementVue = new Vue({
            el: '#replacement-inform',
            data: {
                replacement_list: @if (isset($replacement_list))
                    @json($replacement_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_replacement_ids: [],
                btn_group_sheet: @if (isset($btn_group_sheet))
                    true
                @else
                    false
                @endif ,
            },
            methods: {
                display: function() {
                    $("#replacement-inform").show();
                },
                addReplacement: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editAccident: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#replacement-modal-label").html('แก้ไขงานรถหลัก/รถทดแทน');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#slide_driver").val('').change();
                    $("#lift_date").val('').change();
                    $("#lift_from").val('').change();
                    $("#lift_to").val('').change();
                    $("#lift_price").val('').change();

                    $("#replacement_type").val('').change();
                    $("#slide_worksheet").val('').change();
                    $("#place").val('').change();
                    // $("#lift_to").val(temp.lift_to).change();
                    $("#customer_receive0").prop("checked", true).change();
                    $("#replacement_pickup_date").val('').change();
                    // flatpickr("#replacement_pickup_date", temp.replacement_pickup_date);

                    window.myDropzone[0].removeAllFiles(true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.replacement_list[index];
                    $("#replacement_type").val(temp.replacement_type).change();
                    $("#slide_worksheet").val(temp.slide_worksheet).change();
                    $("#place").val(temp.place).change();
                    // $("#lift_to").val(temp.lift_to).change();
                    $("#replacement_pickup_date").val(temp.replacement_pickup_date).change();
                    flatpickr("#replacement_pickup_date", temp.replacement_pickup_date);

                    if ((temp.customer_receive === false || temp.customer_receive === 0)) {
                        $("#customer_receive0").prop("checked", true).change();
                    } else {
                        $("#customer_receive1").prop("checked", true).change();
                    }
                    $("#id").val(temp.id).change();

                    // var defaultDeiverSkillOption = {
                    //         id: temp.slide_driver,
                    //         text: temp.driving_skill_text,
                    // };
                    // var tempDriverSkillOption = new Option(defaultDeiverSkillOption.text, defaultDeiverSkillOption.id, false, false);
                    // $("#slide_driver").append(tempDriverSkillOption).trigger('change');
                    // clear file myDropzone
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];

                    // load file skill
                    var replacement_files = temp.replacement_files;
                    if (replacement_files.length > 0) {
                        replacement_files.forEach(item => {
                            let mockFile = {
                                ...item
                            };
                            window.myDropzone[0].emit("addedfile", mockFile);
                            window.myDropzone[0].emit("thumbnail", mockFile, item.url_thumb);
                            window.myDropzone[0].files.push(mockFile);
                        });
                    }
                },
                openModal: function() {
                    $("#modal-replacement").modal("show");
                },
                hideModal: function() {
                    $("#modal-replacement").modal("hide");
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
                    var replacement_type = document.getElementById("replacement_type").value;
                    var replacement_pickup_date = document.getElementById("replacement_pickup_date").value;
                    var customer_receive = document.querySelector('input[name="customer_receive"]:checked')
                        .value;
                    var slide_worksheet = document.getElementById("slide_worksheet").value;
                    var place = document.getElementById("place").value;
                    var skill_raw_files = window.myDropzone[0].files;
                    var replacement_files = skill_raw_files.map(item => this.formatFile(item));
                    var car_id = document.getElementById("car_id").value;
                    var accident_id = document.getElementById("accident_id").value;
                    var accident_order_id = document.getElementById("accident_order_id").value;
                    var id = null;
                    return {
                        id: id,
                        // driving_skill_id: driving_skill_id,
                        replacement_type: replacement_type,
                        replacement_pickup_date: replacement_pickup_date,
                        customer_receive: customer_receive,
                        slide_worksheet: slide_worksheet,
                        place: place,
                        accident_id: accident_id,
                        accident_order_id: accident_order_id,
                        car_id: car_id,
                        // lift_price: parseFloat(lift_price.replace(/,/g, '')).toFixed(2),
                        replacement_files: replacement_files,
                        pending_delete_replacement_files: [],
                    };
                },
                validateDataObject: function(replacement) {
                    // console.log(slide);
                    if (replacement.replacement_type && replacement.replacement_pickup_date && replacement.customer_receive && replacement.slide_worksheet && replacement
                        .place) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function() {
                    var replacement_sheet = this.getDataFromModalAdd();
                    if (this.validateDataObject(replacement_sheet)) {
                        this.replacement_list.push(replacement_sheet);
                            var storeUri = "{{ route('admin.accident-orders.save-replacment-accident') }}";
                        var formData = new FormData();
                        // console.log(formData);
                        formData.append('replacement_type', replacement_sheet.replacement_type);
                        formData.append('replacement_pickup_date', replacement_sheet.replacement_pickup_date);
                        formData.append('slide_worksheet', replacement_sheet.slide_worksheet);
                        formData.append('place', replacement_sheet.place);
                        formData.append('accident_id', replacement_sheet.accident_id);
                        formData.append('car_id', replacement_sheet.car_id);
                        formData.append('customer_receive', replacement_sheet.customer_receive);
                        formData.append('id', '');
                        formData.append('accident_order_id', replacement_sheet.accident_order_id);
                        // console.log(formData);
                        if (window.myDropzone) {
                            var dropzones = window.myDropzone;
                            dropzones.forEach((dropzone) => {
                                let dropzone_id = dropzone.options.params.elm_id;
                                let files = dropzone.getQueuedFiles();
                                files.forEach((file) => {
                                    formData.append(dropzone_id + '[]', file);
                                });
                                // delete data
                                let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                                if (pending_delete_ids.length > 0) {
                                    pending_delete_ids.forEach((id) => {
                                        formData.append(dropzone_id + '__pending_delete_ids[]',
                                            id);
                                    });
                                }

                                let pending_add_ids = dropzone.options.params.pending_add_ids;
                                if (pending_add_ids.length > 0) {
                                    pending_add_ids.forEach((id) => {
                                        formData.append(dropzone_id + '__pending_add_ids[]',
                                            id);
                                    });
                                }
                            });
                        }
                        saveForm(storeUri, formData);

                        // this.edit_index = null;
                        // this.display();
                        // this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var replacement = this.replacement_list[index];
                    // // load files in modal dropzone
                    // var modal_slide_files = window.myDropzone[0].files;
                    // var slide_files = modal_slide_files.map(item => this.formatFile(item));

                    // // get all deleted files
                    // var deleted_slide_files = window.myDropzone[0].options.params.js_delete_files;
                    // deleted_slide_files = deleted_slide_files.filter((file) => {
                    //     return (file.media_id);
                    // });
                    // var deleted_slide_media_ids = deleted_slide_files.map((file) => {
                    //     return file.media_id;
                    // });

                    var replacement_type = document.getElementById("replacement_type").value;
                    var replacement_pickup_date = document.getElementById("replacement_pickup_date").value;
                    var customer_receive = document.querySelector('input[name="customer_receive"]:checked')
                        .value;
                    var slide_worksheet = document.getElementById("slide_worksheet").value;
                    var place = document.getElementById("place").value;
                    var skill_raw_files = window.myDropzone[0].files;
                    var replacement_files = skill_raw_files.map(item => this.formatFile(item));
                    var car_id = document.getElementById("car_id").value;
                    var accident_id = document.getElementById("accident_id").value;
                    var id = document.getElementById("id").value;
                    var accident_order_id = document.getElementById("accident_order_id").value;

                    replacement['replacement_type'] = replacement_type;
                    replacement['replacement_pickup_date'] = replacement_pickup_date;
                    replacement['customer_receive'] = customer_receive;
                    replacement['slide_worksheet'] = slide_worksheet;
                    // replacement['lift_price'] = parseFloat(lift_price.replace(/,/g, '')).toFixed(2);
                    // driver_skill['driving_skill_text'] = driving_skill_text;
                    replacement['place'] = place;
                    replacement['skill_raw_files'] = skill_raw_files;
                    replacement['replacement_files'] = replacement_files;
                    replacement['car_id'] = car_id;
                    replacement['accident_id'] = accident_id;
                    replacement['id'] = id;
                    replacement['accident_order_id'] = accident_order_id;
                    // replacement['pending_delete_slide_files'] = deleted_slide_media_ids;
                    if (this.validateDataObject(replacement)) {
                        // addAccidentReplacementVue.$set(this.replacement_list, index, slide);
                        // this.edit_index = null;
                        // this.display();
                        // this.hideModal();
                        let storeUri = "{{ route('admin.accident-orders.save-replacment-accident') }}";
                        var formData = new FormData();
                        formData.append('replacement_type', replacement.replacement_type);
                        formData.append('replacement_pickup_date', replacement.replacement_pickup_date);
                        formData.append('slide_worksheet', replacement.slide_worksheet);
                        formData.append('place', replacement.place);
                        formData.append('accident_id', replacement.accident_id);
                        formData.append('car_id', replacement.car_id);
                        formData.append('customer_receive', replacement.customer_receive);
                        formData.append('id', replacement.id);
                        formData.append('accident_order_id', replacement.accident_order_id);
                        if (window.myDropzone) {
                            var dropzones = window.myDropzone;
                            dropzones.forEach((dropzone) => {
                                let dropzone_id = dropzone.options.params.elm_id;
                                let files = dropzone.getQueuedFiles();
                                files.forEach((file) => {
                                    formData.append(dropzone_id + '[]', file);
                                });
                                // delete data
                                let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                                if (pending_delete_ids.length > 0) {
                                    pending_delete_ids.forEach((id) => {
                                        formData.append(dropzone_id + '__pending_delete_ids[]',
                                            id);
                                    });
                                }

                                let pending_add_ids = dropzone.options.params.pending_add_ids;
                                if (pending_add_ids.length > 0) {
                                    pending_add_ids.forEach((id) => {
                                        formData.append(dropzone_id + '__pending_add_ids[]',
                                            id);
                                    });
                                }
                            });
                        }
                        saveForm(storeUri, formData);
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                removeAccident: function(index) {
                    if (this.replacement_list[index].id) {
                        this.pending_delete_replacement_ids.push(this.replacement_list[index].id);
                    }
                    this.replacement_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.replacement_list.length;
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
                    return this.replacement_list.map(function(slide, index) {
                        return {
                            // driver: driver,
                            slide_files: slide.slide_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.replacement_list.map(function(slide, index) {
                        return {
                            slide: slide,
                            pending_delete_slide_files: slide.pending_delete_slide_files,
                            index: index
                        }
                    });
                },
                getFilesPendingCount: function(files) {
                    return (files ? files.filter((file) => {
                        return (!file.saved)
                    }).length : '---');
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
        addAccidentReplacementVue.display();
        window.addAccidentReplacementVue = addAccidentReplacementVue;

        function addReplacement() {
            addAccidentReplacementVue.addReplacement();
        }

        function saveReplacement() {
            addAccidentReplacementVue.save();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
