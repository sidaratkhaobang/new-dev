@extends('admin.layouts.layout')

@section('page_title', 'รายงานรายจ่ายพนักงานขับรถ')

@push('custom_styles')
    <style>
        .block-header {
            padding: 0;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="block-header">
                <h4><i class="fa fa-file-lines"></i> {{ __('รายจ่ายคนขับ') }}</h4>
            </div>
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="save-form">
                    <x-forms.hidden id="id" :value="$driving_job->id" />
                    <div id="wage-job" v-cloak data-detail-uri="" data-title="">
                        <div class="table-wrap">
                            <table class="table table-striped">
                                <thead class="bg-body-dark">
                                <th style="width: 2px;">#</th>
                                <th>{{ __('driving_jobs.driver_wage') }}</th>
                                <th>{{ __('driving_jobs.remark') }}</th>
                                <th>{{ __('driving_jobs.amount') }}</th>
                                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                                </thead>
                                <tbody v-if="driver_wage_job_list.length > 0">
                                <tr v-for="(item, index) in driver_wage_job_list">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ item.driver_wage_text }}</td>
                                    <td>@{{ item.remark }}</td>
                                    <td>@{{ convertNumberToFloat(item.amount)}} @{{convertTextAmountType(item.amount_type)}}</td>
                                    <td class="sticky-col text-center">
                                        <div class="btn-group" v-if="status">
                                            <div class="col-sm-12">
                                                <div class="dropdown dropleft">
                                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                        <a class="dropdown-item" v-on:click="editWageJob(index)"><i
                                                                class="far fa-edit me-1"></i> แก้ไข</a>
                                                        <a class="dropdown-item btn-delete-row" v-on:click="removeWageJob(index)"><i
                                                                class="fa fa-trash-alt me-1"></i> ลบ</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][id]'" v-bind:value="item.id">
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][driver_wage_id]'" id="driver_wage_id" v-bind:value="item.driver_wage_id">
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][remark]'" id="remark" v-bind:value="item.remark">
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][amount]'" id="amount" v-bind:value="item.amount">
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][driver_wage_text]'" id="driver_wage_text" v-bind:value="item.driver_wage_text">
                                    <input type="hidden" v-bind:name="'wage_job['+ index+ '][amount_type]'" id="amount_type" v-bind:value="item.amount_type">
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr class="table-empty">
                                    <td class="text-center" colspan="5">“{{ __('lang.no_list') . __('driving_jobs.wage_job_table') }} “</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row" v-if="status">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-primary" onclick="addWageJob()"
                                        id="openModal">{{ __('lang.add') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row push">
                <div class="col-md-12 text-end">
                    <a class="btn btn-secondary" href="{{ route('admin.driver-report.show',$driver) }}">{{ __('lang.back') }}</a>
                    @if (strcmp($driving_job->status, DrivingJobStatusEnum::COMPLETE) == 0 && strcmp($driving_job->is_confirm_wage, BOOL_FALSE) == 0)
                        <button type="button" class="btn btn-info btn-save-draft"
                                data-status="{{ BOOL_TRUE }}">{{ __('บันทึก') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.driver-report.modals.wage-job-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')

@include('admin.components.select2-ajax', [
    'id' => 'driver_wage_field',
    'modal' => '#modal-wage-job',
    'url' => route('admin.util.select2-driver.driver-wage-not-month'),
])

@include('admin.components.form-save', [
    'store_uri' => route('admin.driving-jobs.store'),
])

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

        if (('{{ $driving_job->status }}' === '{{ DrivingJobStatusEnum::COMPLETE }}') && ('{{ $driving_job->is_confirm_wage }}' === '{{ BOOL_FALSE }}')) {
            defaultStatusJob(true);
        } else {
            defaultStatusJob(false);
        }

        let dropdownState = 'baht'

        $('#modal-wage-job').on('show.bs.modal', function (e) {
            const amount_type = $('#amount_type_field').val();
            if (amount_type === '{{\App\Enums\AmountTypeEnum::PERCENT}}') {
                select_amount_type('percent')
            } else {
                select_amount_type('baht')
            }
        })

        $('#modal-wage-job').on('hidden.bs.modal', function (e) {
            select_amount_type('baht')
            $('#input_amount').hide()
            $('#service_type_id_field_hidden').val('')
        })

        $('#dropdown-toggle-select-type').on('show.bs.dropdown', function () {
            if (dropdownState === 'baht') {
                $('#select_amount_type_baht').addClass('active')
                $('#select_amount_type_percent').removeClass('active')
            } else {
                $('#select_amount_type_baht').removeClass('active')
                $('#select_amount_type_percent').addClass('active')
            }
        })

        function select_amount_type(type) {
            if (type === 'baht') {
                dropdownState = 'baht'
                $('#amount_type_text').html('฿');
                $('#amount_type_field').val('{{\App\Enums\AmountTypeEnum::BAHT}}')
            }
            else if (type === 'percent') {
                dropdownState = 'percent'
                $('#amount_type_text').html('%');
                $('#amount_type_field').val('{{\App\Enums\AmountTypeEnum::PERCENT}}')
            }
        }

        $(".btn-save-draft").on("click", function() {
            let storeUri = "{{ route('admin.driver-report.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            saveForm(storeUri, formData);
        });

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
