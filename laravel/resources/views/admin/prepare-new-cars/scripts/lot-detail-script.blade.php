@push('scripts')
    <script>
        function backLotDetailModal() {
            detailLotVue.backModal();
        }

        function closeModalLotDetail() {
            detailLotVue.closeModal();
        }

        function submitModalLotDetail() {
            detailLotVue.submitModal();
        }

        let detailLotVue = new Vue({
            el: '#modal-lot-detail-vue',
            data: {
                car_data: [],
            },
            methods: {
                setModalCarData: function (car_data) {
                    this.car_data = [];
                    if (car_data) {
                        this.car_data = car_data;
                    }
                },
                backModal: function () {
                    $('#modal-lot').modal('show');
                    $('#modal-lot-detail').modal('hide');
                    this.clearModalData();
                },
                closeModal: function () {
                    $('#modal-lot-detail').modal('hide');
                    this.clearModalData();
                    createLotVue.closeModal();
                },
                clearModalData: function () {
                    this.car_data = [];
                },
                submitModal: function () {
                    let validate = this.validate();
                    if (validate == true) {
                        this.callCreateNewLot();
                    }

                },
                validate: function () {
                    var lot_no = $('#_lot_no').val();
                    var leasing_id = $('#leasing_id').val()
                    if (!lot_no) {
                        warningAlert("{{ __('lang.field_required') . __('import_cars.lot_no') }}");
                        return false;
                    }
                    if (!leasing_id) {
                        warningAlert("{{ __('lang.field_required') . __('import_cars.leasing') }}");
                        return false;
                    }
                    {{--for (let i = 0; i < this.car_data.length; i++) {--}}
                    {{--    if (!this.car_data[i].registration_type) {--}}
                    {{--        warningAlert("{{ __('lang.field_required') . __('import_cars.registration_type') }}");--}}
                    {{--        return false;--}}
                    {{--    }--}}
                    {{--}--}}
                        return true;
                },
                callCreateNewLot: async function () {
                    var lot_no = $('#_lot_no').val();
                    var leasing_id = $('#leasing_id').val();
                    var data = {
                        cars: this.car_data,
                        lot_no: lot_no,
                        leasing_id: leasing_id,
                    };
                    var updateUri = "{{ route('admin.cmi-cars.create-cmi-cars') }}";
                    await axios.post(updateUri, data).then(response => {
                        if (response.data.success) {
                            mySwal.fire({
                                title: "{{ __('lang.store_success_title') }}",
                                text: "{{ __('lang.store_success_message') }}",
                                icon: 'success',
                                confirmButtonText: "{{ __('lang.ok') }}"
                            }).then(value => {
                                $('#modal-insure-display').modal('hide');
                                window.location.reload();
                            });
                        } else {
                            errorAlert(response.data.message);
                        }
                    }).catch(error => {
                        errorAlert(error.response.data.message);
                    });
                }
            },
            props: ['title'],
        });
    </script>
@endpush
