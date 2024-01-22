@push('scripts')
    <script>
        $(document).ready(function() {
            $('#upload').on('change', function(e) {
                const file = e.target.files[0];
                uploadFile(file);
            });
        });
        insurance_type = ' {{ $insurance_type ?? '' }}';
        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('insurance_type', insurance_type);
            $upload_url = "{{ route('admin.cancel-cmi-cars.import-cancel-insurances') }}";
            showLoading();
            axios.post($upload_url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
            .then(response => {
                log = response.data.data;
                hideLoading();
                if (log.length > 0) {
                    alert_text = log.join('<br>');
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                        html: alert_text,
                    }).then(value => {
                        window.location.reload();
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        window.location.reload();
                    });
                }
            })
            .catch(error => {
                hideLoading();
                errorAlert(error.response.data.message);
            });
        }
    </script>
@endpush
