@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@latest/dist/plugins/monthSelect/index.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script> --}}

    <script>
        function add2(j) {
            carVue2.add(j);
            carVue2.selectTwo();
        }

        function addSub2() {
            carVue2.addSub();
        }

        function remove(k) {
            carVue2.remove(k);
        }

        function removeList2(k) {
            carVue2.removeList(k);
        }

        Vue.component('flatpickr', {
            template: '<input :name="name" class="form-control" type="text" />',
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
                let currentYear = new Date().getFullYear();
                let flatpickrInstance = flatpickr(this.$el, {
                    ...this.options,
                    plugins: [
                        new monthSelectPlugin({
                            dateFormat: "m/Y",
                            shorthand: true,
                            theme: "light",
                        })
                    ],
                    minDate: new Date(currentYear, 0, 1),
                    onReady: function(selectedDates, dateStr, instance) {
                        instance.calendarContainer.classList.add('flatpickr-monthYear');
                    },
                    onChange: function(selectedDates, dateStr) {
                        vm.$emit('input', dateStr);
                    },
                    // locale: "th"
                });
                if (this.value) {
                    flatpickrInstance.setDate(this.value);
                }
            }
        });

        var rental = @json($rental)

        let carVue2 = new Vue({
            el: '#app2',
            data() {
                return {
                    form_list: @if (isset($form_list))
                        []
                    @else
                        []
                    @endif ,

                    rental_line: @if (isset($rental))
                        @json($rental)
                    @else
                        []
                    @endif ,

                    test: 'test1',

                    flow_type: @if (isset($d))
                        @json($d->id)
                    @else
                        []
                    @endif ,
                    inputs: @if (isset($question_list))

                        @json($question_list)
                    @else
                        [{
                            delivery_month_year: '',
                        }]
                    @endif ,
                    del_input_id: [],

                }
            },
            mounted: function() {
                $('#delivery_month_year0').select2({
                    data: this.form_list,
                    placeholder: '- กรุณาเลือก -',
                })

                $('.list').one('select2:open', function(e) {
                    $('input.select2-search__field').prop('placeholder', 'ค้นหา...');
                });
            },
            methods: {
                add(j, total) {
                    var ob = {
                        amount: '',
                        delivery_month_year: '',
                        remark: '',
                    };

                    let remain_amount = this.rental_line[j].amount;
                    this.rental_line[j].dealer_check_cars.push(ob);
                    this.rental_line[j].amount_car = remain_amount - total;
                },

                disableInput(j) {
                    index = this.rental_line[j].dealer_check_cars.length - 1;
                },

                checkMax(j, k) {
                    let remain_amount = this.rental_line[j].amount_car;
                    if (this.rental_line[j].dealer_check_cars[k].amount > remain_amount) {
                        this.rental_line[j].dealer_check_cars[k].amount = remain_amount;
                    }
                },

                checkKeyup(j, k) {
                    let total = 0;
                    let actual_amount = this.rental_line[j].amount;
                    this.rental_line[j].dealer_check_cars.forEach((car, index) => {
                        total += parseFloat(car.amount);

                    });
                    console.log(actual_amount, total)

                    if (total > actual_amount) {
                        this.rental_line[j].dealer_check_cars[k].amount = '';
                        return warningAlert('จำนวนรถเกินกว่าที่กำหนด');
                    }

                    let remain_amount = this.rental_line[j].amount_car;
                    if (this.rental_line[j].dealer_check_cars[k].amount > remain_amount) {
                        this.rental_line[j].dealer_check_cars[k].amount = remain_amount;
                    }
                },

                addSelect(j) {
                    let total = 0;
                    let actual_amount = this.rental_line[j].amount;
                    this.rental_line[j].dealer_check_cars.forEach((car, index) => {
                        total += parseFloat(car.amount);

                    });

                    if (total >= actual_amount) {
                        return warningAlert('จำนวนรถครบตามที่กำหนดแล้ว');
                    }
                    let last_index = this.rental_line[j].dealer_check_cars.length - 1;
                    let last_element = this.rental_line[j].dealer_check_cars[last_index];

                    if (last_element && (last_element.amount === "" ||
                        last_element.delivery_month_year === "" )) {

                        warningAlert('{{ __('lang.required_field_inform') }}');
                    } else {
                        this.add(j, total);
                        this.disableInput(j);
                    }
                },

                async selectTwo() {
                    await this.$nextTick()
                    var index = this.inputs.length;
                    index = index - 1;
                    $("#delivery_month_year" + index).select2({
                        data: this.form_list,
                        placeholder: '- กรุณาเลือก -',
                        allowClear: true,

                    })

                },

                remove(j, k) {
                    this.del_input_id.push(this.rental_line[j].dealer_check_cars.id);
                    this.rental_line[j].dealer_check_cars.splice(k, 1)
                    this.rental_line[j].dealer_check_cars.forEach(function(item, index) {
                        $("#delivery_month_year" + j + index).val(item.delivery_month_year).trigger(
                            'change')

                    });
                    let lastIndex = this.rental_line[j].dealer_check_cars.length - 1;

                    let remain_amount = this.rental_line[j].amount_car;

                },
                clearDealerCheckCars(id) {
                    line = this.rental_line.find(o => o.id == id);
                    line.dealer_check_cars = [];
                },
                setDealerCheckCars(id) {
                    line = this.rental_line.find(o => o.id == id);
                    dealer_check_car = {};
                    dealer_check_car.amount = "";
                    dealer_check_car.delivery_month_year = "";
                    dealer_check_car.remark = "";
                    line.dealer_check_cars = [dealer_check_car];
                },
            }
        })

        function openModal() {
            $("#modal-confirm").modal("show");
        }
    </script>
@endpush
