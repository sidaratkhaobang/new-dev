@push('scripts')
    <script>
        let addCarVue = new Vue({
            el: '#car-accessory',
            data: {
                car_list: @if (isset($tor_line_list)) @json($tor_line_list) @else [] @endif,
                all_accessories: @if (isset($car_accessory)) @json($car_accessory) @else [] @endif,
                edit_index: null,
                total_car: 0,
                mode: null,
                prev_index_id: null,
                accessory_controller : @if (isset($accessory_controller)) @json($accessory_controller) @else false @endif,
                approve_controller : @if (isset($approve_controller)) @json($approve_controller) @else false @endif,
            },
            mounted: function() {
                var tor_index = 0;
                this.car_list.forEach(function (car) {
                   if (this.prev_index_id === car.lt_rental_tor_id) {
                        car.tor_index = null;
                   } else {
                        tor_index += 1;
                        car.tor_index = tor_index;
                   }
                    this.prev_index_id = car.lt_rental_tor_id;
                    return car;
                })
            },
            methods: {
                display: function() {
                    this.setTotal();
                    $("#car-accessory").show();
                },
                addCar: function(){
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.clearModalAccessory();
                    addAccessoryVue.setCarAccessories([]);
                    this.mode = 'add';
                    this.openModal();
                },
                editCar: function(index){
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.clearModalAccessory();
                    var filtered_car_accessories = this.filterAccessoryIndex(index);
                    addAccessoryVue.setCarAccessories(filtered_car_accessories);
                    this.mode = 'edit';
                    $("#car-accessory-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function(){
                    $("#car_class_field").val('').change();
                    $("#car_color_field").val('').change();
                    $("#amount_car_field").val('');
                },
                loadModalData: function(index){
                    var temp = null;
                    temp = this.car_list[index];
                    $("#car_class_field").val(temp.car_class_id).change();
                    $("#car_color_field").val(temp.car_color_id).change();
                    $("#amount_car_field").val(temp.amount_car);
                    var defaultCarClassOption = {
                            id: temp.car_class_id,
                            text: temp.car_class_text,
                    };
                    var tempCarClassOption = new Option(defaultCarClassOption.text, defaultCarClassOption.id, false, false);
                    $("#car_class_field").append(tempCarClassOption).trigger('change');

                    var defaultCarColorOption = {
                            id: temp.car_color_id,
                            text: temp.car_color_text,
                    };
                    var tempCarColorOption = new Option(defaultCarColorOption.text, defaultCarColorOption.id, false, false);
                    $("#car_color_field").append(tempCarColorOption).trigger('change');
                },
                clearModalAccessory: function(){
                    $("#accessory_field").val('').change();
                },
                filterAccessoryIndex: function(index){
                    var clone_car_accessories = [...this.all_accessories];
                    return clone_car_accessories.filter(obj => obj.car_index === index);
                },
                openModal: function(){
                    $("#modal-car-accessory").modal("show");
                },
                hideModal: function(){
                    $("#modal-car-accessory").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var car = _this.getCarDataFromModal();
                    if (_this.validateCarObject(car)) {
                        if(_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(car, index);
                        }else {
                            _this.saveAdd(car);
                        }
                        var clone_all_accessories = [..._this.all_accessories];  //clone in js [...array]
                        clone_all_accessories = clone_all_accessories.filter(obj => obj.car_index !== _this.edit_index);
                        var return_car_accessories = addAccessoryVue.getCarAccessories();
                        _this.all_accessories = clone_all_accessories.concat(return_car_accessories);
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    }else{
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getCarDataFromModal: function(){
                    var car_class_id = document.getElementById("car_class_field").value;
                    var car_class_text = (car_class_id) ? document.getElementById('car_class_field').selectedOptions[0].text : '';
                    var car_color_id = document.getElementById("car_color_field").value;
                    var car_color_text = (car_color_id) ? document.getElementById('car_color_field').selectedOptions[0].text : '';
                    var amount_car = document.getElementById("amount_car_field").value;
                    var have_accessories = document.querySelector('input[name="have_accessory_field"]:checked').value;
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
                validateCarObject: function(car){
                    if (car.car_class_id && car.car_color_id && car.amount_car) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(car){
                    this.car_list.push(car);
                },
                saveEdit: function(car, index) {
                    addCarVue.$set(this.car_list, index, car);
                },
                removeCar: function(index) {
                    this.car_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function(){
                    return this.edit_index;
                },
                setLastIndex: function(){
                   return this.car_list.length;
                },
                setTotal: function() {
                    total_car = 0;
                    this.car_list.forEach(element => {
                        total_car += parseInt(element.amount_car);
                    });
                    this.total_car = total_car;
                },
                redirectToEdit: function(tor_id) {
                    route = "{{ route('admin.long-term-rental.specs.tor.edit', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id']) }}"
                    route = route.replace('rental_id', "{{ $d->id }}");
                    route = route.replace('tor_id', tor_id); 
                    var url = new URL(route);
                    if (this.accessory_controller) {
                        url.searchParams.append('accessory_controller', true);
                    }
                    return url.href;
                },
                redirectToView: function(tor_id) {
                    route = "{{ route('admin.long-term-rental.specs.tor.show', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id']) }}";
                    if (this.approve_controller) {
                        route = "{{ route('admin.long-term-rental.specs-approve.show-tor', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id']) }}";
                    }

                    route = route.replace('rental_id', "{{ $d->id }}");
                    route = route.replace('tor_id', tor_id);
        
                    var url = new URL(route);
                    if (this.accessory_controller) {
                        url.searchParams.append('accessory_controller', true);
                    }
                    return url.href;
                },
                redirectToEditAccessory: function(tor_id,id) {
                    route = "{{ route('admin.long-term-rental.specs.tor.edit-car', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id', 'id' => 'lt_rental_tor_line_id']) }}"
                    route = route.replace('rental_id', "{{ $d->id }}");
                    route = route.replace('tor_id', tor_id);
                    route = route.replace('lt_rental_tor_line_id', id);
                    
                    var url = new URL(route);
                    if (this.accessory_controller) {
                        url.searchParams.append('accessory_controller', true);
                    }
                    return url.href;
                },
                redirectToViewAccessory: function(tor_id,id) {
                    route = "{{ route('admin.long-term-rental.specs.tor.show-car', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id', 'id' => 'lt_rental_tor_line_id']) }}";
                    if (this.approve_controller) {
                        route = "{{ route('admin.long-term-rental.specs-approve.show-tor', ['rental' => 'rental_id', 'lt_rental_tor_id' => 'tor_id', 'id' => 'lt_rental_tor_line_id']) }}";
                    }

                    route = route.replace('rental_id', "{{ $d->id }}");
                    route = route.replace('tor_id', tor_id);
                    route = route.replace('lt_rental_tor_line_id', id);
        
                    var url = new URL(route);
                    if (this.accessory_controller) {
                        url.searchParams.append('accessory_controller', true);
                    }
                    return url.href;
                },
                convertToText: function(value) {
                    return (value == true) ? "{{ __('lang.have') }}" : "{{ __('lang.no_have') }}";
                },
                checkedCheckBox: function(lt_rental_tor_id, id) {
                    this_checkbox = $('input[data-id="'+ id +'"]');
                    var is_check = this_checkbox.prop('checked');
                    $('input[data-parent-id="'+lt_rental_tor_id+'"]').prop("checked", false);
                    this_checkbox.prop("checked", is_check);
                },
            },
            props: ['title'],
        });
        addCarVue.display();

        function addCar(){
            addCarVue.addCar();
        }

        function saveCarAccessory() {
            addCarVue.save();
        }

        $("#car_class_field").on('select2:select', function (e) {
            var data = e.params.data;
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
