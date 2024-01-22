@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        $(document).on('click',".gantt-card",function(){
            $('.gantt-card').find('.check-box-car-select').prop('checked',false)
            $('.high-light').find('.check-box-car-select').prop('checked',true)
        })
        moment.locale('th');
        function getDayNameData(){
            let month = GanttChartVue.month - 1
            let year = GanttChartVue.year
            let day = GanttChartVue.daysInMonth
            let dayname = [];
            for (let i = 1; i <= day; i++) {
                const date = moment({year:year,month: month, day: i });
                const shortDayName = date.format('dd');
                dayname.push(shortDayName)
            }
            if(dayname){
                GanttChartVue.daysName = dayname
            }
        }

        $(document).ready(function(){
            getDayNameData()
        })

        let GanttChartVue = new Vue({
            el: '#gantt-chart',
            data: {
                car_selected: [],
                car_list: [],
                car_list_temp: [],
                count_select_car: 0,
                selectedMonth: moment(),
                month: moment().format("M"),
                year: moment().format("Y"),
                daysInMonth: moment().daysInMonth(),
                daysName: [],
                car_brand_id: null,
                service_type_id: @json($d->service_type_id),
                rental_id: @json($rental_id),
                // offsetDays: moment().startOf("month").day() - 1,
                currentMonthName: moment().format("MMMM Y"),
                select_multiple: @json($select_multiple),
                pickup_date: @json($d->pickup_date),
                return_date: @json($d->return_date),
                car_rental : @if(!empty($car_rental)) @json($car_rental) @else [] @endif,
                // search: null,

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
                // search: function(search) {
                //     // this.car_list = this.car_list.filter(car => car.license_plate.includes(search));
                //     this.filterCarList(search);
                //     // if(search){
                //     //     this.car_list = this.car_list.filter(car => car.license_plate.includes(search));
                //     // }else{
                //     //     this.car_list = this.car_list_temp.filter(car => car.license_plate.includes(search));
                //     // }
                // },
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
                        // console.log(search)
                        this.car_list_temp = this.car_list.filter(car => car.license_plate.includes(this.search));
                        // console.log(this.car_list_temp)
                        return true;
                    } else {
                        this.car_list_temp = this.car_list;


                    }
                    // this.currPage = 1;
                    // console.log(this.filteredCarList);
                    // this.car_list = this.filteredCarList;
                },
                prevMonth() {
                    const prevMonth = this.selectedMonth.add(-1, "M");
                    this.daysInMonth = prevMonth.daysInMonth();
                    // this.offsetDays = prevMonth.startOf("month").day() - 1;
                    this.currentMonthName = prevMonth.format("MMMM Y");
                    this.month = prevMonth.format("M");
                    this.year = prevMonth.format("Y");
                    this.getAvailablecar();
                    getDayNameData()

                },
                nextMonth() {
                    const nextMonth = this.selectedMonth.add(1, "M");
                    this.daysInMonth = nextMonth.daysInMonth();
                    // this.offsetDays = nextMonth.startOf("month").day() - 1;
                    this.currentMonthName = nextMonth.format("MMMM Y");
                    this.month = nextMonth.format("M");
                    this.year = nextMonth.format("Y");
                    this.getAvailablecar();
                    getDayNameData()
                },
                setPage: function(idx) {
                    if (idx <= 0 || idx > this.totalPage) {
                        return;
                    }
                    this.currPage = idx;
                },
                async getAvailablecar() {
                    const url = "{{ route('admin.short-term-rentals.available-cars') }}";
                    const {
                        data
                    } = await axios.get(url, {
                        params: {
                            month: this.month,
                            year: this.year,
                            car_brand_id: this.car_brand_id,
                            service_type_id: this.service_type_id,
                            rental_id: this.rental_id,
                            pickup_date: this.pickup_date,
                            return_date: this.return_date,
                            // search: this.search,
                        }
                    });
                    var car_list = [...data.data];
                    car_list.map(car => {
                        if (this.car_selected.includes(car.id)) {
                            car.checked = true;
                        }
                        car.can_rent = true;
                        return car;
                    });
                    this.car_list = car_list;
                    this.car_list_temp = car_list;
                    console.log(this.car_list_temp);
                    if (this.search) {
                        // this.car_list_temp = car_list.filter(car => car.license_plate.includes(this.search));
                        this.filterCarList();
                    }
                    if(!this.car_brand_id){
                        $('#total_car').empty().append(car_list.length)
                    }
                    // this.search = null;
                },
            },
            props: ['title'],
        });
    </script>
@endpush
