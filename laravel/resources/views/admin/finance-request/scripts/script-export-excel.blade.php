@push('scripts')
    <script>
        function openModalExcel() {
            addModalExportExcelVue.openModalExcel();
        }

        function addModalFinanceCarData() {
            addModalExportExcelVue.addModalFinanceCarData();
        }

        function clearModalFilter() {
            addModalExportExcelVue.clearModalFilter();
        }

        function ajaxDownloadExcel(url, file_name = 'template.xlsx', data = []) {
            if (!url) {
                return false
            }
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: data,
                success: function (result, status, xhr) {
                    var fileName = file_name;
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

        function exportExcel() {
            let finance_lot_ids = addModalExportExcelVue.getFinanceCarLotId();
            let data = {
                finance_lot_ids: finance_lot_ids,
            }
            ajaxDownloadExcel("{{ route('admin.finance-request.finance-request-export-excel') }}", 'FOD_แจ้งวันวางบิลและชำระเงิน.xlsx', data)
            ajaxDownloadExcel("{{ route('admin.finance-request.finance-request-export-dealer-excel') }}", 'Dealer_แจ้งวันจ่ายเงินและชื่อไฟแนนซ์.xlsx', data)
        }

        let addModalExportExcelVue = new Vue({
            el: '#modal-export-excel-vue',
            data: {
                modal_export_car_data: [],
            },
            methods: {
                openModalExcel: function () {
                    $('#modal-export-excel').modal('show')
                    this.clearModalData();
                },
                clearModalFilter() {
                    $('#modal_lot_no').val(null).trigger('change')
                    $('#modal_rental').val(null).trigger('change')
                    $('#modal_date_create').val(null).trigger('change')
                    $('#modal_status').val(null).trigger('change')

                },
                clearModalData: function () {
                    this.modal_export_car_data = [];
                },
                addModalFinanceCarData: function () {
                    let lot_no = $('#modal_lot_no').val()
                    let rental = $('#modal_rental').val()
                    let date_create = $('#modal_date_create').val()
                    let status = $('#modal_status').val()
                    let finance_car_lot_id = this.getFinanceCarLotId();

                    axios.get("{{ route('admin.util.select2-finance.finance-request-get-excel-data') }}", {
                        params: {
                            lot_no: lot_no,
                            rental: rental,
                            date_create: date_create,
                            status: status,
                            finance_car_lot_id: finance_car_lot_id,
                        }
                    }).then(response => {
                        if (response.data.success) {
                            if (response.data.data.length > 0) {
                                var modal_export_car_data = this.modal_export_car_data;
                                response.data.data.forEach(function (item, index) {
                                    modal_export_car_data.push(item)
                                })
                            } else {
                                warningAlert("{{ __('maintenance_costs.no_data') }}");
                            }
                        }
                    });
                },
                removeModalFinanceCarData: function (index) {
                    this.modal_export_car_data.splice(index, 1)
                },
                getFinanceCarLotId: function () {
                    let finance_car_lot_ids = [];
                    this.modal_export_car_data.forEach(function (item) {
                        if (item.lot_id) {
                            finance_car_lot_ids.push(item.lot_id);
                        }
                    })
                    return finance_car_lot_ids;
                },
                alertWaiting() {
                    warningAlert("{{ __('finance_request.alert_warning') }}");
                }
            },
            props: ['title'],
        });
    </script>
@endpush
