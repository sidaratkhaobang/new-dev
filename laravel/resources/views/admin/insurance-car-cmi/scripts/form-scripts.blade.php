@push('scripts')
    <script>
        window.tableFileUpload = new window.Vue({
            el: '#vue-item',
            data: {
                accessory_data_list: [],
                cmi_id: '{{$d->id}}'
            },
            methods: {
                // Modal Accessory
                addCarAccessoryData(data) {
                    this.accessory_data_list = data
                },
                fetchCarAccessoryData() {
                    let url_get_accessory = `{{route('admin.insurance-car-cmi.car-accessory-list-data')}}`
                    axios.post(url_get_accessory, {cmi_id: this.cmi_id})
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
