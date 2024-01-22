@push('scripts')
    <script>
        $('input[name="temp_is_at_tls"]').change(function() {
            var value = $(this).val();
            if (value == 1) {
                $("#temp_slide_id").val(null).change();
                $("#temp_slide_id").prop('disabled', true);
            } else {
                $("#temp_slide_id").prop('disabled', false);
            }
        });
        // replacment_section
        const mode = @if (isset($mode)) @json($mode) @else null @endif;
        let replacement_list = @if (isset($replacement_list)) @json($replacement_list) @else [] @endif;
        if (replacement_list.length == 0) {
            $("#replacment_section").hide();
        }

        function showReplacementSection() {
            $("#replacment_section").slideDown();
            $("#btn_add_replacement").hide();
            document.getElementById('replacment_section').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        let addReplacementVue = new Vue({
            el: '#replacment_section',
            data: {
                replacement_list: @if (isset($replacement_list)) @json($replacement_list) @else [] @endif,
                edit_index: null,
                mode: null,
                page_mode: @if (isset($mode)) @json($mode) @else [] @endif,
                pending_replacement_ids: [],
            },
            methods: {
                display: function() {
                    $("#replacement-modal").show();
                },
                addData: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit'; 
                    $("#replacement-modal-label").html('แก้ไขเปิดงานรถหลัก/รถทดแทน');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#temp_job_type").val(null).change();
                    $("#temp_send_pickup_date").val('');
                    $('input[id="temp_is_at_tls0"]').prop('checked', true);
                    $("#temp_send_pickup_place").val('');
                    $("#temp_slide_id").prop('disabled', false);
                    $("#temp_slide_id").val(null).change();
                    window.myDropzone[1].removeAllFiles(true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.replacement_list[index];
                    $("#temp_job_type").val(temp.job_type_id);
                    $("#temp_send_pickup_date").val(temp.send_pickup_date);
                    $("#temp_slide_id").val(temp.slide_id).change();
                    if (temp.is_pickup_at_tls) {
                        $("#temp_slide_id").prop('disabled', true);
                        $('input[id="temp_is_at_tls1"]').prop('checked', true);
                    } else {
                        $("#temp_slide_id").prop('disabled', false);
                        $('input[id="temp_is_at_tls0"]').prop('checked', true);
                        var defaultSlideOption = {
                            id: temp.slide_id,
                            text: temp.slide_text,
                        };
                        var tempSlideOption = new Option(defaultSlideOption.text, defaultSlideOption.id, false, false);
                        $("#temp_slide_id").append(tempSlideOption).trigger('change');
                    }
                    $("#temp_send_pickup_place").val(temp.send_pickup_place);
                    $("#temp_slide_id").val(temp.slide_id);
                },
                openModal: function() {
                    $("#replacement-modal").modal("show");
                },
                hideModal: function() {
                    $("#replacement-modal").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var replacement = _this.getDataFromModal();
                    if (this.validateObject(replacement)) {
                        if (this.page_mode == '{{ MODE_UPDATE }}') {
                            _this.callFormSave(replacement);
                        }
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(replacement, index);
                        } else {
                            _this.saveAdd(replacement);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    }
                },
                getDataFromModal: function() {
                    var main_car_id = document.getElementById("car_id").value;
                    var main_license_plate  = (main_car_id) ? document.getElementById('car_id').selectedOptions[0].text : '';
                    var job_type_id = document.getElementById("temp_job_type").value;
                    var job_type_text = (job_type_id) ? document.getElementById('temp_job_type').selectedOptions[0].text : '';
                    var send_pickup_date = document.getElementById("temp_send_pickup_date").value;
                    var is_pickup_at_tls = document.querySelector('input[name="temp_is_at_tls"]:checked').value;
                    var slide_id = document.getElementById("temp_slide_id").value;
                    var slide_text = (slide_id) ? document.getElementById('temp_slide_id').selectedOptions[0].text : '';
                    var send_pickup_place = document.getElementById("temp_send_pickup_place").value;
                    var replacement_raw_files = window.myDropzone[1].files;
                    var replacement_files = replacement_raw_files.map(item => this.formatFile(item));
                   
                    return {
                        job_type_id: job_type_id,
                        job_type_text: job_type_text,
                        send_pickup_date: send_pickup_date,
                        is_pickup_at_tls: parseInt(is_pickup_at_tls),
                        slide_id: slide_id,
                        slide_text: slide_text,
                        send_pickup_place: send_pickup_place,
                        main_car_id: main_car_id,
                        main_license_plate: main_license_plate,
                        replacement_files: replacement_files,
                        pending_delete_replacement_files: [],
                    };
                },
                validateObject: function(replacement) {
                    var main_car_id = document.getElementById("car_id").value;
                    if (!main_car_id) {
                        warningAlert("{{ __('repairs.main_car_not_found') }}");
                        return false;
                    }
                    if (!replacement.job_type_id || !replacement.send_pickup_date || !replacement.send_pickup_place) {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                        return false;
                    }
                    return true;
                },
                saveAdd: function(replacement) {
                    this.replacement_list.push(replacement);
                },
                saveEdit: function(replacement, index) {
                    addReplacementVue.$set(this.replacement_list, index, replacement);
                },
                remove: function(index) {
                    if (this.replacement_list[index].id) {
                        this.pending_replacement_ids.push(this.replacement_list[index].id);
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
                formatDate(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-US', options);
                    return formattedDate;
                },
                formatFile: function(file) {
                    if(file.formated){
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
                callFormSave: async function(replacement_car) {
                    showLoading();
                    const storeUri = '{{ route("admin.replacement-car-informs.store") }}';
                    var job_id = document.getElementById("id").value;
                    var main_car_id = document.getElementById("car_id").value;
                    var customer_name = document.getElementById("contact").value;
                    var tel = document.getElementById("tel").value;
                    var replacement_raw_files = window.myDropzone[1].files;

                    var formData = new FormData();
                    formData.append('replacement_type', replacement_car.job_type_id);
                    formData.append('job_type', '{{ ReplacementJobTypeEnum::REPAIR }}');
                    formData.append('job_id', job_id);
                    formData.append('main_car_id', main_car_id);
                    formData.append('is_pickup_at_tls', replacement_car.is_pickup_at_tls);
                    formData.append('is_need_slide', replacement_car.slide_id ? true : false);
                    formData.append('slide_id', replacement_car.slide_id);
                    formData.append('is_need_driver', (!replacement_car.is_pickup_at_tls && !replacement_car.slide_id));
                    formData.append('replacement_expect_place', replacement_car.send_pickup_place);
                    formData.append('replacement_expect_date', replacement_car.send_pickup_date);
                    formData.append('customer_name', customer_name);
                    formData.append('tel', tel);
                    replacement_raw_files.forEach((file) => {
                        formData.append('documents[]', file);
                    });
                    await axios.post(storeUri, formData).then(response => {
                        hideLoading();
                        if (response.data.success) {
                            window.location.reload();
                        } else {
                            errorAlert(response.data.message);
                        }
                    }).catch(error => {
                        hideLoading();
                        errorAlert(response.data.message);
                    });
                },
                getFilesPendingCount: function (files) {
                    return (files ? files.filter((file) => {return (!file.saved)}).length : '---');
                },
                openReplacementTab: function (replacement_id) {
                    const url = "{{ route('admin.replacement-car-informs.edit', ['replacement_car_inform' => ':replacement_id']) }}";
                    const formatted_url = url.replace(':replacement_id', replacement_id);
                    window.open(formatted_url, '_blank');
                },
                setData: function(list) {
                    this.replacement_list = list;
                }
            },
            props: ['title'],
        });

        function openReplacementModal() {
            addReplacementVue.clearModalData();
            addReplacementVue.openModal();
        }

        function saveReplacementCar() {
            addReplacementVue.save();
        }
        $('#replacement-modal').on('hide.bs.dropdown', function() {
            addReplacementVue.clearModalData();
        })
    </script>
@endpush
