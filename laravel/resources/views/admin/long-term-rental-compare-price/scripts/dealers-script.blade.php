@push('scripts')
    <script>
        Vue.component('input-number-format', {
            props: ["value"],
            template: `
                <div>
                    <input class="form-control input-show-room" type="text" v-model="displayValue" @blur="isInputActive = false" @focus="isInputActive = true"/>
                </div>`,
            data: function() {
                return {
                    isInputActive: false
                }
            },
            computed: {
                displayValue: {
                    get: function() {
                        if (this.isInputActive) {
                            // Cursor is inside the input field. unformat display value for user
                            return this.value.toString()
                        } else {
                            // User is not modifying now. Format display value for user interface
                            if (this.value == null) {
                                this.value = 0;
                            }
                            this.value = parseFloat(this.value);
                            return this.value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
                        }
                    },
                    set: function(modifiedValue) {
                        // Recalculate value after ignoring "$" and "," in user input
                        let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""))
                        // Ensure that it is not NaN
                        if (isNaN(newValue)) {
                            newValue = 0
                        }
                        // Note: we cannot set this.value as it is a "prop". It needs to be passed to parent component
                        // $emit the event so that parent component gets it
                        this.$emit('input', newValue)
                    }
                }
            }
        });

        let addPurchaseOrderDealerVue = new Vue({
            el: '#purchase-order-dealers',
            data: {
                purchase_requisition_car_list: @if (isset($purchase_requisition_car_list))
                    @json($purchase_requisition_car_list)
                @else
                    []
                @endif ,
                purchase_order_dealer_list: @if (isset($purchase_order_dealer_list))
                    @json($purchase_order_dealer_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_dealer_ids: [],
            },
            mounted: function() {
                var self = this;
                this.purchase_order_dealer_list.forEach(function(purchase_order_dealer) {
                    purchase_order_dealer.dealer_price_list.forEach(function(item) {
                        if (item.car_price) {
                            item.vat = parseFloat(parseFloat(item.car_price) * 7 / 107).toFixed(
                                2);
                            item.vat_exclude_price = parseFloat(parseFloat(item.car_price) *
                                100 / 107).toFixed(2);
                        }
                    });
                })

                this.purchase_order_dealer_list.forEach(function(item) {
                    var newOption = new Option(item.creditor_text, item.creditor_id, false, false);
                    $('#ordered_creditor_id').append(newOption).trigger('change');
                })
            },
            watch: {
                purchase_order_dealer_list: function(selected_dealer) {
                    $('#ordered_creditor_id').empty();
                    selected_dealer.forEach(function(item) {
                        var newOption = new Option(item.creditor_text, item.creditor_id, false, false);
                        $('#ordered_creditor_id').append(newOption);
                    })
                    $('#ordered_creditor_id').val(null);
                }
            },
            methods: {
                display: function() {
                    $("#purchase-order-dealers").show();
                },
                addDealer: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                edit: function(index) {
                    this.setIndex(index);
                    var status = '{{ $d->comparison_price_status }}';
                    var confirm = '{{ ComparisonPriceStatusEnum::CONFIRM }}';
                    if (status != confirm) {
                        var modal_delete_item = document.getElementById('modal-delete-item');
                        modal_delete_item.style.display = "block";
                    }
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#purchase-order-dealer-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    window.myDropzone[0].removeAllFiles(true);
                    var modal_delete_item = document.getElementById('modal-delete-item');
                    modal_delete_item.style.display = "none";
                    $("#creditor_id_field").val('').change();
                    $("#remark_field").val('');
                    this.purchase_requisition_car_list.forEach(function(car) {
                        $("#" + car.id + "_price_field").val('');
                        $("#" + car.id + "_discount_field").val('');
                        $("#" + car.id + "_vat").html('-');
                        $("#" + car.id + "_vat_exclude_price").html('-');
                    });
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.purchase_order_dealer_list[index];

                    $("#creditor_id_field").val(temp.creditor_id).change();
                    $("#remark_field").val(temp.remark);
                    this.purchase_requisition_car_list.forEach(function(car, index) {
                        dealer_price = temp.dealer_price_list[index];
                        var car_price = dealer_price.car_price.toString().replace(
                            /\B(?=(\d{3})+(?!\d))/g, ",");
                        $("#" + car.id + "_price_field").val(car_price);
                        var car_discount = dealer_price.car_discount.toString().replace(
                            /\B(?=(\d{3})+(?!\d))/g, ",");
                        $("#" + car.id + "_discount_field").val(car_discount.length != 0 ?
                            car_discount : 0);
                        $("#" + car.id + "_vat").html(numberWithCommas(dealer_price.vat));
                        $("#" + car.id + "_vat_exclude_price").html(numberWithCommas(dealer_price
                            .vat_exclude));
                    });

                    var defaultDealerOption = {
                        id: temp.creditor_id,
                        text: temp.creditor_text,
                    };
                    var tempDealerOption = new Option(defaultDealerOption.text, defaultDealerOption.id, true,
                        false);
                    $("#creditor_id_field").append(tempDealerOption).trigger('change');
                    $("#creditor_id_field").val(temp.creditor_id).change();
                    // clear file myDropzone
                    window.myDropzone[0].removeAllFiles(true);
                    window.myDropzone[0].options.params.js_delete_files = [];

                    // load file license
                    var dealer_files = temp.dealer_files;
                    if (dealer_files.length > 0) {
                        dealer_files.forEach(item => {
                            console.log(item);
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
                    $("#modal-purchase-order-dealer").modal("show");
                },
                hideModal: function() {
                    $("#modal-purchase-order-dealer").modal("hide");
                },
                save: function() {
                    var _this = this;
                    if (_this.mode == 'edit') {
                        var index = _this.edit_index;
                        _this.saveEdit(index);
                    } else {
                        _this.saveAdd();
                    }
                    this.sortedArray();
                },
                getDataFromModalAdd: function() {
                    var _this = this;
                    var total = 0;
                    var creditor_id = document.getElementById("creditor_id_field").value;
                    var creditor_text = (creditor_id) ? document.getElementById('creditor_id_field')
                        .selectedOptions[0].text : '';
                    const MAX_PRICE = 99999999.99;
                    var dealer_price_list = [];
                    var dealer_raw_files = window.myDropzone[0].files;
                    var dealer_files = dealer_raw_files.map(item => this.formatFile(item));

                    this.purchase_requisition_car_list.forEach(function(car) {
                        var car_price = document.getElementById(car.id + "_price_field").value;
                        car_price = parseFloat(car_price.replace(/,/g, '')).toFixed(2);
                        var car_discount = document.getElementById(car.id + "_discount_field").value;
                        if (car_discount.length != 0) {
                            car_discount = parseFloat(car_discount.replace(/,/g, '')).toFixed(2) ;
                        }else{
                            car_discount = '0.00';
                        }
                        var vat_text = document.getElementById(car.id + "_vat").innerText;
                        var vat = parseFloat(vat_text.replace(/,/g, ''));
                        var vat_exclude_text = document.getElementById(car.id + "_vat_exclude_price")
                            .innerText;
                        var vat_exclude = parseFloat(vat_exclude_text.replace(/,/g, ''));

                        var dealer_car_price = {};
                        dealer_car_price.car_id = car.id;
                        dealer_car_price.car_price = car_price;
                        dealer_car_price.car_discount = car_discount;
                        dealer_car_price.vat = vat;
                        dealer_car_price.vat_exclude = vat_exclude;
                        dealer_car_price.vat_exclude_price = vat_exclude;
                        total += parseFloat(car_price - car_discount) * car.amount;
                        dealer_price_list.push(dealer_car_price);
                    });

                    var id = null;
                    return {
                        id: id,
                        creditor_id: creditor_id,
                        creditor_text: creditor_text,
                        dealer_price_list: dealer_price_list,
                        total: parseFloat(total).toFixed(2),
                        dealer_files: dealer_files,
                        pending_delete_dealer_files: [],
                    };
                },
                validateDataObject: function(validate_data) {
                    // To Do
                    var status = true;
                    var message = '';
                    const MAX_PRICE = 99999999.99;
                    if (!validate_data.creditor_id) {
                        status = false;
                        message = "{{ __('lang.required_field_inform') }}";
                    } else if (validate_data.total > MAX_PRICE) {
                        status = false;
                        message = "{{ __('purchase_orders.price_too_high') }}";
                    } else if (isNaN(parseFloat(validate_data.total)) || parseFloat(validate_data.total) <= 0) {
                        status = false;
                        message = "{{ __('purchase_orders.dealer_price_list_error') }}";
                    } else {}
                    return {
                        'status': status,
                        'message': message
                    };
                },
                saveAdd: function() {
                    var dealer = this.getDataFromModalAdd();
                    var validate_data = dealer;
                    var validate_result = this.validateDataObject(validate_data);
                    if (!validate_result.status) {
                        return warningAlert(validate_result.message);
                    }
                    this.purchase_order_dealer_list.push(dealer);
                    this.edit_index = null;
                    this.display();
                    this.hideModal();
                },
                saveEdit: function(index) {
                    var validate_data = {};
                    var total = 0;
                    var dealer_price_list = [];
                    var creditor_id = document.getElementById("creditor_id_field").value;
                    validate_data.creditor_id = creditor_id;
                    var creditor_text = (creditor_id) ? document.getElementById('creditor_id_field')
                        .selectedOptions[0].text : '';
                    var dealer = this.purchase_order_dealer_list[index];
                    // load files in modal dropzone
                    var modal_dealer_files = window.myDropzone[0].files;
                    var dealer_files = modal_dealer_files.map(item => this.formatFile(item));

                    // get all deleted files
                    var deleted_dealer_files = window.myDropzone[0].options.params.js_delete_files;
                    deleted_dealer_files = deleted_dealer_files.filter((file) => {
                        return (file.media_id);
                    });
                    var deleted_dealer_media_ids = deleted_dealer_files.map((file) => {
                        return file.media_id;
                    });

                    this.purchase_requisition_car_list.forEach(function(car) {
                        var car_price = document.getElementById(car.id + "_price_field").value;
                        car_price = parseFloat(car_price.replace(/,/g, '')).toFixed(2);
                        var car_discount = document.getElementById(car.id + "_discount_field").value;
                        if (car_discount.length != 0) {
                            car_discount = parseFloat(car_discount.replace(/,/g, '')).toFixed(2) ;
                        }else{
                            car_discount = '0.00';
                        }
                        var vat_text = document.getElementById(car.id + "_vat").innerText;
                        var vat = parseFloat(vat_text.replace(/,/g, ''));
                        var vat_exclude_text = document.getElementById(car.id + "_vat_exclude_price")
                            .innerText;
                        var vat_exclude = parseFloat(vat_exclude_text.replace(/,/g, ''));

                        var dealer_car_price = {};
                        dealer_car_price.car_id = car.id;
                        dealer_car_price.car_price = car_price;
                        dealer_car_price.car_discount = car_discount;
                        dealer_car_price.vat = vat;
                        dealer_car_price.vat_exclude = vat_exclude;
                        dealer_car_price.vat_exclude_price = vat_exclude;
                        total += parseFloat(car_price - car_discount) * car.amount;
                        dealer_price_list.push(dealer_car_price);
                    });
                    validate_data.total = total;
                    var validate_result = this.validateDataObject(validate_data);
                    if (!validate_result.status) {
                        return warningAlert(validate_result.message);
                    }
                    dealer.id = dealer.id;
                    dealer.creditor_id = creditor_id;
                    dealer.creditor_text = creditor_text;
                    dealer.total = parseFloat(total).toFixed(2);
                    dealer.dealer_price_list = dealer_price_list;
                    dealer.dealer_files = dealer_files;
                    dealer.pending_delete_dealer_files = deleted_dealer_media_ids;

                    addPurchaseOrderDealerVue.$set(this.purchase_order_dealer_list, index, dealer);
                    this.edit_index = null;
                    this.display();
                    this.hideModal();
                },
                remove: function(index) {
                    if (this.purchase_order_dealer_list[this.edit_index].id) {
                        this.pending_delete_dealer_ids.push(this.purchase_order_dealer_list[this.edit_index]
                            .id);
                    }
                    this.purchase_order_dealer_list.splice(this.edit_index, 1);
                },
                removeCarClass: function(car_id) {
                    var found_index = this.purchase_requisition_car_list.findIndex(x => x.id == car_id);
                    this.purchase_requisition_car_list.splice(found_index, 1);

                    $('#' + car_id).hide();

                },
                setIndex: function(index) {
                    this.edit_index = index;
                    console.log('sett', this.edit_index);
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.purchase_order_dealer_list.length;
                },
                getNumberWithCommas(x) {
                    return numberWithCommas(x);
                },
                sortedArray() {
                    return this.purchase_order_dealer_list.sort((a, b) => a.total - b.total);
                },
                truncateString: function(string, limit) {
                    return string.substring(0, limit) + '...';
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
                    return this.purchase_order_dealer_list.map(function(purchase_order_dealer, index) {
                        return {
                            purchase_order_dealer: purchase_order_dealer,
                            dealer_files: purchase_order_dealer.dealer_files,
                            index: index
                        }
                    });
                },
                getPendingDeleteMediaIds: function() {
                    return this.purchase_order_dealer_list.map(function(purchase_order_dealer, index) {
                        return {
                            purchase_order_dealer: purchase_order_dealer,
                            pending_delete_dealer_files: purchase_order_dealer
                                .pending_delete_dealer_files,
                            index: index
                        }
                    });
                },
                getFilesPendingCount: function(files) {
                    return (files ? files.filter((file) => {
                        return (!file.saved)
                    }).length : '---');
                },
                // updateDealer: function(car){
                //     console.log(car);
                //     var dealer_car_price = {};
                //         dealer_car_price.car_id = car.id;
                //         dealer_car_price.car_price = 0;
                //         dealer_car_price.vat = 0;
                //         dealer_car_price.vat_exclude = 0;
                //         dealer_car_price.vat_exclude_price = 0;
                //         // total += parseFloat(car_price) * parseFloat(car.amount_car);
                //         // dealer_price_list.push(dealer_car_price);

                //     this.purchase_order_dealer_list.map(function (purchase_order_dealer) {
                //         // console.log(purchase_order_dealer.dealer_price_list);
                //        purchase_order_dealer.dealer_price_list.push(dealer_car_price);
                //         console.log(purchase_order_dealer);
                //         // purchase_order_dealer.dealer_price_list = po_dealer;
                //         return purchase_order_dealer;
                //     });

                //     var car_ob = {};
                //         car_ob.amount = car.amount_car;
                //         car_ob.color = car.car_color_text;
                //         car_ob.color_id = car.car_color_id;
                //         car_ob.id = '';
                //         car_ob.model = car.car_class_id;
                //         car_ob.model_full_name = car.car_class_text;

                //     this.purchase_requisition_car_list.push(car_ob);
                // }
            },
            props: ['title'],
        });
        addPurchaseOrderDealerVue.display();
        window.addPurchaseOrderDealerVue = addPurchaseOrderDealerVue;

        function deletePurchaseOrderDealer() {
            addPurchaseOrderDealerVue.remove();
        }

        function addDealer() {
            addPurchaseOrderDealerVue.addDealer();
        }

        function savePurchaseOrderDealer() {
            addPurchaseOrderDealerVue.save();
        }
        let purchase_requisition_car_list = @json($purchase_requisition_car_list);
        //net price for each car
        purchase_requisition_car_list.forEach(function(car) {
            $("#" + car.id + "_price_field").on("input", function() {
                price = $(this).val();
                price = parseFloat(price.replace(/,/g, ''));
                vat = 0;
                vat_exclude_price = 0;
                if ((price)) {
                    vat = parseFloat(parseFloat(price) * 7 / 107).toFixed(2);
                    vat_exclude_price = parseFloat(parseFloat(price) * 100 / 107).toFixed(2);
                }

                $("#" + car.id + "_vat").html(numberWithCommas(vat));
                $("#" + car.id + "_vat_exclude_price").html(numberWithCommas(vat_exclude_price));
            });
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
@endpush
