@push('scripts')
    <script>
        async function exportCMIList() {
            var checked_ids = [];
            $('input[name="row_checkbox"]:checked').each(function() {
                checked_ids.push($(this).val());
            });
            var export_url = "{{ route('admin.cmi-cars.export-cmis') }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.cmi-cars.export-cmis') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ids: checked_ids,
                },
                success: function(result, status, xhr) {
                    let today = new Date().toISOString().slice(0, 10);
                    var fileName = 'ข้อมูลพรบ' + today + '.xlsx';
                    var blob = new Blob([result], {
                        type: 'text/csv;charset=utf-8'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = fileName;

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(result, status, xhr) {
                    errorAlert("{{ __('lang.not_found') }}");
                }
            });
            
        }
    </script>
@endpush