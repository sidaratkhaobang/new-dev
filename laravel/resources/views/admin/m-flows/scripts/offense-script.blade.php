@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush

@push('scripts')
    <script>
        function addOffenseLine() {
            window.addOffenseLineVue.add();
        }
        // offense_time
        Vue.component('input-time-vue', {
            template: '<input :name="name" class="form-control input-time" type="text" style="background-color: white !important;" readonly/>',
            props: {
                name: {
                    type: String,
                    default: '',
                },
                value: null,
                options: {
                    type: Object,
                    default: () => {
                        return {}
                    }
                }
            },
            mounted() {
                let vm = this;
                let input = $(this.$el).find('.input-time');
                let flatpickrInstance = flatpickr(this.$el, {
                    ...this.options,
                    dateFormat: "H:i",
                    onChange: function(selectedDates, dateStr) {
                        vm.$emit('input', dateStr);
                    },
                });
                $(input).css('background-color', 'none')
                if (this.value) {
                    flatpickrInstance.setDate(this.value);
                }
            }
        });

        window.addOffenseLineVue = new window.Vue({
            el: '#offense-line',
            data() {
                return {
                    form_list: [],
                    offense_list: @if (isset($offense_list))
                        @json($offense_list)
                    @else
                        []
                    @endif ,
                    express_way_list: @if (isset($express_way_list))
                        @json($express_way_list)
                    @else
                        []
                    @endif ,
                    del_input_id: [],
                    edit_index: null,
                }
            },
            methods: {
                add() {
                    this.offense_list.push({
                        offense_time: '',
                        expressway_id: null,
                        fee: null,
                        fine: null,
                        job_id: null,
                        job_type: null,
                        id: null,
                    });
                    this.selectTwo();
                },
                view: function(index) {
                    this.clearModalData();
                    this.loadModalData(index);
                    this.openModal();
                },
                openModal: function() {
                    $("#modal-car").modal("show");
                },
                clearModalData: function() {
                    $("#rental_no").text('');
                    $("#rental_name").text('');
                    $("#business_line").text('');
                    $("#car_type").text('');
                    $("#contract_no").text('');
                    $("#contract_start_date").text('');
                    $("#contract_end_date").text('');
                    $("#customer_group").text('');
                    $("#customer_address").text('');
                    $("#full_name").text('');
                    $("#agency").text('');
                    $("#driver_tel").text('');
                },
                loadModalData: function(index) {
                    var temp = null;
                    temp = this.offense_list[index];
                    var offense_time = temp.offense_time;
                    var car_id = document.getElementById("car_id").value;
                    var overdue_date = document.getElementById("overdue_date").value;
                    if (offense_time && car_id && overdue_date) {
                        axios.get("{{ route('admin.m-flows.car-data') }}", {
                            params: {
                                offense_time: offense_time,
                                car_id: car_id,
                                overdue_date: overdue_date,
                            }
                        }).then(response => {
                            if (response.data.success) {
                                if (response.data.data) {
                                    $("#rental_no").text(response.data.data.rental_no);
                                    $("#rental_name").text(response.data.data.rental_name);
                                    $("#business_line").text('-');
                                    $("#car_type").text(response.data.data.car_type);
                                    $("#contract_no").text(response.data.data.contract_no);
                                    $("#contract_start_date").text(response.data.data
                                        .contract_start_date);
                                    $("#contract_end_date").text(response.data.data.contract_end_date);
                                    $("#customer_group").text(response.data.data.customer_group);
                                    $("#customer_address").text(response.data.data.customer_address);
                                    $("#full_name").text(response.data.data.full_name);
                                    $("#agency").text('-');
                                    $("#driver_tel").text(response.data.data.driver_tel);
                                }
                            }
                        });
                    }
                },
                async selectTwo() {
                    await this.$nextTick()
                    var index = this.offense_list.length;
                    index = index - 1;
                    $("#expressway_id" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,
                    })
                },
                handle(key) {
                    var temp = null;
                    temp = this.offense_list[key];
                    var offense_time = temp.offense_time;
                    var car_id = document.getElementById("car_id").value;
                    var overdue_date = document.getElementById("overdue_date").value;
                    if (offense_time && car_id && overdue_date) {
                        axios.get("{{ route('admin.m-flows.car-data') }}", {
                            params: {
                                offense_time: offense_time,
                                car_id: car_id,
                                overdue_date: overdue_date,
                            }
                        }).then(response => {
                            if (response.data.success) {
                                if (response.data.data) {
                                    temp.job_id = response.data.data.job_id;
                                    temp.job_type = response.data.data.job_type;
                                }
                            }
                        });
                    }
                },
            }
        })
    </script>
@endpush
