@push('scripts')
    <script>
        let addSignYellowTicketVue = new Vue({
            el: '#lawsuit-vue',
            data: {
                lawsuit_list: @if (isset($lawsuit_list))
                    @json($lawsuit_list)
                @else
                    []
                @endif ,
                all_accessories: @if (isset($car_accessory))
                    @json($car_accessory)
                @else
                    []
                @endif ,
                edit_index: null,
                total_car: 0,
                mode: null,
                // pending_delete_receipt_ids: [],
                receipt_files_delete: [],
                status: @if (isset($d->status))
                    @json($d->status)
                @else
                    []
                @endif ,
            },
            methods: {
                display: function() {
                    if (this.lawsuit_list.length >= 1) {
                        $('#car_id').prop('disabled', true)
                    } else {
                        $('#car_id').prop('disabled', false)
                    }
                    $("#lawsuit-vue").show();
                },
                addLawsuit: function() {
                    // this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.clearModalAccessory();
                    // addSignYellowTicketVue.setCarAccessories([]);
                    this.mode = 'add';
                    this.openModal();
                },

                addEmpty: function() {
                    this.lawsuit_list = [];
                    this.all_accessories = [];
                },
                openImageInNewTab(item) {
                    if (item.receipt_files.length > 0) {
                        const dataUrl = item.receipt_files[0].url;
                        const newWindow = window.open();
                        newWindow.document.write(
                            `<img src="${dataUrl}" alt="Receipt" >`
                        );
                    }
                },
                editCar: function(index, CarTotal) {
                    this.clearModalData();
                    this.setIndex(index);
                    this.loadModalData(index);
                    var filtered_car_accessories = this.filterAccessoryIndex(index);
                    this.mode = 'edit';
                    $("#lawsuit-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    flatpickr("#incident_date");
                    flatpickr("#incident_date").clear();
                    // $("#incident_date").trigger("change");
                    $("#lawsuit_detail").val('').change();
                    $("#amount").val('').change();
                    $("#province").val('').change();
                    $("#responsible").val('').change();
                    $("#training").val('').change();
                    $("#driver_type").val('').change();
                    $("#driver").val('').change();
                    $("#tel").val('').change();
                    flatpickr("#notification_date");
                    flatpickr("#notification_date").clear();
                    // $("#notification_date").val('').change();
                    $("#mistake").val('').change();
                    flatpickr("#payment_fine_date");
                    flatpickr("#payment_fine_date").clear();
                    $("#receipt_no").val('').change();
                    $("#amount_total").val('').change();
                    $("#is_payment_fine").val('').change();
                    flatpickr("#payment_date");
                    flatpickr("#payment_date").clear();

                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.lawsuit_list[index];
                    $("#incident_date").val(temp.incident_date);
                    $("#lawsuit_detail").val(temp.lawsuit_detail);
                    var defaultProvinceOption = {
                        id: temp.province_id,
                        text: temp.province_text,
                    };
                    if (temp.province_id) {
                        var tempProvinceOption = new Option(defaultProvinceOption.text, defaultProvinceOption
                            .id,
                            false, true);
                        $("#province").append(tempProvinceOption).trigger('change');
                    }

                    var defaultResponsibleOption = {
                        id: temp.responsible_id,
                        text: temp.responsible_text,
                    };
                    if (temp.responsible_id) {
                        var tempResponsibleOption = new Option(defaultResponsibleOption.text,
                            defaultResponsibleOption.id,
                            false, true);
                        $("#responsible").append(tempResponsibleOption).trigger('change');
                    }

                    var defaultTrainingOption = {
                        id: temp.training_id,
                        text: temp.training_text,
                    };

                    if (temp.training_id) {
                        var tempTrainingOption = new Option(defaultTrainingOption.text, defaultTrainingOption
                            .id,
                            false, true);
                        $("#training").append(tempTrainingOption).trigger('change');

                        var defaultMistakeOption = {
                            id: temp.mistake_id,
                            text: temp.mistake_text,
                        };
                    }

                    if (temp.mistake_id) {
                        var tempMistakeOption = new Option(defaultMistakeOption.text, defaultMistakeOption.id,
                            false, true);
                        $("#mistake").append(tempMistakeOption).trigger('change');
                    }

                    if (temp.is_payment_fine_id) {
                        var defaultIsPaymentFineOption = {
                            id: temp.is_payment_fine_id,
                            text: temp.is_payment_fine_text,
                        };
                        var tempIsPaymentFineOption = new Option(defaultIsPaymentFineOption.text,
                            defaultIsPaymentFineOption.id,
                            false, true);
                    }
                    $("#is_payment_fine").append(tempIsPaymentFineOption).trigger('change');
                    $("#payment_date").val(temp.payment_date);
                    $("#notification_date").val(temp.notification_date);
                    $("#payment_fine_date").val(temp.payment_fine_date);

                    $("#lawsuit_detail").val(temp.lawsuit_detail);
                    $("#amount").val(temp.amount);
                    $("#amount_total").val(temp.amount);
                    $("#receipt_no").val(temp.receipt_no);

                    $("#driver_type").val(temp.driver_type);
                    $("#driver").val(temp.driver);
                    $("#tel").val(temp.tel);



                    window.myDropzone[0].removeAllFiles(true);
                    // window.myDropzone[0].options.params.js_delete_files = [];
                    window.myDropzone[0].options.params.js_delete_files = [];
                    window.myDropzone[0].options.params.pending_delete_ids = [];
                    var receipt_files = temp.receipt_files;
                    if (receipt_files != undefined) {
                        if (receipt_files.length > 0) {
                            window.myDropzone[0].emit("addedfile", receipt_files[0]);
                            window.myDropzone[0].emit("thumbnail", receipt_files[0], receipt_files[0]
                                .url_thumb);
                            window.myDropzone[0].files.push(receipt_files[0]);
                        }
                    }
                    if (this.status == '{{ SignYellowTicketStatusEnum::WAITING_PAY_FINE }}') {
                        // var preview_link = $(
                        //         '.dz-receipt_files-preview-content > .dz-content > [data-dz-name]'
                        //     ).eq(index);
                        //     preview_link.attr('href', receipt_files[0].url);
                        //     preview_link.attr('target', '_blank');
                        $('.dz-content .fa.fa-times').hide();
                    }


                },
                clearModalAccessory: function() {

                },
                filterAccessoryIndex: function(index) {
                    var clone_car_accessories = [...this.all_accessories];
                    return clone_car_accessories.filter(obj => obj.car_index === index);
                },
                openModal: function() {
                    $("#modal-lawsuit").modal("show");
                },
                hideModal: function() {
                    $("#modal-lawsuit").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var lawsuit_data = _this.getCarDataFromModal();
                    if (_this.validateObject(lawsuit_data)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(lawsuit_data, index);
                        } else {
                            _this.saveAdd(lawsuit_data);
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                saveMistake: function() {
                    var _this = this;
                    var mistake_data = _this.getCarDataFromModalMistake();
                    if (_this.validateObjectMistake(mistake_data)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            var lawsuit = this.lawsuit_list[index];

                            lawsuit.notification_date = mistake_data.notification_date;
                            lawsuit.mistake_id = mistake_data.mistake_id;
                            lawsuit.mistake_text = mistake_data.mistake_text;
                            lawsuit.is_mistake = mistake_data.mistake_id;
                            lawsuit.receipt_files = mistake_data.receipt_files;
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                savePaid: function() {
                    var _this = this;
                    var paid_data = _this.getCarDataFromModalPaidDLT();
                    if (_this.validateObjectPaid(paid_data)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            var lawsuit = this.lawsuit_list[index];

                            lawsuit.receipt_no = paid_data.receipt_no;
                            lawsuit.payment_fine_date = paid_data.payment_fine_date;
                            lawsuit.amount_total = paid_data.amount_total;
                            lawsuit.receipt_files = paid_data.receipt_files;

                            // get all deleted files
                            var deleted_receipt_files = window.myDropzone[0].options.params.js_delete_files;
                            deleted_receipt_files = deleted_receipt_files.filter((file) => {
                                return (file.media_id);
                            });
                            var deleted_receipt_media_ids = deleted_receipt_files.map((file) => {
                                return file.media_id;
                            });

                            lawsuit.pending_delete_receipt_files = deleted_receipt_media_ids;

                            if (window.myDropzone[0].options.params.pending_delete_ids.length > 0) {
                                this.receipt_files_delete.push(window.myDropzone[0].options.params
                                    .pending_delete_ids);

                            }

                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },

                savePaidFine: function() {
                    var _this = this;
                    var paid_data = _this.getCarDataFromModalPaidFine();
                    if (_this.validateObjectPaidFine(paid_data)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            var lawsuit = this.lawsuit_list[index];
                            lawsuit.payment_date = paid_data.payment_date;
                            lawsuit.is_payment_fine_id = paid_data.is_payment_fine_id;
                            lawsuit.is_payment_fine_text = paid_data.is_payment_fine_text;
                            lawsuit.is_payment_fine = paid_data.is_payment_fine_id;
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarDataFromModal: function() {
                    var incident_date = document.getElementById("incident_date").value;
                    var lawsuit_detail = document.getElementById("lawsuit_detail").value;
                    var amount = document.getElementById("amount").value;

                    var province_id = document.getElementById("province").value;
                    var province_text = (province_id) ? document.getElementById('province')
                        .selectedOptions[0].text : '';

                    var responsible_id = document.getElementById("responsible").value;
                    var responsible_text = (responsible_id) ? document.getElementById('responsible')
                        .selectedOptions[0].text : '';

                    var training_id = document.getElementById("training").value;
                    var training_text = (training_id) ? document.getElementById('training')
                        .selectedOptions[0].text : '';

                    var driver_type = document.getElementById("driver_type").value;
                    var driver = document.getElementById("driver").value;
                    var tel = document.getElementById("tel").value;

                    var receipt_raw_files = window.myDropzone[0].files;
                    var receipt_files = receipt_raw_files.map(item => this.formatFile(item));

                    return {
                        incident_date: incident_date,
                        lawsuit_detail: lawsuit_detail,
                        province_id: province_id,
                        province_text: province_text,
                        responsible_id: responsible_id,
                        responsible_text: responsible_text,
                        training_id: training_id,
                        training_text: training_text,
                        driver_type: driver_type,
                        driver: driver,
                        amount: amount,
                        tel: tel,
                        receipt_files: receipt_files,
                        pending_delete_receipt_files: [],
                    };
                },
                getCarDataFromModalMistake: function() {
                    var notification_date = document.getElementById("notification_date").value;
                    var mistake_id = document.getElementById("mistake").value;
                    var mistake_text = (mistake_id) ? document.getElementById('mistake')
                        .selectedOptions[0].text : '';

                    var receipt_raw_files = window.myDropzone[0].files;
                    var receipt_files = receipt_raw_files.map(item => this.formatFile(item));

                    return {
                        notification_date: notification_date,
                        mistake_id: mistake_id,
                        mistake_text: mistake_text,
                        receipt_files: receipt_files,
                        pending_delete_receipt_files: [],
                    };
                },

                getCarDataFromModalPaidDLT: function() {
                    var receipt_no = document.getElementById("receipt_no").value;
                    var payment_fine_date = document.getElementById("payment_fine_date").value;
                    var amount_total = document.getElementById("amount_total").value;
                    var receipt_raw_files = window.myDropzone[0].files;
                    var receipt_files = receipt_raw_files.map(item => this.formatFile(item));

                    return {
                        receipt_no: receipt_no,
                        payment_fine_date: payment_fine_date,
                        amount_total: amount_total,
                        receipt_files: receipt_files,
                        pending_delete_receipt_files: [],
                    };
                },
                getCarDataFromModalPaidFine: function() {
                    var payment_date = document.getElementById("payment_date").value;
                    var is_payment_fine_id = document.getElementById("is_payment_fine").value;
                    var is_payment_fine_text = (is_payment_fine_id) ? document.getElementById('is_payment_fine')
                        .selectedOptions[0].text : '';

                    return {
                        payment_date: payment_date,
                        is_payment_fine_id: is_payment_fine_id,
                        is_payment_fine_text: is_payment_fine_text,
                    };
                },
                validateObject: function(lawsuit_data) {
                    if (lawsuit_data.incident_date && lawsuit_data.lawsuit_detail && lawsuit_data.province_id &&
                        lawsuit_data.responsible_id && lawsuit_data.training_id && lawsuit_data.driver_type &&
                        lawsuit_data.driver && lawsuit_data.amount && lawsuit_data.tel) {
                        return true;
                    } else {
                        return false;
                    }
                },

                validateObjectMistake: function(mistake_data) {
                    if (mistake_data.mistake_id && mistake_data.notification_date) {
                        return true;
                    } else {
                        return false;
                    }
                },
                validateObjectPaid: function(paid_data) {
                    if (paid_data.receipt_no && paid_data.payment_fine_date && paid_data.amount_total) {
                        return true;
                    } else {
                        return false;
                    }
                },
                validateObjectPaidFine: function(paid_data) {
                    if (paid_data.payment_date && paid_data.is_payment_fine_id) {
                        return true;
                    } else {
                        return false;
                    }
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
                saveAdd: function(lawsuit_data) {
                    this.lawsuit_list.push(lawsuit_data);
                    if (this.lawsuit_list.length >= 1) {
                        $('#car_id').prop('disabled', true)
                    } else {
                        $('#car_id').prop('disabled', false)
                    }
                },
                saveEdit: function(lawsuit_data, index) {
                    addSignYellowTicketVue.$set(this.lawsuit_list, index, lawsuit_data);
                    if (this.lawsuit_list.length >= 1) {
                        $('#car_id').prop('disabled', true)
                    } else {
                        $('#car_id').prop('disabled', false)
                    }
                },
                removeLawsuit: function(index) {
                    this.lawsuit_list.splice(index, 1);
                    if (this.lawsuit_list.length >= 1) {
                        $('#car_id').prop('disabled', true)
                    } else {
                        $('#car_id').prop('disabled', false)
                    }

                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.lawsuit_list.length;
                },
                getNumberWithCommas(x) {
                    return numberWithCommas(x);
                },
                getFiles: function() {
                    return this.lawsuit_list.map(function(lawsuit, index) {
                        return {
                            lawsuit: lawsuit,
                            receipt_files: lawsuit.receipt_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.lawsuit_list.map(function(lawsuit, index) {
                        return {
                            lawsuit: lawsuit,
                            pending_delete_receipt_files: lawsuit.pending_delete_receipt_files,
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
        addSignYellowTicketVue.display();

        function addLawsuit() {
            addSignYellowTicketVue.addLawsuit();
        }

        function saveLawsuit() {

            addSignYellowTicketVue.save();
        }

        function saveMistake() {
            addSignYellowTicketVue.saveMistake();
        }

        function savePaid() {
            addSignYellowTicketVue.savePaid();
        }

        function savePaidFine() {
            addSignYellowTicketVue.savePaidFine();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
