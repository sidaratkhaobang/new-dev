@push('scripts')
    <script>
        let addTemplateImportVue = new Vue({
            el: '#template-import',
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
             
                importData: function(data) {
                    // console.log(data.data)
                    this.face_sheet_list = [];
                    data.data.forEach((element) => {
                        var car_ob = {
                            id: element.registered_id,
                            sale: element.sale_name,
                            tax: element.is_tax_sign,
                            lot_no: element.lot,
                            engine_no: element.engine_no,
                            chassis_no: element.chassis_no,
                            car_class: element.car_class,
                            cc: element.cc,
                            car_color: element.car_color,
                            customer: element.customer,
                            car_characteristic: element.car_characteristic,
                            car_characteristic_transport: element.car_characteristic_transport,
                            color_registered: element.color_registered,
                            registered_date: element.registered_date,
                            receive_information_date: element.receive_information_date,
                            license_plate: element.license_plate,
                            car_tax_exp_date: element.car_tax_exp_date,
                            receipt_date: element.receipt_date,
                            receipt_no: element.receipt_no,
                            tax: element.tax,
                            service_fee: element.service_fee,
                            link: element.link,
                            is_registration_book: element.is_registration_book,
                            is_license_plate: element.is_license_plate,
                            is_tax_sign: element.is_tax_sign,
                        }
                        // console.log(car_ob)
                        this.face_sheet_list.push(car_ob);
                        console.log(this.face_sheet_list)
                    });

                    $('#template-import-car-modal').modal('show');

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
                            car_ob_filter = this.face_sheet_list.filter(obj => obj.status === car_ob
                                .status);

                            car_ob_dup_filter = this.face_sheet_list.filter(obj => obj.id === car_ob
                                .id);
                            // console.log(car_ob_filter)
                            if (car_ob_filter.length > 0) {
                                if (car_ob_dup_filter.length > 0) {
                                    warningAlert("{{ __('registers.validate_car_duplicate') }}");
                                } else {
                                    this.face_sheet_list.push(car_ob);
                                }
                            } else {
                                warningAlert("{{ __('registers.validate_status') }}");
                            }
                        } else {
                            this.face_sheet_list.push(car_ob);
                        }


                    });

                },
              
            },
            props: ['title'],
        });
        addTemplateVue.display();

        function addCar() {
            addTemplateVue.addCar();
        }

        function saveCarAccessory(CarTotal) {
            let CarTotalAmount = CarTotal.closest('.modal').find('.btn-save-car').val()
            let CarAmount = CarTotal.closest('.modal').find('#amount_car_field').val()
            if (CarTotalAmount && CarAmount) {
                if (parseInt(CarAmount) > parseInt(CarTotalAmount)) {
                    warningAlert('จำนวนรถเกิน')
                    return false;
                }
            }
            addTemplateVue.save();
        }

   
    </script>
@endpush
