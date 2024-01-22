@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        let addAvanceVue = new Vue({
            el: '#avance',
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
                addCar: function(data) {
                    // var data = e.params.data;
                    data.forEach((element) => {
                        var total = parseFloat(element.receipt_avance) + parseFloat(element
                            .operation_fee_avance);
                        total = total.toFixed(2)
                        var car_ob = {
                            id: element.id,
                            car_id: element.car_id,
                            license_plate: element.license_plate,
                            engine_no: element.engine_no,
                            chassis_no: element.chassis_no,
                            car_class_id: element.car_class_id,
                            full_name: element.full_name,
                            actual_last_payment_date: element.actual_last_payment_date,
                            status: element.status,
                            memo_no: element.memo_no,
                            receipt_avance: element.receipt_avance,
                            operation_fee_avance: element.operation_fee_avance,
                            creditor_name: element.creditor_name,
                            engine_size: element.engine_size,
                            total: total,
                        }

                        if (this.face_sheet_list.length > 0) {
                            car_ob_filter = this.face_sheet_list.filter(obj => obj.status === car_ob
                                .status);

                            car_ob_dup_filter = this.face_sheet_list.filter(obj => obj.id === car_ob
                                .id);
                            if (car_ob_filter.length > 0) {
                                if (car_ob_dup_filter.length > 0) {
                                    warningAlert("{{ __('registers.validate_car_duplicate') }}");
                                } else {
                                    this.face_sheet_list.push(car_ob);
                                    // addAvanceSelectedVue.face_sheet_list.push(car_ob);
                                }
                            } else {
                                warningAlert("{{ __('registers.validate_status') }}");
                            }
                        } else {
                            this.face_sheet_list.push(car_ob);
                            // addAvanceSelectedVue.face_sheet_list.push(car_ob);
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
            
                removeCar: function(index) {
                    this.face_sheet_list.splice(index, 1);
                },
             
            },
            props: ['title'],
        });
    
    </script>
@endpush