@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script>
        let addHistoricalCarVue = new Vue({
            el: '#historical-car',
            data: {
                car_list: @if (isset($car_list))
                    @json($car_list)
                @else
                    []
                @endif ,
                edit_index: null,
                mode: null,
                pending_delete_car_ids: [],
            },
            methods: {
                display: function() {
                    $("#historical-car").show();
                },
                addHistoricalCar: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editHistoricalCar: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#historical-car-modal-label").html('แก้ไขข้อมูลการใช้งานรถ');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#license_plate_field").val(null).change();
                    $('input[name="start_date"]').val('');
                    $('input[name="end_date"]').val('');
                    $('input[name="start_time"]').val('');
                    $('input[name="end_time"]').val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.car_list[index];
                    $("#license_plate_field").val(temp.license_plate_id).change();
                    flatpickr('input[name="start_date"]', {
                        defaultDate: temp.start_date,
                    });
                    flatpickr('input[name="end_date"]', {
                        defaultDate: temp.end_date,
                    });
                    flatpickr('input[name="start_time"]', {
                        defaultDate: temp.start_time,
                    });
                    flatpickr('input[name="end_time"]', {
                        defaultDate: temp.end_time,
                    });
                    $("#id").val(temp.id);

                    var defaultCarLicensePlateOption = {
                        id: temp.license_plate_id,
                        text: temp.license_plate_text,
                    };
                    var tempCarLicensePlateOption = new Option(defaultCarLicensePlateOption.text,
                        defaultCarLicensePlateOption.id, true, true);
                    $("#license_plate_field").append(tempCarLicensePlateOption).trigger('change');
                },
                openModal: function() {
                    $("#modal-historical-car").modal("show");
                },
                hideModal: function() {
                    $("#modal-historical-car").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var car = _this.getDataFromModal();
                    if (_this.validateCarObject(car)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(car, index);
                        } else {
                            _this.saveAdd(car);
                        }
                        _this.edit_index = null;
                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getDataFromModal: function() {
                    var license_plate_id = document.getElementById("license_plate_field").value;
                    var license_plate_text = (license_plate_id) ? document.getElementById('license_plate_field')
                        .selectedOptions[0].text : '';
                    var start_date = $('input[name="start_date"]').val();
                    var end_date = $('input[name="end_date"]').val();
                    var start_time = $('input[name="start_time"]').val();
                    var end_time = $('input[name="end_time"]').val();
                    return {
                        license_plate_id: license_plate_id,
                        license_plate_text: license_plate_text,
                        start_date: start_date,
                        end_date: end_date,
                        start_time: start_time,
                        end_time: end_time,
                    };
                },
                validateCarObject: function(car) {
                    if (car.license_plate_id && car.start_date && car.end_date) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(car) {
                    this.car_list.push(car);
                },
                saveEdit: function(car, index) {
                    addHistoricalCarVue.$set(this.car_list, index, car);
                },

                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.car_list.length;
                },
                formatDate(x) {
                    if (x) {
                        return moment(x).format('DD/MM/YYYY');
                    }
                },
                removeHistoricalCar: function(index) {
                    car_id = this.car_list[index].id;
                    this.pending_delete_car_ids.push(car_id);
                    this.car_list.splice(index, 1);
                },
                uploadData: function(car_arr) {
                    this.car_list = car_arr;
                },
            },
            props: ['title'],
        });
        addHistoricalCarVue.display();

        function addCar() {
            addHistoricalCarVue.addHistoricalCar();
        }

        function saveCar() {
            addHistoricalCarVue.save();
        }
        var historical_id = document.getElementById('id').value;

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

                    var loggedIn = {!! json_encode(Auth::check()) !!};
                    if (loggedIn) {
                        $.ajax({
                            type: 'GET',
                            url: "{{ route('admin.gps-historical-data-alerts.upload-excel') }}",
                            data: {
                                json_object: JSON.parse(json_object),
                            },
                            success: function(data) {
                                addHistoricalCarVue.uploadData(data.data);
                            },
                            error: function(data) {
                                mySwal.fire({
                                    title: "{{ __('lang.store_error_title') }}",
                                    text: 'ไม่พบข้อมูล',
                                    icon: 'warning',
                                    confirmButtonText: "{{ __('lang.ok') }}",
                                });
                            }
                        });
                    }
                };

                reader.onerror = function(ex) {
                    console.log(ex);
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
    </script>
@endpush
