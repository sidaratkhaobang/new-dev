@push('scripts')
    <script>
        let addModalExportExcelVue = new Vue({
            el: '#export-excel',
            data: {
                modal_export_car_data : [],
            },
            methods: {
                alertWaiting(){
                    warningAlert("{{ __('finance_request.alert_warning') }}");
                }
            },
            props: ['title'],
        });
    </script>
@endpush
