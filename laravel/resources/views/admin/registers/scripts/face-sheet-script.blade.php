@push('scripts')
    <script>
        let addFaceSheetVue = new Vue({
            el: '#face-sheet',
            data: {
                face_sheet_list: @if (isset($pr_car_list))
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
                    // this.setTotal();
                    // $("#pr-car").show();
                },
               
                addCar: function(data) {
                    // var data = e.params.data;
                    data.forEach((element) => {
                        var car_ob = {
                            id: element.id,
                            car_id: element.car_id,
                            engine_no: element.engine_no,
                            chassis_no: element.chassis_no,
                            car_class_id: element.car_class_id,
                            full_name: element.full_name,
                            lot_no: element.lot_no,
                            status: element.status,
                        }

                        if (this.face_sheet_list.length > 0) {
                            car_ob_filter = this.face_sheet_list.filter(obj => obj.status === car_ob.status);

                            car_ob_dup_filter = this.face_sheet_list.filter(obj => obj.id === car_ob
                                .id);
                            // console.log(car_ob_filter)
                            if(car_ob_filter.length > 0){
                                if (car_ob_dup_filter.length > 0) {
                                    warningAlert("{{ __('registers.validate_car_duplicate') }}");
                                } else {
                                    this.face_sheet_list.push(car_ob);
                                }
                            }else{
                                warningAlert("{{ __('registers.validate_status') }}");
                            }       
                        }else{
                            this.face_sheet_list.push(car_ob);
                        }

                        
                    });

                },
                clearModalAccessory: function() {
                    $("#accessory_field").val('').change();
                },
                filterAccessoryIndex: function(index) {
                    var clone_car_accessories = [...this.all_accessories];
                    // console.log(clone_car_accessories);
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
                        var clone_all_accessories = [..._this.all_accessories]; //clone in js [...array]
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
              
                removeCar: function(index) {
                    this.face_sheet_list.splice(index, 1);
                },
               
            },
            props: ['title'],
        });
        addFaceSheetVue.display();

        function addCar() {
            addFaceSheetVue.addCar();
        }

       
    </script>
@endpush
