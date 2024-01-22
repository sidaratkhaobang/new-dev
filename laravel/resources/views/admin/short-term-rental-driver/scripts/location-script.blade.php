@push('scripts')
    <script>
        let addLocationVue = new Vue({
            el: '#location-vue',
            data: {
                location_data: [],
                car_data: @json($cars) ,
                modal_car_key: null,
                modal_location_key: null,
                del_input_id: [],
            },
            methods: {
                addStopOverData: function (arr_key) {
                    let data = {
                        id: null,
                        location_id: null,
                        location_name: null,
                        arrived_at: null,
                        departured_at: null,
                        lat: null,
                        lng: null,
                    }
                    this.car_data[arr_key].rental_checkins.push(data)
                },
                remove(car_k, stopover_k) {
                    if (this.car_data[car_k].rental_checkins[stopover_k].id) {
                        this.del_input_id.push(this.car_data[car_k].rental_checkins[stopover_k].id)
                    }
                    this.car_data[car_k].rental_checkins.splice(stopover_k, 1)

                },
                openModalAddLocation: function (car_key, location_key) {
                    this.modal_car_key = car_key
                    this.modal_location_key = location_key
                    this.modalLocationLoadData()
                    $('#location-modal').modal('show');
                },
                modalLocationLoadData() {
                    let car_key = this.modal_car_key
                    let location_key = this.modal_location_key
                    if (this.car_data[car_key].rental_checkins[location_key].location_name) {
                        $('#origin_name_temp').val(this.car_data[car_key].rental_checkins[location_key].location_name)

                    }
                    if (this.car_data[car_key].rental_checkins[location_key].lat) {
                        $('#origin_lat_temp').val(this.car_data[car_key].rental_checkins[location_key].lat)
                    }
                    if (this.car_data[car_key].rental_checkins[location_key].lng) {
                        $('#origin_lng_temp').val(this.car_data[car_key].rental_checkins[location_key].lng)
                    }
                },
                saveModalLocation: function () {
                    let location_name = $('#origin_name_temp').val()
                    let lat = $('#origin_lat_temp').val()
                    let lng = $('#origin_lng_temp').val()
                    let car_key = this.modal_car_key
                    let location_key = this.modal_location_key
                    this.car_data[car_key].rental_checkins[location_key].location_name = location_name
                    this.car_data[car_key].rental_checkins[location_key].lat = lat
                    this.car_data[car_key].rental_checkins[location_key].lng = lng
                    $(`#select_${car_key}_${location_key}`).append((new Option(location_name, location_name, true, true))).trigger('change');
                    this.clearModalLocation()
                },
                clearModalLocation: function () {
                    $('#origin_name_temp').val(null)
                    $('#origin_lat_temp').val(null)
                    $('#origin_lng_temp').val(null)
                    this.modal_car_key = null
                    this.modal_location_key = null
                    this.closeModalLocation()
                },
                closeModalLocation: function () {
                    $('#location-modal').modal('hide');
                },
            },
            props: ['title'],
        });

        function addStopOverData() {
            addLocationVue.addStopOverData()
        }

        function saveModalLocation() {
            addLocationVue.saveModalLocation()
        }

        $('#location-modal').on('hidden.bs.modal', function () {
            $('#origin_name_temp').val(null)
            $('#origin_lat_temp').val(null)
            $('#origin_lng_temp').val(null)
            addLocationVue.modal_car_key = null
            addLocationVue.modal_location_key = null
        });
    </script>

@endpush
