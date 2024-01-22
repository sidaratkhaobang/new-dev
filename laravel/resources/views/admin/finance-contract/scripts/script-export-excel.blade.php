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

        function exportExcel() {
            let finance_car_ids = addModalExportExcelVue.getFinanceCarId();
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                type: 'GET',
                url: "{{ route('admin.finance-contract.finance-contract-export-excel') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    finance_car_ids: finance_car_ids,
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
            el: '#modal-export-excel-vue',
            data: {
                modal_export_car_data: [],
            },
            methods: {
                openModalExcel: function () {
                    $('#export-excel-modal').modal('show')
                    this.clearModalData();
                },
                clearModalFilter() {
                    $('#modal_lot_no').val(null).trigger('change')
                    $('#modal_rental').val(null).trigger('change')
                    $('#modal_date_create').val(null).trigger('change')
                    $('#modal_status').val(null).trigger('change')
                    this.clearModalData()
                },
                clearModalData: function () {
                    this.modal_export_car_data = [];
                },
                addModalFinanceCarData: function () {
                    let lot_no = $('#modal_lot_no').val()
                    let rental = $('#modal_rental').val()
                    let date_create = $('#modal_date_create').val()
                    let status = $('#modal_status').val()
                    let car_id = $('#modal_car').val()
                    let contract_no = $('#modal_contract_no').val()
                    let contract_start_date = $('#modal_contract_start_date').val()
                    let contract_end_date = $('#modal_contract_end_date').val()
                    let finance_car_id = this.getFinanceCarId()

                    axios.get("{{ route('admin.util.select2-finance.finance-contract-get-excel-data') }}", {
                        params: {
                            lot_no: lot_no,
                            rental: rental,
                            date_create: date_create,
                            status: status,
                            finance_car_id: finance_car_id,
                            car_id: car_id,
                            contract_no: contract_no,
                            contract_start_date: contract_start_date,
                            contract_end_date: contract_end_date,
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
                getFinanceCarId: function () {
                    let finance_car_ids = [];
                    this.modal_export_car_data.forEach(function (item) {
                        if (item.car_id) {
                            finance_car_ids.push(item.car_id);
                        }
                    })
                    return finance_car_ids;
                },
                alertWaiting() {
                    warningAlert("{{ __('finance_request.alert_warning') }}");
                }
            },
            props: ['title'],
        });
    </script>
@endpush
