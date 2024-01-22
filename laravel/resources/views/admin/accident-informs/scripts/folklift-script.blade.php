@push('scripts')
    <script>
        let addAccidentVue = new Vue({
            el: '#folklift',
            data: {
                folklift_list: @if (isset($slide_list))
                    @json($slide_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_slide_ids: [],
                btn_group_sheet: @if (isset($btn_group_sheet))
                    true
                @else
                    false
                @endif ,
            },
            methods: {
                display: function() {
                    $("#folklift").show();
                },
                addSlide: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editAccident: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    // $("#slide_type").prop('disabled', true);
                    $("#folklift-modal-label").html('แก้ไขข้อมูลการยกรถ');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#slide_driver").val('').change();
                    $("#lift_date").val('').change();
                    $("#lift_from").val('').change();
                    $("#lift_to").val('').change();
                    $("#lift_price").val('').change();
                    $("#lift_date_to").val('').change();
                    $("#origin_contact").val('').change();
                    $("#destination_contact").val('').change();
                    $("#origin_tel").val('').change();
                    $("#destination_tel").val('').change();
                    $("#origin_place").val('').change();
                    $("#destination_place").val('').change();

                    // window.myDropzone[0].removeAllFiles(true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.folklift_list[index];
                    // console.log(temp)
                    $("#slide_driver").val(temp.slide_driver).change();
                    $("#lift_date").val(temp.lift_date).change();
                    $("#lift_from").val(temp.lift_from).change();
                    $("#lift_to").val(temp.lift_to).change();
                    $("#lift_price").val(temp.lift_price).change();
                    $("#origin_place").val(temp.origin_place).change();
                    $("#destination_place").val(temp.destination_place).change();
                    $("#lift_date_to").val(temp.destination_date).change();
                    $("#origin_contact").val(temp.origin_contact).change();
                    $("#destination_contact").val(temp.destination_contact).change();
                    $("#origin_tel").val(temp.origin_tel).change();
                    $("#destination_tel").val(temp.destination_tel).change();
                    $("#slide_type").val(temp.slide_type_id).change();
                    flatpickr("#lift_date", temp.lift_date);

                    // var defaultDeiverSkillOption = {
                    //         id: temp.slide_driver,
                    //         text: temp.driving_skill_text,
                    // };
                    // var tempDriverSkillOption = new Option(defaultDeiverSkillOption.text, defaultDeiverSkillOption.id, false, false);
                    // $("#slide_driver").append(tempDriverSkillOption).trigger('change');
                    // clear file myDropzone
                    // window.myDropzone[0].removeAllFiles(true);
                    // window.myDropzone[0].options.params.js_delete_files = [];

                    // // load file skill
                    // var slide_files = temp.slide_files;
                    // if (slide_files.length > 0) {
                    //     slide_files.forEach(item => {
                    //         let mockFile = {
                    //             ...item
                    //         };
                    //         window.myDropzone[0].emit("addedfile", mockFile);
                    //         window.myDropzone[0].emit("thumbnail", mockFile, item.url_thumb);
                    //         window.myDropzone[0].files.push(mockFile);
                    //     });
                    // }
                },
                openModal: function() {
                    $("#modal-folklift").modal("show");
                },
                hideModal: function() {
                    $("#modal-folklift").modal("hide");
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
                    // var slide_driver = document.getElementById("slide_driver").value;
                    var lift_date = document.getElementById("lift_date").value;
                    var lift_date_to = document.getElementById("lift_date_to").value;
                    var lift_from = document.getElementById("lift_from").value;
                    var lift_to = document.getElementById("lift_to").value;
                    var lift_price = document.getElementById("lift_price").value;
                    var origin_contact = document.getElementById("origin_contact").value;
                    var destination_contact = document.getElementById("destination_contact").value;
                    var origin_tel = document.getElementById("origin_tel").value;
                    var destination_tel = document.getElementById("destination_tel").value;
                    var accident_id = document.getElementById("accident_id").value;
                    var slide_type = document.getElementById("slide_type").value;
                    // var driving_skill_text = (driving_skill_id) ? document.getElementById('driving_skill_field').selectedOptions[0].text : '';
                    // var skill_raw_files = window.myDropzone[0].files;
                    // var slide_files = skill_raw_files.map(item => this.formatFile(item));
                    var id = null;
                    return {
                        id: id,
                        // driving_skill_id: driving_skill_id,
                        // slide_driver: slide_driver,
                        lift_date: lift_date,
                        lift_date_to: lift_date_to,
                        lift_from: lift_from,
                        lift_to: lift_to,
                        lift_price: parseFloat(lift_price.replace(/,/g, '')).toFixed(2),
                        origin_contact: origin_contact,
                        destination_contact: destination_contact,
                        origin_tel: origin_tel,
                        destination_tel: destination_tel,
                        accident_id: accident_id,
                        slide_type: slide_type,
                        // slide_files: slide_files,
                        // pending_delete_slide_files: [],
                    };
                },
                validateDataObject: function(slide) {
                    // console.log(slide);
                    if (slide.lift_date_to && slide.lift_date && slide.lift_from && slide.lift_to && slide
                        .lift_price && slide.origin_contact && slide.destination_contact && slide.origin_tel &&
                        slide.destination_tel && slide.slide_type) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function() {

                    var slide_sheet = this.getDataFromModalAdd();
                    if (this.validateDataObject(slide_sheet)) {
                        this.folklift_list.push(slide_sheet);

                        if (this.btn_group_sheet == true) {
                            var updateUri = "{{ route('admin.accident-inform-sheets.save-slide-accident') }}";
                        } else {
                            var updateUri = "{{ route('admin.accident-informs.save-slide-accident') }}";
                        }

                        axios.post(updateUri, slide_sheet).then(response => {
                            if (response.data.success) {    
                                mySwal.fire({
                                    title: "{{ __('lang.store_success_title') }}",
                                    text: "บันทึกข้อมูลเรียบร้อย",
                                    icon: 'success',
                                    confirmButtonText: "{{ __('lang.ok') }}"
                                }).then(value => {
                                    this.hideModal();
                                        window.location.reload();
                                });
                            }
                        });
                        // this.edit_index = null;
                        // this.display();
                        // this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveEdit: function(index) {
                    var slide = this.folklift_list[index];
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
                    var lift_date = document.getElementById("lift_date").value;
                    var lift_date_to = document.getElementById("lift_date_to").value;
                    var lift_from = document.getElementById("lift_from").value;
                    var lift_to = document.getElementById("lift_to").value;
                    var lift_price = document.getElementById("lift_price").value;
                    var origin_contact = document.getElementById("origin_contact").value;
                    var destination_contact = document.getElementById("destination_contact").value;
                    var origin_tel = document.getElementById("origin_tel").value;
                    var destination_tel = document.getElementById("destination_tel").value;
                    var accident_id = document.getElementById("accident_id").value;
                    var slide_type = document.getElementById("slide_type").value;

                    slide['lift_date_to'] = lift_date_to;
                    slide['lift_date'] = lift_date;
                    slide['lift_from'] = lift_from;
                    slide['lift_to'] = lift_to;
                    slide['lift_price'] = parseFloat(lift_price.replace(/,/g, '')).toFixed(2);
                    slide['origin_contact'] = origin_contact;
                    slide['destination_contact'] = destination_contact;
                    slide['origin_tel'] = origin_tel;
                    slide['destination_tel'] = destination_tel;
                    slide['accident_id'] = accident_id;
                    slide['slide_type'] = slide_type;
                    // driver_skill['driving_skill_text'] = driving_skill_text;
                    // slide['slide_files'] = slide_files;
                    // slide['pending_delete_slide_files'] = deleted_slide_media_ids;
                    if (this.validateDataObject(slide)) {
                        addAccidentVue.$set(this.folklift_list, index, slide);

                        if (this.btn_group_sheet == true) {
                            var updateUri = "{{ route('admin.accident-inform-sheets.save-slide-accident') }}";
                        } else {
                            var updateUri = "{{ route('admin.accident-informs.save-slide-accident') }}";
                        }
                        // var updateUri = "{{ route('admin.accident-informs.save-slide-accident') }}";
                        axios.post(updateUri, slide).then(response => {
                            if (response.data.success) {
                                window.location.reload();
                            }
                        });
                        this.edit_index = null;
                        this.display();
                        this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                removeAccident: function(index) {
                    if (this.folklift_list[index].id) {
                        this.pending_delete_slide_ids.push(this.folklift_list[index].id);
                    }
                    this.folklift_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.folklift_list.length;
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
                    return this.folklift_list.map(function(slide, index) {
                        return {
                            // driver: driver,
                            slide_files: slide.slide_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    // console.log('22222')
                    return this.folklift_list.map(function(slide, index) {
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
        addAccidentVue.display();
        window.addAccidentVue = addAccidentVue;

        function addSlide() {
            addAccidentVue.addSlide();
        }

        function saveAccident() {
            addAccidentVue.save();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
