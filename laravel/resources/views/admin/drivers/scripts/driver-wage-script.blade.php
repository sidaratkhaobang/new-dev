@push('scripts')
    <script>
        let addDriverWageVue = new Vue({
            el: '#driver-wage',
            data: {
                driver_wage_list: @if (isset($driver_wage_list))@json($driver_wage_list)@else[]@endif ,
                edit_index: null,
                mode: null,
            },
            methods: {
                display: function() {
                    $("#driver-wage").show();
                },
                addDriverWage: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editDriverWage: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#driver-wage-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#driver_wage_field").val('').change();
                    $('#service_type_field').val('').prop('disabled', true);
                    $('#driver_wage_category_field').val('').prop('disabled', true);
                    $('#wage_cal_type_field').val('').prop('disabled', true);
                    $("#amount_field").val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.driver_wage_list[index];
                    $('#driver_wage_field').empty().trigger("change");

                    var defaultDriverWageOption = {
                        id: temp.driver_wage_id,
                        text: temp.driver_wage_text,
                    };
                    var tempDriverWageOption = new Option(defaultDriverWageOption.text, defaultDriverWageOption.id, false, false);
                    $("#driver_wage_field").append(tempDriverWageOption).trigger('change');

                    $("#driver_wage_field").val(temp.driver_wage_id).change();
                    $('#driver_wage_category_field').val(temp.driver_wage_category_text).prop('disabled',true);
                    $('#wage_cal_type_field').val(temp.wage_cal_type_text).prop('disabled', true);
                    $('#service_type_field').val(temp.wage_cal_type_text).prop('disabled', true);

                    $("#amount_field").val(temp.amount);
                    $("#amount_type_field").val(temp.amount_type);
                    $("#service_type_field").val(temp.service_type_text);
                    $("#service_type_id_field_hidden").val(temp.service_type_id);

                    checkConditionDisplayInputAmount(temp.service_type_id)
                },
                openModal: function() {
                    $("#modal-driver-wage").modal("show");
                },
                hideModal: function() {
                    $("#modal-driver-wage").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var driver_wage = _this.getDataFromModal();
                    if (_this.validateObject(driver_wage)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(driver_wage, index);
                        } else {
                            _this.saveAdd(driver_wage);
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                convertTextAmountType(amount_type_text) {
                    if (amount_type_text === '{{\App\Enums\AmountTypeEnum::PERCENT}}') {
                        return '%'
                    } else {
                        return '฿'
                    }
                },
                convertNumberToFloat(num) {
                    return parseFloat(num).toFixed(2);
                },
                getDataFromModal: function() {
                    var driver_wage_id = document.getElementById("driver_wage_field").value;
                    var driver_wage_text = (driver_wage_id) ? document.getElementById('driver_wage_field').selectedOptions[0].text : '';
                    var driver_wage_category_text = document.getElementById("driver_wage_category_field").value;
                    var service_type_text = document.getElementById("service_type_field").value;
                    var service_type_id = document.getElementById("service_type_id_field_hidden").value;
                    var amount_type_text = document.getElementById("amount_type_field").value;
                    var wage_cal_type_text = document.getElementById("wage_cal_type_field").value;
                    var amount = document.getElementById("amount_field").value;
                    return {
                        driver_wage_id: driver_wage_id,
                        service_type_text: service_type_text,
                        service_type_id: service_type_id,
                        driver_wage_text: driver_wage_text,
                        driver_wage_category_text: driver_wage_category_text,
                        wage_cal_type_text: wage_cal_type_text,
                        amount: amount,
                        amount_type: amount_type_text,
                    };
                },
                validateObject: function(driver_wage) {
                    if (driver_wage.driver_wage_id && driver_wage.amount) {
                        return true;
                    } else {
                        return false;
                    }
                },
                saveAdd: function(driver_wage) {
                    this.driver_wage_list.push(driver_wage);
                    console.log(this.driver_wage_list);
                },
                saveEdit: function(driver_wage, index) {
                    addDriverWageVue.$set(this.driver_wage_list, index, driver_wage);
                },
                removeDriverWage: function(index) {
                    this.driver_wage_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.driver_wage_list.length;
                },
            },
            props: ['title'],
        });
        addDriverWageVue.display();

        function addDriverWage() {
            addDriverWageVue.addDriverWage();
        }

        function saveDriverWage() {
            addDriverWageVue.save();
        }

        function checkConditionDisplayInputAmount(service_type_id) {
            if (service_type_id === '{{ServiceTypeEnum::LIMOUSINE}}') {
                $('#select_amount_type_baht').show()
                $('#select_amount_type_percent').show()
            } else {
                $('#select_amount_type_baht').show()
                $('#select_amount_type_percent').hide()
            }

            $('#input_amount').show()
        }

        $("#driver_wage_field").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.drivers.default-driver-wage') }}", {
                params: {
                    driver_wage_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#driver_wage_category_field").val(e.driver_wage_category_text);
                            $("#wage_cal_type_field").val(e.wage_cal_type_text);
                            $("#service_type_field").val(e.service_type_name);
                            $("#service_type_id_field_hidden").val(e.service_type_id);

                            checkConditionDisplayInputAmount(e.service_type_id)
                        });
                    }
                }
            });
        });
    </script>
@endpush
