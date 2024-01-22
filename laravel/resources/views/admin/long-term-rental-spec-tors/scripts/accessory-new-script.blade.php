@push('scripts')
    <script>
        let addAccessoryVue = new window.Vue({
            el: '#accessory-new',
            data: {
                car_list: @if (isset($car_list))
                    @json($car_list)
                @else
                    []
                @endif ,
                car_accessories: @if (isset($car_accessory))
                    @json($car_accessory)
                @else
                    []
                @endif ,
                type_accessories: '{{ \App\Enums\LongTermRentalTypeAccessoryEnum::ADDITIONAL }}',
            },
            methods: {
                display: function() {
                    console.log('test');
                    // this.loadModalData();
                },
                add: function() {
                    // var car_index = addCarVue.getIndex();
                    var _this = this;
                    console.log(_this.car_accessories);
                    var accessory_id = document.getElementById("accessory_field").value;
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field')
                        .selectedOptions[0].text : '';
                    var amount_accessory = document.getElementById("amount_accessory_field").value;
                    var tor_section = document.getElementById("tor_section_field").value;
                    var remark = document.getElementById("remark_bom_field").value;

                    var accessory = {};
                    if (accessory_id && amount_accessory) {
                        accessory.accessory_id = accessory_id;
                        accessory.accessory_text = accessory_text;
                        accessory.amount_accessory = amount_accessory;
                        accessory.tor_section = tor_section;
                        accessory.remark = remark;
                        accessory.type_accessories = this.type_accessories;
                        // accessory.car_index = car_index;

                        _this.car_accessories.push(accessory);
                        $("#accessory").show();
                        // $("#have_accessory_field1").prop('checked', true);
                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#tor_section_field").val('');
                        $("#remark_bom_field").val('');
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                addFromModal: function() {
                    var _this = this;
                    var id = document.getElementById("bom_field").value;
                    var tor_section = document.getElementById("tor_section").value;
                    var remark = document.getElementById("remark_bom").value;
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('admin.long-term-rental.specs.tors.get-data-accessory-type') }}",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: id
                        },
                        success: function(data) {

                            $('#list_table').empty();
                            console.log(data);
                            var accessory_id = '';
                            var accessory_text = '';
                            var amount_accessory = '';

                            if (data.lists.length > 0) {
                                data.lists.forEach((element, index) => {
                                    var accessory = {};
                                    accessory.accessory_id = element.accessories_id;
                                    accessory.accessory_text = element.name;
                                    accessory.amount_accessory = element.amount;
                                    accessory.tor_section = tor_section;
                                    accessory.remark = remark;
                                    accessory.type_accessories = this.type_accessories;
                                    _this.car_accessories.push(accessory);
                                });
                            }
                        }
                    });
                    $("#modal-bom").modal("hide");
                    $("#bom_field").val('').change();
                },
                addByDefault: function(e) {
                    var car_index = addCarVue.getIndex();
                    var _this = this;

                    var accessory = {};
                    if (e.accessory_id) {
                        accessory.accessory_id = e.accessory_id;
                        accessory.accessory_text = e.accessory_text;
                        accessory.amount_accessory = 1;
                        accessory.car_index = car_index;
                        accessory.type_accessories = this.type_accessories;

                        _this.car_accessories.push(accessory);
                        $("#accessory").show();

                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.car_list[index];
                    console.log(temp);
                    $("#car_class_field").val(temp.car_class_id).change();
                    $("#car_color_field").val(temp.car_color_id).change();
                    $("#amount_car_field").val(temp.amount_car);
                    $("#car_remark_field").val(temp.remark);
                    $('#have_accessory_field' + temp.have_accessories).prop('checked', true);
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
                getCarAccessories: function() {
                    return this.car_accessories;
                },
                setCarAccessories: function(car_accessories) {
                    this.car_accessories = car_accessories;
                },
                removeAccessory: function(index) {
                    this.car_accessories.splice(index, 1);
                },
                removeAll: function() {
                    this.car_accessories = [];
                },
                changeAllAmount: function(val) {
                    var car_accessories = [...this.car_accessories];
                    var new_car_accessories = car_accessories.map((item) => {
                        item.amount_accessory = val;
                        return item;
                    });
                    this.car_accessories = new_car_accessories;
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
                        // if (car.accessory_list.length > 0) {
                        //     car.have_accessories = 1;
                        // }
                    });
                },
            },
            props: ['title'],
        });
        addAccessoryVue.display();

        function addAccessory() {
            addAccessoryVue.add();
        }

        function saveAccessory() {
            addAccessoryVue.addFromModal();
        }

        function addAccessoryByDefault(e) {
            addAccessoryVue.addByDefault(e);
        }

        function removeAllAccessories() {
            addAccessoryVue.removeAll();
        }

        function modalAccessory() {
            $("#modal-bom").modal("show");
        }

        $("#amount_car_field").on('change', function(e) {
            var val = $(this).val();
            val = parseInt(val, 10);
            if (!isNaN(val)) {
                if (val <= 0) {
                    val = 1;
                }
                // $("#amount_accessory_field").val(val).trigger("change");
            }
        });
    </script>
@endpush
