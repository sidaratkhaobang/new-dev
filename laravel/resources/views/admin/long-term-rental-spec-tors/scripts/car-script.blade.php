@push('scripts')
    <script>
        let addCarVue = new Vue({
            el: '#car-accessory',
            data: {
                car_list: @if (isset($car_list))
                    @json($car_list)
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
                pending_delete_car_ids: [],
                accessory_controller: @if (isset($accessory_controller))
                    @json($accessory_controller)
                @else
                    false
                @endif ,
            },
            watch: {
                all_accessories(newValue, oldValue) {
                    if (newValue !== oldValue) {
                        $('#add-dealer').prop('disabled', true);
                    }
                }
            },
            methods: {
                display: function() {
                    this.setTotal();
                    $("#car-accessory").show();
                },
                addCar: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.clearModalAccessory();
                    addAccessoryVue.setCarAccessories([]);
                    this.mode = 'add';
                    this.openModal();
                },
                editCar: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.clearModalAccessory();
                    var filtered_car_accessories = this.filterAccessoryIndex(index);
                    addAccessoryVue.setCarAccessories(filtered_car_accessories);
                    this.mode = 'edit';
                    $("#car-accessory-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#car_class_field").val('').change();
                    $("#car_color_field").val('').change();
                    $("#amount_car_field").val('');
                    $("#car_remark_field").val('');
                    $("#amount_accessory_field").val('');
                    $("#amount_per_car_accessory_field").val('');
                    $("#have_accessory_field0").prop('checked', true);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.car_list[index];
                    $("#car_class_field").val(temp.car_class_id).change();
                    $("#car_color_field").val(temp.car_color_id).change();
                    $("#amount_car_field").val(temp.amount_car);
                    $("#car_remark_field").val(temp.remark);
                    $('#have_accessory_field' + temp.have_accessories).prop('checked', true);
                    // console.log(temp.have_accessories);
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
                    $("#accessory_field").val('').change();
                    $("#accessory_per_car_field").val('').change();
                },
                filterAccessoryIndex: function(index) {
                    var clone_car_accessories = [...this.all_accessories];
                    return clone_car_accessories.filter(obj => obj.car_index === index);
                },
                openModal: function() {
                    $("#modal-car-accessory").modal("show");
                },
                hideModal: function() {
                    this.clearModalData();
                    this.clearModalAccessory();
                    $("#modal-car-accessory").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var car = _this.getCarDataFromModal();
                    if (_this.validateCarObject(car)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            car.id = this.car_list[index].id;
                            _this.saveEdit(car, index);
                        } else {
                            _this.saveAdd(car);
                        }
                        var clone_all_accessories = [..._this.all_accessories]; //clone in js [...array]
                        clone_all_accessories = clone_all_accessories.filter(obj => obj.car_index !== _this
                            .edit_index);
                        var return_car_accessories = addAccessoryVue.getCarAccessories();
                        _this.all_accessories = clone_all_accessories.concat(return_car_accessories);
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                        this.addAccessoriesToCar();
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
                    var remark = document.getElementById("car_remark_field").value;
                    var have_accessories = document.querySelector('input[name="have_accessory_field"]:checked')
                        .value;
                    // console.log(have_accessories);

                    return {
                        car_class_id: car_class_id,
                        car_class_text: car_class_text,
                        car_color_id: car_color_id,
                        car_color_text: car_color_text,
                        amount_car: parseInt(amount_car),
                        remark: remark,
                        have_accessories: parseInt(have_accessories)
                    };
                },
                validateCarObject: function(car) {
                    if (car.car_class_id && car.car_color_id && car.amount_car) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(car) {
                    this.car_list.push(car);
                },
                saveEdit: function(car, index) {
                    addCarVue.$set(this.car_list, index, car);
                },
                removeCar: function(index) {
                    car_id = this.car_list[index].id;
                    this.pending_delete_car_ids.push(car_id);
                    this.car_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.car_list.length;
                },
                setTotal: function() {
                    total_car = 0;
                    this.car_list.forEach(element => {
                        total_car += parseInt(element.amount_car);
                    });
                    this.total_car = total_car;
                },
                addAccessoriesToCar: function() {
                    this.car_list.map(element => {
                        element.accessory_list = [];
                        // element.have_accessories = 0;
                        return element;
                    });
                    this.all_accessories.forEach(accessory => {
                        car_index = accessory.car_index;
                        var car = this.car_list[car_index];
                        car.accessory_list.push(accessory);
                        console.log(accessory);
                        // if (car.accessory_list.length > 0) { // check have accesory from count
                        //     car.have_accessories = 1; 
                        // }
                    });
                },
                convertToText: function(value) {
                    // console.log(value);
                    return (value == true) ? "{{ __('lang.have') }}" : "{{ __('lang.no_have') }}";
                },
            },
            props: ['title'],
        });
        addCarVue.display();

        function addCar() {
            addCarVue.addCar();
        }

        function saveCarAccessory() {
            addCarVue.save();
        }

        // $("#car_class_field").on('select2:select', function (e) {
        //     var data = e.params.data;
        //     axios.get("{{ route('admin.purchase-requisition.default-car-class-accessories') }}", {
        //         params: {
        //             car_class_id: data.id
        //         }
        //     }).then(response => {
        //         if (response.data.success) {
        //             removeAllAccessories();
        //             if (response.data.data.length > 0) {
        //                 response.data.data.forEach((e) => {
        //                     addAccessoryByDefault(e);
        //                 });
        //             }
        //         }
        //     });
        // });
    </script>
@endpush
