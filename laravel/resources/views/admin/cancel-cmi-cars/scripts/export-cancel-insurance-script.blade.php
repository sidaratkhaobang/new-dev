@push('scripts')
    <script>
        $(document).ready(function() {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function() {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function(e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });
        async function exportCancelInsuranceList(file_name) {
            var checked_ids = [];
            $('input[name="row_checkbox"]:checked').each(function() {
                checked_ids.push($(this).val());
            });
            var export_url = "{{ route('admin.cancel-cmi-cars.export-cancel-insurances') }}";
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: export_url,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    ids: checked_ids,
                },
                success: function(result, status, xhr) {
                    let today = new Date().toISOString().slice(0, 10);
                    var fileName = file_name + today + '.xlsx';
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
