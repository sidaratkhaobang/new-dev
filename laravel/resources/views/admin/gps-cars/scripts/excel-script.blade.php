@push('scripts')
    <script>
        function openExcelModal() {
            $("#excel_type_id").val('').trigger('change');
            $("#from_install_date").val('');
            $("#to_install_date").val('');
            $("#from_revoke_date").val('');
            $("#to_revoke_date").val('');
            document.getElementById("install_date").style.display = "none"
            document.getElementById("revoke_date").style.display = "none"
            $("#excel-modal").modal("show");
        }

        $(".btn-hide-excel").on("click", function() {
            $('#excel-modal').modal('hide');
        });

        $('#excel_type_id').on('select2:select', function(e) {
            var excel_type_id = document.getElementById("excel_type_id").value;
            if (excel_type_id === '2') {
                document.getElementById("install_date").style.display = "block"
            } else if (excel_type_id === '3') {
                document.getElementById("revoke_date").style.display = "block"
            } else {
                document.getElementById("install_date").style.display = "none"
                document.getElementById("revoke_date").style.display = "none"
            }
        });

        function exportExcel() {
            var excel_type_id = document.getElementById("excel_type_id").value;
            var from_install_date = document.getElementById("from_install_date").value;
            var to_install_date = document.getElementById("to_install_date").value;
            var from_revoke_date = document.getElementById("from_revoke_date").value;
            var to_revoke_date = document.getElementById("to_revoke_date").value;
            if (!excel_type_id) {
                return warningAlert("{{ __('lang.required_field_inform') }}");
            }
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.gps-cars.export-excel') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    excel_type_id: excel_type_id,
                    from_install_date: from_install_date,
                    to_install_date: to_install_date,
                    from_revoke_date: from_revoke_date,
                    to_revoke_date: to_revoke_date,
                },
                success: function(result, status, xhr) {
                    if (excel_type_id === '1') {
                        var fileName = 'รถทั้งหมดในระบบ GPS.xlsx';
                    } else if (excel_type_id === '2') {
                        var fileName = 'รถทั้งหมดในระบบที่มีการติดตั้ง GPS.xlsx';
                    } else if (excel_type_id === '3') {
                        var fileName = 'รถทั้งหมดในระบบที่มีการหยุดสัญญาณ GPS.xlsx';
                    } else if (excel_type_id === '4') {
                        var fileName = 'ค่าบริการ GPS และ DVR.xlsx';
                    }

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
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
            $("#excel-modal").modal("hide");
        }
    </script>
@endpush
