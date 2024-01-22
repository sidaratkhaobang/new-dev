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
                            id: element.transfers_id,
                            leasing_name: element.leasing_name,
                            engine_no: element.engine_no,
                            chassis_no: element.chassis_no,
                            car_class: element.car_class,
                            cc: element.cc,
                            car_color: element.car_color,
                            license_plate: element.license_plate,
                            car_ownership_date: element.car_ownership_date,
                            receive_registration_book_date: element.receive_registration_book_date,
                            return_registration_book_date: element.return_registration_book_date,
                            receipt_date: element.receipt_date,
                            receipt_no: element.receipt_no,
                            receipt_fee: element.receipt_fee,
                            service_fee: element.service_fee,
                            car_ownership_date: element.car_ownership_date,
                        }
                        // console.log(car_ob)
                        this.face_sheet_list.push(car_ob);
                        // console.log(this.face_sheet_list)
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
                formatDate(date) {
                    var dateObject = new Date(date);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var formattedDate = dateObject.toLocaleDateString('en-GB', options);
                    return formattedDate;
                },
              
            },
            props: ['title'],
        });
        // addTemplateVue.display();

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
