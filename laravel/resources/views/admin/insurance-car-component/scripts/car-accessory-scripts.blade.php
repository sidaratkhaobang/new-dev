@push('scripts')
    <script>
        window.car_accessory = new window.Vue({
            el: '#car-accessory',
            data: {
                accessory_data_list: [],
                id: '{{$d->id}}'
            },
            methods: {
                // Modal Accessory
                addCarAccessoryData(data) {
                    this.accessory_data_list = data
                },
                fetchCarAccessoryData(type) {
                    let url_get_accessory = `{{route('admin.insurance-car.car-accessory-list-data')}}`
                    axios.post(url_get_accessory, {id: this.id,type:type})
                        .then(response => {
                            if (response.data) {
                                this.addCarAccessoryData(response.data);
                            } else {
                                this.addCarAccessoryData([]);
                            }
                        })
                        .catch(error => {
                            this.addCarAccessoryData([]);
                            console.error(error);
                        });
                },
            },
            props: ['title'],
        });

    </script>
@endpush
