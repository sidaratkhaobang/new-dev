@push('scripts')
    <script>
        let addPRAccessoryVue = new window.Vue({
            el: '#pr-accessory',
            data: {
                car_accessories: @if (isset($accessories))
                    @json($accessories)
                @else
                    []
                @endif ,
            },
            methods: {
                add: function() {
                    var car_index = addPRCarAccessoryVue.getIndex();
                    var _this = this;
                    var accessory_id = document.getElementById("accessory_field").value;
                    var accessory_text = (accessory_id) ? document.getElementById('accessory_field')
                        .selectedOptions[0].text : '';
                    var amount_accessory = document.getElementById("amount_accessory_field").value;

                    var pr_accessory = {};
                    if (accessory_id && amount_accessory) {
                        pr_accessory.accessory_id = accessory_id;
                        pr_accessory.accessory_text = accessory_text;
                        pr_accessory.amount_accessory = amount_accessory;
                        _this.car_accessories.push(pr_accessory);
                        $("#pr-accessory").show();
                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                        $("#remark_accessory_field").val('').change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                addByDefault: function(e) {
                    var car_index = addPRCarAccessoryVue.getIndex();
                    var _this = this;

                    var pr_accessory = {};
                    if (e.accessory_id) {
                        pr_accessory.accessory_id = e.accessory_id;
                        pr_accessory.accessory_text = e.accessory_text;
                        pr_accessory.amount_accessory = 1;
                        _this.car_accessories.push(pr_accessory);
                        $("#pr-accessory").show();
                        $("#accessory_field").val('').change();
                        $("#amount_accessory_field").val('').change();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
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
            },
            props: ['title'],
        });

        function addAccessory() {
            addPRAccessoryVue.add();
        }

        function addAccessoryByDefault(e) {
            addPRAccessoryVue.addByDefault(e);
        }

        function removeAllAccessories() {
            addPRAccessoryVue.removeAll();
        }

        $("#amount_accessory_field").on('change', function(e) {
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
