@push('scripts')
    <script>
        let addWageJobVue = new Vue({
            el: '#wage-job',
            data: {
                driver_wage_job_list: @if (isset($driver_wage_job_list)) @json($driver_wage_job_list) @else [] @endif ,
                edit_index: null,
                mode: null,
                status: false,
            },
            methods: {
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
                display: function() {
                    $("#wage-job").show();
                },
                addWageJob: function() {
                    this.setIndex(this.setLastIndex());
                    this.clearModalData();
                    this.mode = 'add';
                    this.openModal();
                },
                editWageJob: function(index) {
                    this.setIndex(index);
                    this.loadModalData(index);
                    this.mode = 'edit';
                    $("#wage-job-modal-label").html('แก้ไขข้อมูล');
                    this.openModal();
                },
                clearModalData: function() {
                    $("#driver_wage_field").val('').change();
                    $('#amount_field').val('');
                    $('#remark_field').val('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.driver_wage_job_list[index];
                    $('#driver_wage_field').empty().trigger("change");

                    var defaultWageJobOption = {
                        id: temp.driver_wage_id,
                        text: temp.driver_wage_text,
                    };
                    var tempWageJobOption = new Option(defaultWageJobOption.text, defaultWageJobOption.id,
                        false, false);
                    $("#driver_wage_field").append(tempWageJobOption).trigger('change');

                    $("#driver_wage_field").val(temp.driver_wage_id).change();
                    $('#amount_field').val(temp.amount);
                    $('#amount_type_field').val(temp.amount_type);
                    $('#remark_field').val(temp.remark);

                    checkConditionDisplayInputAmount(temp.service_type_id)
                },
                openModal: function() {
                    $("#modal-wage-job").modal("show");
                },
                hideModal: function() {
                    $("#modal-wage-job").modal("hide");
                },
                save: function() {
                    var _this = this;
                    var job_wage = _this.getDataFromModal();
                    if (_this.validateObject(job_wage)) {
                        if (_this.mode == 'edit') {
                            var index = _this.edit_index;
                            _this.saveEdit(job_wage, index);
                        } else {
                            _this.saveAdd(job_wage);
                        }
                        _this.edit_index = null;

                        _this.display();
                        _this.hideModal();
                    } else {
                        warningAlert("{{ __('lang.required_field_inform') }}");
                    }
                },
                getDataFromModal: function() {
                    var driver_wage_id = document.getElementById("driver_wage_field").value;
                    var driver_wage_text = (driver_wage_id) ? document.getElementById('driver_wage_field')
                        .selectedOptions[0].text : '';
                    var amount = document.getElementById("amount_field").value;
                    var amount_type_text = document.getElementById("amount_type_field").value;
                    var remark = document.getElementById("remark_field").value;
                    return {
                        driver_wage_id: driver_wage_id,
                        driver_wage_text: driver_wage_text,
                        amount: amount,
                        amount_type: amount_type_text,
                        remark: remark,
                    };
                },
                validateObject: function(job_wage) {
                    if (job_wage.driver_wage_id) {
                        return true;
                    } else {
                        return false;
                    }
                },
                addByDefault: function(data) {
                    this.driver_wage_job_list = [];
                    data.forEach((e) => {
                        this.driver_wage_job_list.push(e);
                    });
                },
                defaultStatusJob: function(status_job) {
                    this.status = status_job;
                },
                saveAdd: function(job_wage) {
                    this.driver_wage_job_list.push(job_wage);
                },
                saveEdit: function(job_wage, index) {
                    addWageJobVue.$set(this.driver_wage_job_list, index, job_wage);
                },
                removeWageJob: function(index) {
                    this.driver_wage_job_list.splice(index, 1);
                },
                setIndex: function(index) {
                    this.edit_index = index;
                },
                getIndex: function() {
                    return this.edit_index;
                },
                setLastIndex: function() {
                    return this.driver_wage_job_list.length;
                },
            },
            props: ['title'],
        });
        addWageJobVue.display();
        window.addWageJobVue = addWageJobVue;

        function addWageJob() {
            addWageJobVue.addWageJob();
        }

        function saveWageJob() {
            addWageJobVue.save();
        }

        function addWageJobByDefault(data) {
            addWageJobVue.addByDefault(data);
        }

        function defaultStatusJob(status_job) {
            console.log(status_job);
            addWageJobVue.defaultStatusJob(status_job);
        }

        function checkConditionDisplayInputAmount(service_type_id) {
            console.log('service_type_id : ' + service_type_id )
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
