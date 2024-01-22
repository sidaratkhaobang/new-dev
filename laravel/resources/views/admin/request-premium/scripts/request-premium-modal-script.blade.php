@push('scripts')
    <script>
        window.tableFileUpload = new window.Vue({
            el: '#table-accessory-car',
            data: {
                data_list: []
            },
            methods: {
                addItem(data) {

                    this.data_list = data
                },
            },
            props: ['title'],
        });

        function ModalAccessory(lt_rental_line_id = null) {
            if (lt_rental_line_id) {
                axios.post("{{ route('admin.request-premium-accessory-list') }}", {lt_rental_line_id: lt_rental_line_id})
                    .then(response => {
                        if (response.res_code = 200) {
                            if (response.data.res_data) {
                                window.tableFileUpload.addItem(response.data.res_data)
                            } else {
                                window.tableFileUpload.addItem([])

                            }
                        } else {
                            window.tableFileUpload.addItem([])

                        }
                    });
            }
        }
    </script>
@endpush
