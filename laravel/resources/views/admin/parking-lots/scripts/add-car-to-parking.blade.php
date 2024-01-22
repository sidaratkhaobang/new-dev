@push('scripts')
<script>
    $(document).ready(() => {
        // add
        $('.btn-modal-add-car-to-parking').on('click', () => {
            clearForm('#modal-add-car-to-parking');
            $('#modal-add-car-to-parking').modal('show');
        });

        $('.btn-save-add-car-to-parking').on('click', () => {
            var formData = new FormData(document.querySelector('#form-modal-add-car-to-parking'));
            axios.post("{{ route('admin.car-park.add-car-to-parking') }}", formData).then(response => {
                if (response.data.success) {
                    console.log(response.data.data);
                    $('#modal-add-car-to-parking').modal('hide');
                    clearForm('#modal-add-car-to-parking');
                } else {
                    errorAlert(error.response.data.message);
                }
            }).catch(error => {
                errorAlert(error.response.data.message);
            });
        });

        // remove
        $('.btn-modal-remove-car-from-parking').on('click', () => {
            clearForm('#modal-remove-car-from-parking');
            $('#modal-remove-car-from-parking').modal('show');
        });

        $('.btn-save-remove-car-from-parking').on('click', () => {
            var formData = new FormData(document.querySelector('#form-modal-remove-car-from-parking'));
            axios.post("{{ route('admin.car-park.remove-car-from-parking') }}", formData).then(response => {
                if (response.data.success) {
                    console.log(response.data.data);
                    $('#modal-remove-car-from-parking').modal('hide');
                    clearForm('#modal-remove-car-from-parking');
                } else {
                    errorAlert(error.response.data.message);
                }
            }).catch(error => {
                errorAlert(error.response.data.message);
            });
        });
    });
</script>
@endpush