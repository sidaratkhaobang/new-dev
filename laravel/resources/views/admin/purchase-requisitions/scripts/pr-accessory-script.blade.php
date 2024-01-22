@push('scripts')
    <script>
        let addPRAccessoryVue = new window.Vue({
            el: '#pr-accessory',
            data: {
                car_accessories: [],
                type_accessories: '{{ \App\Enums\LongTermRentalTypeAccessoryEnum::ATTACHMENT }}',
            },
            methods: {
                add: function() {
                    var car_index = addPRCarAccessoryVue.getIndex();
                    var _this = this;
                    var accessory_id = document.getElementById("accessory_field").value;
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field')
                        .selectedOptions[0].text : '';
                    var amount_car = document.getElementById("amount_car_field").value;
                    var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field")
                        .value;
                    var amount_accessory = amount_car * amount_per_car_accessory;
                    var remark_accessory = document.getElementById("remark_accessory").value;

                    var accessory = {};
                    if (accessory_id) {
                        accessory.accessory_id = accessory_id;
                        accessory.accessory_text = accessory_text;
                        accessory.amount_per_car_accessory = amount_per_car_accessory;
                        accessory.amount_accessory = amount_accessory;
                        accessory.remark_accessory = remark_accessory;
                        accessory.car_index = car_index;
                        accessory.type_accessories = this.type_accessories;

                        _this.car_accessories.push(accessory);
                        $("#accessory").show();
                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#amount_per_car_accessory_field").val('').change();
                        $("#remark_accessory").val('').change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                addByDefault: function(e) {
                    var car_index = addCarVue.getIndex();
                    var _this = this;

                    var accessory = {};
                    if (e.accessory_id) {
                        accessory.accessory_id = e.accessory_id;
                        accessory.accessory_text = e.accessory_text;
                        accessory.amount_accessory = 1;
                        accessory.amount_per_car_accessory = 1;
                        accessory.car_index = car_index;
                        accessory.remark_accessory = remark_accessory;
                        accessory.type_accessories = this.type_accessories;
                        _this.car_accessories.push(accessory);
                        $("#accessory").show();

                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#amount_per_car_accessory_field").val('').change();
                        $("#remark_accessory").val('').change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                addAccessoryByRental: function(e, index) {
                    var pr_accessory = {};
                    var _this = this;
                    if (e.accessory_id) {
                        pr_accessory.accessory_id = e.accessory_id;
                        pr_accessory.accessory_text = e.accessory_text;
                        pr_accessory.amount_accessory = e.amount;
                        pr_accessory.car_index = index;
                        pr_accessory.remark_accessory = e.remark_accessory;
                        pr_accessory.type_accessories = e.type_accessories;
                        _this.car_accessories.push(pr_accessory);
                        addPRCarAccessoryVue.setCarAccessories(_this.car_accessories);
                        $("#pr-accessory").show();
                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#remark_accessory").val('').change();
                    }
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
                addEmpty: function() {
                    var _this = this;
                    _this.car_accessories = [];
                },
            },
            props: ['title'],
        });

        function addAccessory() {
            addPRAccessoryVue.add();
        }

        function addAccessoryByDefault(e) {
            addPRAccessoryVue.addByDefault(e);
        }

        function addAccessoryByRental(e, index) {
            addPRAccessoryVue.addAccessoryByRental(e, index);
        }

        function removeAllAccessories() {
            addPRAccessoryVue.removeAll();
        }

        $("#amount_car_field").on('change', function(e) {
            var val = $(this).val();
            val = parseInt(val, 10);
            if (!isNaN(val)) {
                if (val <= 0) {
                    val = 1;
                }
                $("#amount_accessory_field").val(val).trigger("change");
            }
        });
    </script>
@endpush
