@push('scripts')
    <script>
        async function exportVMIList() {
            var checked_ids = [];
            $('input[name="row_checkbox"]:checked').each(function() {
                checked_ids.push($(this).val());
            });

            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.insurance-vmi-renew.export-vmis') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ids: checked_ids,
                },
                success: function(result, status, xhr) {
                    var fileName = 'ข้อมูลประกัน.xlsx';
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
