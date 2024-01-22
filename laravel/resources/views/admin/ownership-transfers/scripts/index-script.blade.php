@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'contract_no_search',
    'url' => route('admin.util.select2-ownership-transfer.contract-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'facesheet_status',
    'url' => route('admin.util.select2-ownership-transfer.get-status-facesheet'),
    'modal' => '#face-sheet-modal',
])

@include('admin.components.select2-ajax', [
    'id' => 'leasing_search',
    'url' => route('admin.util.select2-ownership-transfer.leasing-list'),
])

@php
    $modals = [
        'face-sheet-select-car-modal' => ['leasing', 'lot_no', 'car_class', 'car_id', 'status'],
        'avance-select-car-modal' => ['leasing_avance', 'lot_no_avance', 'car_class_avance', 'car_id_avance', 'status_avance'],
        'template-select-car-modal' => ['leasing_template', 'lot_no_template', 'car_class_template', 'car_id_template', 'status_template'],
        'attorney-select-car-modal' => ['leasing_attorney', 'lot_no_attorney', 'car_class_attorney', 'car_id_attorney', 'status_attorney'],
        'transfer-register-power-attorney-select-car-modal' => ['leasing_transfer', 'lot_no_transfer', 'car_class_transfer', 'car_id_transfer', 'status_transfer'],
    ];

    $url_mapping = [
        'leasing' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_avance' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_template' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_attorney' => 'admin.util.select2-finance.creditor-leasing-list',
        'leasing_transfer' => 'admin.util.select2-finance.creditor-leasing-list',
        'lot_no' => 'admin.util.select2-register.lot-list',
        'lot_no_avance' => 'admin.util.select2-register.lot-list',
        'lot_no_template' => 'admin.util.select2-register.lot-list',
        'lot_no_attorney' => 'admin.util.select2-register.lot-list',
        'lot_no_transfer' => 'admin.util.select2-register.lot-list',
        'car_class' => 'admin.util.select2-register.car-class-list',
        'car_class_avance' => 'admin.util.select2-register.car-class-list',
        'car_class_template' => 'admin.util.select2-register.car-class-list',
        'car_class_attorney' => 'admin.util.select2-register.car-class-list',
        'car_class_transfer' => 'admin.util.select2-register.car-class-list',
        'car_id' => 'admin.util.select2-register.license-plate-list',
        'car_id_avance' => 'admin.util.select2-register.license-plate-list',
        'car_id_template' => 'admin.util.select2-register.license-plate-list',
        'car_id_attorney' => 'admin.util.select2-register.license-plate-list',
        'car_id_transfer' => 'admin.util.select2-register.license-plate-list',
        'status' => 'admin.util.select2-ownership-transfer.get-status',
        'status_avance' => 'admin.util.select2-ownership-transfer.get-status',
        'status_template' => 'admin.util.select2-ownership-transfer.get-status',
        'status_attorney' => 'admin.util.select2-ownership-transfer.get-status',
        'status_transfer' => 'admin.util.select2-ownership-transfer.get-status',
    ];
@endphp

@foreach ($modals as $modal => $ids)
    @foreach ($ids as $id)
        @include('admin.components.select2-ajax', [
            'id' => $id,
            'url' => route($url_mapping[$id]),
            'modal' => '#' . $modal,
        ])
    @endforeach
@endforeach

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script>
        function openModalFaceSheetSelectCar() {
            $('#status').val('').change();
            $('#lot_no').val('').change();
            $('#leasing').val('').change();
            $('#car_id').val('').change();
            $('#car_class').val('').change();
            addFaceSheetVue.face_sheet_list = [];
            $('#face-sheet-select-car-modal').modal('show');
        }

        function openModalFaceSheet() {
            $('#facesheet_status').val('').change();
            $('#topic_face_sheet').val('').change();
            $('#face-sheet-select-car-modal').modal('hide');
            $('#face-sheet-modal').modal('show');
        }

        function openModalTemplateSelectCar() {
            $('#status_template').val('').change();
            $('#lot_no_template').val('').change();
            $('#leasing_template').val('').change();
            $('#car_id_template').val('').change();
            $('#car_class_template').val('').change();
            addFaceSheetVue.face_sheet_list = [];
            $('#template-select-car-modal').modal('show');
        }

        function openModalAvanceSelectCar() {
            addAvanceSelectedVue.face_sheet_list = [];
            addAvanceVue.face_sheet_list = [];
            $('#status_avance').val('').change();
            $('#lot_no_avance').val('').change();
            $('#leasing_avance').val('').change();
            $('#car_id_avance').val('').change();
            $('#car_class_avance').val('').change();
            $('#avance-select-car-modal').modal('show');
        }

        function clearSelectCar() {
            $('#status').val('').change();
            $('#lot_no').val('').change();
            $('#leasing').val('').change();
            $('#car_id').val('').change();
            $('#car_class').val('').change();
            $('#status_template').val('').change();
            $('#lot_no_template').val('').change();
            $('#leasing_template').val('').change();
            $('#car_id_template').val('').change();
            $('#car_class_template').val('').change();
            $('#status_avance').val('').change();
            $('#lot_no_avance').val('').change();
            $('#leasing_avance').val('').change();
            $('#car_id_avance').val('').change();
            $('#car_class_avance').val('').change();
            $('#month_last_payment_avance').val('').change();
            $('#month_last_payment').val('').change();
            $('#month_last_payment_template').val('').change();
            $('#status_attorney').val('').change();
            $('#lot_no_attorney').val('').change();
            $('#leasing_attorney').val('').change();
            $('#month_last_payment_attorney').val('').change();
            $('#car_id_transfer').val('').change();
            $('#status_transfer').val('').change();
            $('#lot_no_transfer').val('').change();
            $('#leasing_transfer').val('').change();
            $('#month_last_payment_transfer').val('').change();
            $('#car_id_transfer').val('').change();
        }

        function openModalAvance() {
            addAvanceSelectedVue.face_sheet_list = [];
            addAvanceSelectedVue.face_sheet_list = addAvanceVue.face_sheet_list;
            $('#avance-select-car-modal').modal('hide');
            $('#avance-modal').modal('show');
        }

        function BackToModalFaceSheetSelectCar() {
            $('#face-sheet-modal').modal('hide');
            $('#face-sheet-select-car-modal').modal('show');
        }

        function BackToModalAvanceSelectCar() {
            // addAvanceSelectedVue.face_sheet_list = [];
            $('#avance-modal').modal('hide');
            $('#avance-select-car-modal').modal('show');
        }

        var ExcelToJSON = function() {

            this.parseExcel = function(file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {
                        type: 'binary',
                        blankrows: false
                    });
                    var json_object = [];
                    workbook.SheetNames.forEach(function(sheetName) {
                        var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[
                            sheetName]);
                        json_object = JSON.stringify(XL_row_object);
                    })

                    $.ajax({
                        type: 'GET',
                        url: "{{ route('admin.ownership-transfers.import-excel') }}",
                        data: {
                            json_object: JSON.parse(json_object),
                        },
                        success: function(data) {
                            if (data.success) {
                                addTemplateImportVue.importData(data);
                            } else {
                                return warningAlert("{{ __('registers.validate_import') }}");
                            }
                        },

                    });
                };

                reader.onerror = function(ex) {
                    // 
                };

                reader.readAsBinaryString(file);
            };
        };

        function handleFileSelect(evt) {

            var files = evt.target.files;
            var xl2json = new ExcelToJSON();
            xl2json.parseExcel(files[0]);
        }

        document.getElementById('upload').addEventListener('change', handleFileSelect, false);

        function selectCarPowerAttorneyPdf() {
            clearSelectCar();
            addAttorneyVue.attorney_list = [];
            $('#attorney-select-car-modal').modal('show');
        }

        function selectCarTransferPdf() {
            clearSelectCar();
            addTransferVue.transfer_list = [];
            $('#transfer-register-power-attorney-select-car-modal').modal('show');
        }

       
    </script>
@endpush
