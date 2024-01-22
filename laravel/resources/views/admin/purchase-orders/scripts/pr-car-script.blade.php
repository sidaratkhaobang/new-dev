@push('scripts')
    <script>
        let addPRCarAccessoryVue = new Vue({
            el: '#pr-car',
            data: {
                pr_car_list: @if (isset($pr_car_list))
                    @json($pr_car_list)
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
            },
            methods: {
                display: function() {
                    this.setTotal();
                    $("#pr-car").show();
                },
                addCar: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.clearModalAccessory();
                    addPRAccessoryVue.setCarAccessories([]);
                    this.mode = 'add';
                    this.openModal();
                },
                addByDefault: function(e, index) {
                    var _this = this;
                    var pr_car = {};
                    if (e.id) {
                        pr_car.car_class_id = e.car_class_id;
                        pr_car.car_class_text = e.car_class_text;
                        pr_car.car_color_id = e.car_color_id;
                        pr_car.car_color_text = e.car_color_text;
                        pr_car.amount_car = e.amount;
                        pr_car.remark_car = e.remark;
                        _this.pr_car_list.push(pr_car);
                    }
                    var index_car = index;
                    if (e.accessory.length > 0) {
                        e.accessory.forEach((e) => {
                            addAccessoryByRental(e, index_car);
                        });
                    }
                    this.setTotal();
                },
                addEmpty: function() {
                    var _this = this;
                    _this.pr_car_list = [];
                },
                editCar: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.clearModalAccessory();
                    var filtered_car_accessories = this.filterAccessoryIndex(index);
                    addPRAccessoryVue.setCarAccessories(filtered_car_accessories);
                    this.mode = 'edit';
                    $('#add-accessrory').show();
                    $('#save-modal').show();
                    $(document).ready(function() {
                        $('.remove-accessory').show();
                    });
                    $("#car-accessory-modal-label").html('แก้ไขข้อมูล');
                    $("#cancel-button").html('ยกเลิก');
                    $('#accessory_field').prop('disabled', false);
                    $('#amount_accessory_field').prop('disabled', false);
                    $('#remark_field').prop('disabled', false);
                    this.openModal();
                },
                viewCar: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.clearModalAccessory();
                    var filtered_car_accessories = this.filterAccessoryIndex(index);
                    addPRAccessoryVue.setCarAccessories(filtered_car_accessories);
                    this.mode = 'view';
                    $('#add-accessrory').hide();
                    $('#save-modal').hide();
                    $(document).ready(function() {
                        $('.remove-accessory').hide();
                    });
                    $("#car-accessory-modal-label").html('ดูข้อมูล');
                    $("#cancel-button").html('กลับ');
                    $('#accessory_field').prop('disabled', true);
                    $('#amount_accessory_field').prop('disabled', true);
                    $('#remark_field').prop('disabled', true);
                    this.openModal();
                },
                clearModalData: function() {
                    $("#car_class_field").val('').change();
                    $("#car_color_field").val('').change();
                    $("#amount_car_field").val('');
                    $("#remark_car_field").val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.pr_car_list[index];
                    $("#car_class_field").val(temp.car_class_id).change();
                    $("#car_color_field").val(temp.car_color_id).change();
                    $("#amount_car_field").val(temp.amount_car);
                    $("#remark_car_field").val(temp.remark_car);
                    var defaultCarClassOption = {
                        id: temp.car_class_id,
                        text: temp.car_class_text,
                    };
                    var tempCarClassOption = new Option(defaultCarClassOption.text, defaultCarClassOption.id,
                        false, false);
                    $("#car_class_field").append(tempCarClassOption).trigger('change');

                    var defaultCarColorOption = {
                        id: temp.car_color_id,
                        text: temp.car_color_text,
                    };
                    var tempCarColorOption = new Option(defaultCarColorOption.text, defaultCarColorOption.id,
                        false, false);
                    $("#car_color_field").append(tempCarColorOption).trigger('change');
                },
                clearModalAccessory: function() {
                    $("#accessory_field").val('').change();
                },
                filterAccessoryIndex: function(index) {
                    var clone_car_accessories = [...this.all_accessories];
                    return clone_car_accessories.filter(obj => obj.car_index === index);
                },
                openModal: function() {
                    $("#modal-car-accessory").modal("show");
                },
                hideModal: function() {
                    $("#modal-car-accessory").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var pr_car = _this.getCarDataFromModal();
                    if (_this.validateCarObject(pr_car)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(pr_car, index);
                        } else {
                            _this.saveAdd(pr_car);
                        }
                        var clone_all_accessories = [..._this.all_accessories]; 
                        clone_all_accessories = clone_all_accessories.filter(obj => obj.car_index !== _this
                            .edit_index);
                        var return_car_accessories = addPRAccessoryVue.getCarAccessories();
                        _this.all_accessories = clone_all_accessories.concat(return_car_accessories);
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarDataFromModal: function() {
                    var car_class_id = document.getElementById("car_class_field").value;
                    var car_class_text = (car_class_id) ? document.getElementById('car_class_field')
                        .selectedOptions[0].text : '';
                    var car_color_id = document.getElementById("car_color_field").value;
                    var car_color_text = (car_color_id) ? document.getElementById('car_color_field')
                        .selectedOptions[0].text : '';
                    var amount_car = document.getElementById("amount_car_field").value;
                    var remark_car = document.getElementById("remark_car_field").value;
                    return {
                        car_class_id: car_class_id,
                        car_class_text: car_class_text,
                        car_color_id: car_color_id,
                        car_color_text: car_color_text,
                        amount_car: amount_car,
                        remark_car: remark_car,
                    };
                },
                validateCarObject: function(pr_car) {
                    if (pr_car.car_class_id && pr_car.car_color_id && pr_car.amount_car) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(pr_car) {
                    this.pr_car_list.push(pr_car);
                },
                saveEdit: function(pr_car, index) {
                    addPRCarAccessoryVue.$set(this.pr_car_list, index, pr_car);
                },
                removeCar: function(index) {
                    this.pr_car_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.pr_car_list.length;
                },
                setTotal: function() {
                    total_car = 0;
                    this.pr_car_list.forEach(element => {
                        total_car += parseInt(element.amount_car);
                    });
                    this.total_car = total_car;
                },
                setCarAccessories: function(car_accessories) {
                    this.all_accessories = car_accessories;
                },
            },
            props: ['title'],
        });
        addPRCarAccessoryVue.display();

        function addCar() {
            addPRCarAccessoryVue.addCar();
        }

        function saveCarAccessory() {
            addPRCarAccessoryVue.save();
        }

        function addCarByDefault(e, index) {
            // console.log(e);
            addPRCarAccessoryVue.addByDefault(e, index);
        }

        function addEmpty(e, index) {
            // console.log(e);
            addPRCarAccessoryVue.addEmpty();
        }

        $("#reference_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.purchase-requisition.rental-type-data') }}", {
                params: {
                    rental_type: $("#rental_type").val(),
                    rental_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#customer_type").val(e.customer_type);
                            $("#customer_name").val(e.customer_name);
                            if (response.data.rental_type ===
                                '{{ \App\Enums\RentalTypeEnum::LONG }}') {
                                $("#job_type").val(e.job_type);
                                $("#rental_duration").val(e.rental_duration);
                            }
                        });
                    }

                    let rental_images_files = response.data.rental_images_files;

                    //upload file
                    if (rental_images_files.length > 0) {
                        $('.rental_images-previews').empty();

                        rental_images_files.forEach((file, index_file) => {
                            let mockFile = {
                                name: file.file_name,
                                size: file.size,
                                media_id: '',
                                pending_del_media_id: file.id,
                            };

                            window.myDropzone[0].displayExistingFile(mockFile, rental_images_files[
                                index_file].original_url);
                            window.myDropzone[0].addPending(rental_images_files[index_file].id);
                        });
                    } else {
                        console.log('asdasdasdsa');
                        $('.rental_images-previews').empty();
                        window.myDropzone[0].options.params.pending_add_ids = [];
                        window.myDropzone[0].options.params.quota_count = window.myDropzone[0].options
                            .maxFiles;
                    }

                    if (response.data.lt_rental_line.length > 0) {
                        addEmpty();
                        response.data.lt_rental_line.forEach((e, index) => {
                            addCarByDefault(e, index);
                        });
                    } else {
                        addEmpty();
                    }

                }

            });
        });

        $("#reference_id").on('select2:clear', function(e) {
            addEmpty();
        });

        $("#car_class_field").on('select2:select', function(e) {
            var data = e.params.data;
            //console.log(e);
            //console.log(data);
            axios.get("{{ route('admin.purchase-requisition.default-car-class-accessories') }}", {
                params: {
                    car_class_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    removeAllAccessories();
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            addAccessoryByDefault(e);
                        });
                    }
                }
            });
        });
    </script>
@endpush
