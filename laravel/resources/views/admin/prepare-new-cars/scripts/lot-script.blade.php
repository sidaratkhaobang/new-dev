@push('scripts')
    <script>
        function openLotModal() {
            createLotVue.openLotModal();
        }

        function closeModalLot() {
            createLotVue.closeModal()
        }

        function addModalCarDataLot() {
            createLotVue.addModalCarData()
        }

        function submitModalCarLot() {
            createLotVue.submitModalCar()
        }

        let createLotVue = new Vue({
            el: '#modal-lot-vue',
            data: {
                lot_car_data: [],
            },
            methods: {
                openLotModal: function () {
                    $('#modal-lot').modal('show')
                    this.clearModalData()
                    this.clearModalCarData()
                },
                closeModal: function () {
                    $('#modal-lot').modal('hide')
                    this.clearModalData()
                    this.clearModalCarData()
                },
                clearModalData: function () {
                    $('#modal_po_no').val(null).trigger('change')
                    $('#modal_car').val(null).trigger('change')
                    $('#modal_delivery_date').val(null).trigger('change')
                    $('#modal_status').val(null).trigger('change')
                },
                clearModalCarData: function () {
                    this.lot_car_data = [];
                },
                addModalCarData: function () {
                    let po_no = $('#modal_po_no').val()
                    let car_id = $('#modal_car').val()
                    let delivery_date = $('#modal_delivery_date').val()
                    let status = $('#modal_status').val()
                    let id_cars = this.getCarId()
                    axios.get("{{ route('admin.prepare-new-cars.get-car-data') }}", {
                        params: {
                            po_no: po_no,
                            car_id: car_id,
                            delivery_date: delivery_date,
                            status: status,
                            id_cars: id_cars,
                        }
                    }).then(response => {
                        if (response.data.success) {
                            if (response.data.data.length > 0) {
                                var lot_car_data = this.lot_car_data;
                                response.data.data.forEach(function (item, index) {
                                    lot_car_data.push(item)
                                })
                            } else {
                                warningAlert("{{ __('maintenance_costs.no_data') }}");
                            }
                        }
                    });
                },
                submitModalCar: function () {
                    $('#modal-lot-detail').modal('show')
                    $('#modal-lot').modal('hide')
                    detailLotVue.setModalCarData(this.lot_car_data)
                },
                getCarId: function () {
                    let id_cars = [];
                    this.lot_car_data.forEach(function (item, index) {
                        if (item.id) {
                            id_cars.push(item.id)
                        }
                    })
                    return id_cars;
                }
            },
            props: ['title'],
        });
    </script>
@endpush
