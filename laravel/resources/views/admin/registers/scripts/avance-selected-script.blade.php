@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        let addAvanceSelectedVue = new Vue({
            el: '#avance-selected',
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
                        var total = parseFloat(element.receipt_avance) + parseFloat(element
                            .operation_fee_avance);
                        total = total.toFixed(2)
                        var car_ob = {
                            id: element.id,
                            car_id: element.car_id,
                            engine_no: element.engine_no,
                            chassis_no: element.chassis_no,
                            car_class_id: element.car_class_id,
                            full_name: element.full_name,
                            lot_no: element.lot_no,
                            status: element.status,
                            memo_no: element.memo_no,
                            receipt_avance: element.receipt_avance,
                            operation_fee_avance: element.operation_fee_avance,
                            total: total,
                        }

                        if (this.face_sheet_list.length > 0) {
                            car_ob_filter = this.face_sheet_list.filter(obj => obj.status === car_ob
                                .status);

                            car_ob_dup_filter = this.face_sheet_list.filter(obj => obj.id === car_ob
                                .id);
                            // console.log(car_ob_filter , this.face_sheet_list)
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
                sumTotal(index) {
                    // this.inputs[k].spare_parts = numeral(this.inputs[k].spare_parts).format('0,0.00');

                    var operation_fee_avance = numeral(this.face_sheet_list[index].operation_fee_avance).value();
                    var receipt_avance = numeral(this.face_sheet_list[index].receipt_avance).value();

                    if (!isNaN(receipt_avance) && !isNaN(operation_fee_avance)) {
                        console.log(receipt_avance, operation_fee_avance)
                        this.face_sheet_list[index].total = operation_fee_avance + receipt_avance;
                        this.face_sheet_list[index].total = numeral(this.face_sheet_list[index].total).format(
                            '0,0.00');
                    } else {
                        this.face_sheet_list[index].total = 0;
                    }
                    this.face_sheet_list[index].receipt_avance = numeral(this.face_sheet_list[index].receipt_avance)
                        .format('0,0.00');
                    this.face_sheet_list[index].operation_fee_avance = numeral(this.face_sheet_list[index]
                        .operation_fee_avance).format('0,0.00');


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
