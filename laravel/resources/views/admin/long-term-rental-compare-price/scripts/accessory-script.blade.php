@push('scripts')
    <script>
        let addAccessoryVue = new window.Vue({
            el: '#accessory',
            data: {
                car_accessories: [],
                type_accessories: '{{ \App\Enums\LongTermRentalTypeAccessoryEnum::ATTACHMENT }}',
            },
            methods: {
                add: function() {
                    var car_index = addCarVue.getIndex();
                    var _this = this;
                    var accessory_id = document.getElementById("accessory_field").value;
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field').selectedOptions[0].text : '';
                    var amount_car = document.getElementById("amount_car_field").value;
                    var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field")
                        .value;
                    var amount_accessory = amount_car * amount_per_car_accessory;
                    // var tor_section = document.getElementById("tor_section_field").value;
                    var remark = document.getElementById("remark_bom_field").value;

                    var accessory = {};
                    if (accessory_id && amount_accessory) {
                        accessory.accessory_id = accessory_id;
                        accessory.accessory_text = accessory_text;
                        accessory.amount_per_car_accessory = amount_per_car_accessory;
                        accessory.amount_accessory = amount_accessory;
                        // accessory.tor_section = tor_section;
                        accessory.remark = remark;
                        accessory.type_accessories = this.type_accessories;
                        accessory.car_index = car_index;

                        _this.car_accessories.push(accessory);
                        $("#accessory").show();

                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        // $("#tor_section_field").val('');
                        $("#amount_per_car_accessory_field").val('').change();
                        $("#remark_bom_field").val('');
                    }else{
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
                        accessory.type_accessories = this.type_accessories;

                        _this.car_accessories.push(accessory);
                        $("#accessory").show();

                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#amount_per_car_accessory_field").val('').change();
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarAccessories: function(){
                    return this.car_accessories;
                },
                setCarAccessories: function(car_accessories){
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
                        item.amount_per_car_accessory = val;
                        return item;
                    });
                    this.car_accessories = new_car_accessories;
                },
            },
            props: ['title'],
        });

        function addAccessory() {
            addAccessoryVue.add();
        }

        function addAccessoryByDefault(e) {
            addAccessoryVue.addByDefault(e);
        }

        function removeAllAccessories() {
            addAccessoryVue.removeAll();
        }

        $("#amount_car_field").on('change', function (e) {
            var val = $(this).val();
            val = parseInt(val, 10);
            if (!isNaN(val)) {
                if (val <= 0) {
                    val = 1;
                }
                $("#amount_accessory_field").val(val).trigger("change");
            }
        });

        $("#amount_accessory_field").on('change', function (e) {
            var val = $(this).val();
            val = parseInt(val, 10);
            if (!isNaN(val)) {
                if (val <= 0) {
                    val = 1;
                }
                addAccessoryVue.changeAllAmount(val);
            }
        });

    </script>
@endpush
