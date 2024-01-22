@push('scripts')
    <script>
        async function openInsureListModal() {
            var checked_ids = [];
            $('input[name="row_checkboxes"]:checked').each(function () {
                checked_ids.push($(this).val());
            });
            insureListVue.add(checked_ids);
            // await setLotNumber();
            $('#modal-insure').modal('show');
            $('#_insure_year').val('1');
            $('#_insure_year').prop('disabled', true);
        }

        // function setLotNumber() {
        //     axios.get("{{ route('admin.cmi-cars.lot-number') }}", {
        //         params: {}
        //     }).then(response => {
        //         if (response.data.success) {
        //             let lot_no = response.data.data;
        //             $('#_lot_no').prop('disabled', true);
        //             $('#_lot_no').val(lot_no);
        //         }
        //     });
        // }

        let insureListVue = new Vue({
            el: '#modal-insure-display',
            data: {
                car_list: @if (isset($lists)) @json($lists) @else [] @endif,
                edit_index: null,
                selected_list: []
            },
            methods: {
                display: function (id, index) {
                },
                add: function (_array) {
                    this.selected_list = [];
                    _array.forEach(element => {
                        const object = this.car_list.data.find(o => o.id === element);
                        this.selected_list.push(object);
                    });
                },
                remove: function (index) {
                    this.selected_list.splice(index, 1);
                },
                setIndex: function () {
                    this.edit_index = null;
                },
                createNewInsureGroup: function () {
                    if (this.validate()) {
                        this.callCreateNewCMIs();
                    }
                },
                validate: function () {
                    var lot_no = $('#_lot_no').val();
                    if (!lot_no) {
                        warningAlert("{{ __('lang.field_required') . __('import_cars.lot_no') }}");
                        return false;
                    }
                    for (let i = 0; i < this.selected_list.length; i++) {
                        if (!this.selected_list[i].registration_type) {
                            warningAlert("{{ __('lang.field_required') . __('import_cars.registration_type') }}");
                            return false;
                        }
                    }
                    return true;
                },
                callCreateNewCMIs: async function () {
                    var lot_no = $('#_lot_no').val();
                    var leasing_id = $('#leasing_id').val();
                    var data = {
                        cars: this.selected_list,
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

        $('#modal-insure-display').on('hide.bs.dropdown', function () {
            $('#_lot_no').val('');
        })
    </script>
@endpush
