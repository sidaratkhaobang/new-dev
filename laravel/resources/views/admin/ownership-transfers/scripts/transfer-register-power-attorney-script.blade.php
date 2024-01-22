@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        let addTransferVue = new Vue({
            el: '#transfer-register-power-attorney',
            data: {
                transfer_list: @if (isset($transfer_list))
                    @json($transfer_list)
                @else
                    []
                @endif ,
                edit_index: null,
                total_car: 0,
                mode: null,
            },
            methods: {
                display: function() {},
                addCar: function(data) {
                    data.forEach((element) => {
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
                            operation_fee_avance: element.operation_fee_avance,
                            creditor_name: element.creditor_name,
                            creditor_id: element.creditor_id,
                            engine_size: element.engine_size,
                        }

                        if (this.transfer_list.length > 0) {
                            car_ob_filter = this.transfer_list.filter(obj => obj.status === car_ob
                                .status);

                            car_ob_leasing_filter = this.transfer_list.filter(obj => obj.creditor_id === car_ob
                                .creditor_id);

                            car_ob_dup_filter = this.transfer_list.filter(obj => obj.id === car_ob
                                .id);
                            if (car_ob_filter.length > 0 && car_ob_leasing_filter.length > 0) {
                                if (car_ob_dup_filter.length > 0) {
                                    warningAlert("{{ __('registers.validate_car_duplicate') }}");
                                } else {
                                    this.transfer_list.push(car_ob);
                                }
                            } else {
                                warningAlert("{{ __('registers.validate_status_or_leasing') }}");
                            }
                        } else {
                            this.transfer_list.push(car_ob);
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
                    this.transfer_list.splice(index, 1);
                },

            },
            props: ['title'],
        });
    </script>
@endpush
