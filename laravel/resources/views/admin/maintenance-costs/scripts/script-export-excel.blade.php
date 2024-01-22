@push('scripts')
    <script>
        function addExcelExportData() {
            addModalExportExcelVue.addExcelExportData();
        }

        function clearModalExportSearch() {
            addModalExportExcelVue.clearModalExportSearch();
        }

        function exportExcel() {
            let repair_list_id = addModalExportExcelVue.getRepairListId();
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.maintenance-cost.export-excel') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    repair_list_id: repair_list_id,
                },
                success: function (result, status, xhr) {
                    var fileName = 'ใบแจ้งหนี้ศูนย์ให้บริการ.xlsx';
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
                error: function (result, status, xhr) {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูล',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    });
                }
            });
        }

        let addModalExportExcelVue = new Vue({
            el: '#export-excel',
            data: {
                export_excel_list: [],
            },
            mounted: {},
            watch: {},
            methods: {
                addExcelExportData: function () {
                    let worksheet_no = $('#modal_worksheet_no').val();
                    let center = $('#modal_center').val();
                    let geographie = $('#modal_geographie').val();
                    let car = $('#modal_car').val();
                    let invoice_no = $('#modal_invoice_no').val();
                    let center_date = $('#modal_in_center_date').val();
                    let end_date = $('#modal_end_date').val();
                    let status = $('#modal_status').val();
                    let repair_list_id = this.getRepairListId();
                    axios.get("{{ route('admin.maintenance-cost.excel-data') }}", {
                        params: {
                            worksheet_no: worksheet_no,
                            center: center,
                            geographie: geographie,
                            car: car,
                            invoice_no: invoice_no,
                            center_date: center_date,
                            end_date: end_date,
                            status: status,
                            repair_list_id: repair_list_id,
                        }
                    }).then(response => {
                        if (response.data.success) {
                            if (response.data.data.length > 0) {
                                var export_excel_list = this.export_excel_list;
                                response.data.data.forEach(function (item, index) {
                                    export_excel_list.push(item)
                                })
                            } else {
                                warningAlert("{{ __('maintenance_costs.no_data') }}");
                            }
                        }
                    });
                },
                clearModalExportSearch: function () {
                    $('#modal_worksheet_no').val(null).trigger('change')
                    $('#modal_center').val(null).trigger('change')
                    $('#modal_geographie').val(null).trigger('change')
                    $('#modal_car').val(null).trigger('change')
                    $('#modal_invoice_no').val(null).trigger('change')
                    $('#modal_in_center_date').val(null).trigger('change')
                    $('#modal_end_date').val(null).trigger('change')
                    $('#modal_status').val(null).trigger('change')
                    this.export_excel_list = [];
                },
                getRepairListId: function () {
                    let repair_id = [];
                    this.export_excel_list.forEach(function (item) {
                        if (item.id) {
                            repair_id.push(item.id)
                        }
                    })
                    return repair_id;
                },
                removeRepairList: function (index) {
                    this.export_excel_list.splice(index, 1)
                }
            },
            props: ['title'],
        });
    </script>
@endpush
