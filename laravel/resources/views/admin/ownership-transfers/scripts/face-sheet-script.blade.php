@push('scripts')
    <script>
        let addFaceSheetVue = new Vue({
            el: '#face-sheet',
            data: {
                face_sheet_list: @if (isset($face_sheet_list))
                    @json($face_sheet_list)
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
                            // lot_no: element.lot_no,
                            status: element.status,
                            creditor_name: element.creditor_name,
                            actual_last_payment_date: element.actual_last_payment_date,
                            license_plate: element.license_plate,
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
                removeCar: function(index) {
                    this.face_sheet_list.splice(index, 1);
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
        addFaceSheetVue.display();

        function addCar() {
            addFaceSheetVue.addCar();
        }
     
    </script>
@endpush
