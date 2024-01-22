@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        moment.locale('th');
        let GanttChartVue = new Vue({
            el: '#car-select',
            data: {
                cars: @if (isset($cars))
                    @json($cars)
                @else
                    []
                @endif ,
                car_selected: @if (isset($car_list))
                    @json($car_list)
                @else
                    []
                @endif ,
                car_list: [],
                car_list_temp: [],
                count_select_car: 0,
                selectedMonth: moment(),
                month: moment().format("M"),
                year: moment().format("Y"),
                daysInMonth: moment().daysInMonth(),
                car_brand_id: null,
                service_type_id: @json($d->service_type_id),
                rental_status: @json($d->status),
                status_change: 'CHANGE',
                currentMonthName: moment().format("MMMM Y"),
                select_multiple: false,
                prev_car_id: null,
                pickup_date: @json($d->pickup_date),
                return_date: @json($d->return_date),
                countOfPage: 5,
                currPage: 1,
                filter_name: '',
                filteredCarList: [],
                search: '',
            },
            async mounted() {
                this.getAvailablecar();
            },
            computed: {
                grid_column() {
                    return `repeat(${this.daysInMonth}, minmax(0, 1fr))`;
                },
                filteredRows: function() {
                    return this.car_list_temp;
                },

                pageStart: function() {
                    return (this.currPage - 1) * this.countOfPage;
                },
                totalPage: function() {
                    return Math.ceil(this.filteredRows.length / this.countOfPage);
                },
            },
            watch: {
                car_brand_id(new_car_brand_id, old_car_brand_id) {
                    this.getAvailablecar();
                },
                car_list_temp() {
                    this.setPage(1);
                    // this.filteredRows();
                    // this.pageStart();
                    // this.totalPage();

                }
            },
            methods: {
                select: function(index) {
                    if (this.select_multiple) {
                        checked = this.car_list_temp[index].checked;
                        this.car_list_temp[index].checked = !checked;
                        this.count_select_car += checked ? -1 : 1;
                    } else {
                        this.car_list.map(function(x) {
                            x.checked = false;
                            return x;
                        });
                        this.car_list_temp[index].checked = true;
                        this.count_select_car = 1;
                    }
                },
                filterCarList() {
                    if (this.search) {
                        this.car_list_temp = this.car_list.filter(car => car.license_plate.includes(this.search));
                        return true;
                    } else {
                        this.car_list_temp = this.car_list;


                    }
                    // this.currPage = 1; 
                    // console.log(this.filteredCarList);
                    // this.car_list = this.filteredCarList;
                },
                prevMonth(event) {
                    event.preventDefault();
                    const prevMonth = this.selectedMonth.add(-1, "M");
                    this.daysInMonth = prevMonth.daysInMonth();
                    // this.offsetDays = prevMonth.startOf("month").day() - 1;
                    this.currentMonthName = prevMonth.format("MMMM Y");
                    this.month = prevMonth.format("M");
                    this.year = prevMonth.format("Y");
                    this.getAvailablecar();

                },
                nextMonth(event) {
                    event.preventDefault();
                    const nextMonth = this.selectedMonth.add(1, "M");
                    this.daysInMonth = nextMonth.daysInMonth();
                    // this.offsetDays = nextMonth.startOf("month").day() - 1;
                    this.currentMonthName = nextMonth.format("MMMM Y");
                    this.month = nextMonth.format("M");
                    this.year = nextMonth.format("Y");
                    this.getAvailablecar();
                },
                setPage: function(idx) {
                    if (idx <= 0 || idx > this.totalPage) {
                        return;
                    }
                    this.currPage = idx;
                },
                async getAvailablecar() {
                    const rental_type = "{{ RentalTypeEnum::SPARE }}";
                    // console.log(rental_type);
                    // const url = "{{ route('admin.short-term-rentals.available-car-spares') }}";
                    const url = "{{ route('admin.short-term-rentals.available-cars') }}";
                    const {
                        data
                    } = await axios.get(url, {
                        params: {
                            month: this.month,
                            year: this.year,
                            car_brand_id: this.car_brand_id,
                            service_type_id: this.service_type_id,
                            rental_type: rental_type,
                            pickup_date: this.pickup_date,
                            return_date: this.return_date,
                        }
                    });
                    var car_list = [...data.data];
                    car_list.map(car => {
                        if (this.car_selected.includes(car.id)) {
                            car.checked = true;
                        }
                        return car;
                    });
                    this.car_list = car_list;
                    this.car_list_temp = car_list;
                    if (this.search) {
                        // this.car_list_temp = car_list.filter(car => car.license_plate.includes(this.search));
                        this.filterCarList();
                    }
                    // this.search = null;
                },
                openCarChart: function(car_id) {
                    console.log(this.rental_status);
                    $('#modal-car-select').modal('show');
                    this.prev_car_id = car_id;
                    this.car_list.map(car => {
                        car.checked = false;
                        return car;
                    });
                    var found_index = this.car_list.findIndex(x => x.id == car_id);
                    if(this.car_list[found_index]){
                    this.car_list[found_index].checked = true;
                    }
                },
                truncateString: function(string, limit) {
                    return string.substring(0, limit);
                },
                selectNewCar: function() {
                    //
                    var car_select = this.car_list.find(x => x.checked == true);
                    if (car_select == undefined) {
                        return warningAlert("กรุณาเลือกรถ");
                    }
                    console.log(car_select);
                    if (car_select.id == this.prev_car_id) {
                        return warningAlert("ไม่สามารถเลือกรถคันเดิมได้");
                    }
                    const car_select_existed = this.cars.some(function(el) {
                        return el.id === car_select.id
                    });
                    if (car_select_existed) {
                        return warningAlert("รถถูกเลือกไปแล้ว");
                    }
                    var new_car = car_select;
                    new_car.class_full_name = car_select.car_class_full_name;
                    new_car.is_new_car = 1;
                    new_car.former_car = this.prev_car_id;
                    new_car.status_arr = [];
                    this.cars.push(new_car);
                    var prev_car_index = this.cars.findIndex(x => x.id == this.prev_car_id);
                    this.cars[prev_car_index].is_replace = 1;
                    $('#modal-car-select').modal('hide');

                },
                removeNewCar: function(car_id) {
                    var car_to_remove = this.cars.find(x => x.id == car_id);
                    var car_to_remove_index = this.cars.findIndex(x => x.id == car_id);
                    var prev_car_index = this.cars.findIndex(x => x.id == car_to_remove.former_car);
                    this.cars[prev_car_index].is_replace = 0;
                    this.cars.splice(car_to_remove_index, 1);
                }
            },
            props: ['title'],
        });
    </script>
@endpush
