@push('scripts')
    <script>
        let addGpsCarVue = new Vue({
            el: '#gps-car',
            data: {
                car_list: @if (isset($car_list))@json($car_list)@else[]@endif ,
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#gps-car").show();
                },
                addCar: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editGpsCar: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#gps-car-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#license_plate_field").val('').change();
                    $("#chassis_no_field").val('').change();
                    $("#engine_no_field").val('').change();
                    $("#vid_field").val('').change();
                    $("#car_class_field").val('').prop('disabled', true);
                    $("#car_color_field").val('').prop('disabled', true);
                    $("#remark_field").val('');
                    $('input[name="is_check_gps_field"]').prop('checked', false);
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.car_list[index];
                    if (!temp.id) {
                        $("#license_plate_field").val(temp.license_plate_id);
                        $("#chassis_no_field").val(temp.chassis_no_id);
                        $("#engine_no_field").val(temp.engine_no_id);
                        $("#vid_field").val(temp.vid_id);
                    } else {
                        $("#license_plate_field").val(temp.license_plate_id).prop('disabled', true);
                        $("#chassis_no_field").val(temp.chassis_no_id).prop('disabled', true);
                        $("#engine_no_field").val(temp.engine_no_id).prop('disabled', true);
                        $("#vid_field").val(temp.vid_id).prop('disabled', true);
                    }
                    $("#car_class_field").val(temp.car_class_name).prop('disabled', true);
                    $("#car_color_field").val(temp.car_color_name).prop('disabled', true);
                    $("#remark_field").val(temp.gps_remark);
                    $('#is_check_gps_field' + temp.is_check_gps).prop('checked', true);
                    $("#id").val(temp.id);
                    $("#gps_id").val(temp.gps_id);

                    var defaultCarLicensePlateOption = {
                        id: temp.license_plate_id,
                        text: temp.license_plate_text,
                    };
                    var tempCarLicensePlateOption = new Option(defaultCarLicensePlateOption.text,
                        defaultCarLicensePlateOption.id, true, true);
                    $("#license_plate_field").append(tempCarLicensePlateOption).trigger('change');

                    var defaultCarChassisNoOption = {
                        id: temp.chassis_no_id,
                        text: temp.chassis_no_text,
                    };
                    var tempCarChassisNoOption = new Option(defaultCarChassisNoOption.text,
                        defaultCarChassisNoOption.id,
                        true, true);
                    $("#chassis_no_field").append(tempCarChassisNoOption).trigger('change');

                    var defaultCarEngineNoOption = {
                        id: temp.engine_no_id,
                        text: temp.engine_no_text,
                    };
                    var tempCarEngineNoOption = new Option(defaultCarEngineNoOption.text,
                        defaultCarEngineNoOption.id,
                        true, true);
                    $("#engine_no_field").append(tempCarEngineNoOption).trigger('change');

                    var defaultCarVidOption = {
                        id: temp.vid_id,
                        text: temp.vid_text,
                    };
                    if (temp.vid) {
                        var tempCarVidOption = new Option(defaultCarVidOption.text,
                            defaultCarVidOption.id,
                            true, true);
                        $("#vid_field").append(tempCarVidOption).trigger('change');
                    }
                },
                openModal: function() {
                    $("#modal-gps-car").modal("show");
                },
                hideModal: function() {
                    $("#modal-gps-car").modal("hide");
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
                    var chassis_no_id = document.getElementById("chassis_no_field").value;
                    var chassis_no_text = (chassis_no_id) ? document.getElementById('chassis_no_field')
                        .selectedOptions[0].text : '';
                    var engine_no_id = document.getElementById("engine_no_field").value;
                    var engine_no_text = (engine_no_id) ? document.getElementById('engine_no_field')
                        .selectedOptions[0].text : '';
                    var vid_id = document.getElementById("vid_field").value;
                    var vid_text = (vid_id) ? document.getElementById('vid_field').selectedOptions[0].text : '';
                    var car_class = document.getElementById("car_class_field").value;
                    var car_color = document.getElementById("car_color_field").value;
                    var gps_remark = document.getElementById("remark_field").value;
                    var is_check_gps = (document.querySelector('input[name="is_check_gps_field"]:checked')) ?
                        document.querySelector('input[name="is_check_gps_field"]:checked').value : null;
                    var id = document.getElementById("id").value;
                    var gps_id = (document.getElementById("gps_id")) ? document.getElementById("gps_id").value :
                        null;

                    return {
                        license_plate_id: license_plate_id,
                        license_plate_text: license_plate_text,
                        chassis_no_id: chassis_no_id,
                        chassis_no_text: chassis_no_text,
                        engine_no_id: engine_no_id,
                        engine_no_text: engine_no_text,
                        vid_id: vid_id,
                        vid_text: vid_text,
                        car_class: car_class,
                        car_color: car_color,
                        gps_remark: gps_remark,
                        is_check_gps: parseInt(is_check_gps),
                        id: id,
                        gps_id: gps_id,
                    };
                },
                validateCarObject: function(car) {
                    if (car.license_plate_id && car.is_check_gps) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(car) {
                    this.car_list.push(car);
                },
                saveEdit: function(car, index) {
                    addGpsCarVue.$set(this.car_list, index, car);
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
                checkedCheckBox: function(lt_rental_tor_id, id) {
                    this_checkbox = $('input[data-id="' + id + '"]');
                    var is_check = this_checkbox.prop('checked');
                    $('input[data-parent-id="' + lt_rental_tor_id + '"]').prop("checked", false);
                    this_checkbox.prop("checked", is_check);
                },
                addDefaultCar: function(e) {
                    var _this = this;
                    var car_index = _this.getIndex();
                    $("#car_class_field").val(e.car_class);
                    $("#car_color_field").val(e.car_color);
                },
            },
            props: ['title'],
        });
        addGpsCarVue.display();

        function addCar() {
            addGpsCarVue.addCar();
        }

        function saveCar() {
            addGpsCarVue.save();
        }

        function addDefaultCar(e) {
            addGpsCarVue.addDefaultCar(e);
        }

        async function getCarDetail(car_id) {
            try {
                const response = await axios.get("{{ route('admin.install-equipments.car-detail') }}", {
                    params: {
                        car_id: car_id
                    }
                });
                return response.data;
            } catch (error) {
                return null;
            }
        }

        function assignOptions(car, except_id) {
            if (except_id != 'license_plate_field') {
                var tempLicensePlateOption = new Option(car.license_plate, car.id, true, true);
                $("#license_plate_field").append(tempLicensePlateOption).trigger('change');
            }

            if (except_id != 'chassis_no_field') {
                var tempChasisOption = new Option(car.chassis_no, car.id, true, true);
                $("#chassis_no_field").append(tempChasisOption).trigger('change');
            }

            if (except_id != 'engine_no_field') {
                var tempEngineOption = new Option(car.engine_no, car.id, true, true);
                $("#engine_no_field").append(tempEngineOption).trigger('change');
            }

            if (except_id != 'vid_field') {
                if (car.vid) {
                    var tempVidOption = new Option(car.vid, car.id, true, true);
                    $("#vid_field").append(tempVidOption).trigger('change');
                }
            }
        }

        var car_select2_arr = ['license_plate_field', 'engine_no_field', 'chassis_no_field', 'vid_field'];
        car_select2_arr.forEach(element => {
            $("#" + element).on('select2:select', async function(e) {
                var data = e.params.data;
                var car = await getCarDetail(data.id);
                if (car) {
                    assignOptions(car, element);
                }
                axios.get("{{ route('admin.gps-remove-stop-signal-alerts.default-car') }}", {
                    params: {
                        license_plate_id: data.id
                    }
                }).then(response => {
                    if (response.data.success) {
                        // removeAllAccessories();
                        if (response.data.data) {
                            addDefaultCar(response.data.data);
                        }
                    }
                });
            });
        });
    </script>
@endpush
