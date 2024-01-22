@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>
        $(document).on('click', ".gantt-card", function() {
            $('.gantt-card').find('.check-box-car-select').prop('checked', false)
            $('.high-light').find('.check-box-car-select').prop('checked', true)
        })
        moment.locale('th');

        function getDayNameData() {
            let month = GanttChartVue.month - 1
            let year = GanttChartVue.year
            let day = GanttChartVue.days_in_month
            let dayname = [];
            for (let i = 1; i <= day; i++) {
                const date = moment({
                    year: year,
                    month: month,
                    day: i
                });
                const shortDayName = date.format('dd');
                dayname.push(shortDayName)
            }
            if (dayname) {
                GanttChartVue.days_name = dayname
            }
        }

        $(document).ready(function() {
            getDayNameData()
        })

        let GanttChartVue = new Vue({
            el: '#{{ $id }}',
            data: {
                select_items: [],
                item_list: @if (isset($item_list)) @json($item_list) @else [] @endif,
                item_list_temp: @if (isset($item_list)) @json($item_list) @else [] @endif,
                available_item_ids: @if (isset($available_item_ids)) @json($available_item_ids) @else null @endif,
                select_item_count: 0,
                selected_month: moment(),
                month: moment().format("M"),
                year: moment().format("Y"),
                days_in_month: moment().daysInMonth(),
                days_name: [],
                start_date: @json($start_date),
                end_date: @json($end_date),
                disable_ids: @if (isset($disable_ids)) @json($disable_ids) @else [] @endif,
                // offsetDays: moment().startOf("month").day() - 1,
                current_month_name: moment().format("MMMM Y"),
                select_multiple: @json($select_multiple),
                // countOfPage: 5,
                // currPage: 1,
                // filter_name: '',
                // filteredCarList: [],
                search: '',
                dates_between: [],
                visibility: 'hidden'
            },
            mounted() {
                this.getDateBetween();
                if (typeof mountFunction === 'function') {
                    mountFunction();
                }
            },
            computed: {
                grid_column() {
                    return `repeat(${this.days_in_month}, minmax(0, 1fr))`;
                },
            },
            methods: {
                select: function(index) {
                    if (this.select_multiple) {
                        checked = this.item_list_temp[index].checked;
                        this.item_list_temp[index].checked = !checked;
                        this.select_item_count += checked ? -1 : 1;
                    } else {
                        this.item_list_temp.map(function(x) {
                            x.checked = false;
                            return x;
                        });
                        this.item_list_temp[index].checked = true;
                        this.select_item_count = 1;
                    }
                },
                filterSearch() {
                    if (this.search) {
                        this.item_list_temp = this.item_list.filter(
                            o => o.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            (o.sub_name && o.sub_name.toLowerCase().includes(this.search.toLowerCase()))
                        );
                        return true;
                    } else {
                        this.item_list_temp = this.item_list;
                    }
                },
                prevMonth() {
                    const prev_month = this.selected_month.add(-1, "M");
                    this.days_in_month = prev_month.daysInMonth();
                    this.current_month_name = prev_month.format("MMMM Y");
                    this.month = prev_month.format("M");
                    this.year = prev_month.format("Y");
                    getDayNameData()
                },
                nextMonth() {
                    const next_month = this.selected_month.add(1, "M");
                    this.days_in_month = next_month.daysInMonth();
                    this.current_month_name = next_month.format("MMMM Y");
                    this.month = next_month.format("M");
                    this.year = next_month.format("Y");
                    getDayNameData()
                },
                getDateBetween() {
                    _this = this;
                    var start_date = moment(_this.start_date);
                    var end_date = moment(_this.end_date);
                    if (start_date.isValid() == false || end_date.isValid() == false ) {
                        return;
                    }
                    var starting_moment = start_date;
                    while (starting_moment <= end_date) {
                        highlight_year = starting_moment.get('year').toString();
                        highlight_month = (starting_moment.get('month') + 1).toString();
                        highlight_date = starting_moment.get('date').toString();
                        this.dates_between.push(highlight_year + highlight_month + highlight_date); 
                        starting_moment.add(1, 'days');
                    }
                },
                checkhighlightDate(id) {
                    return this.dates_between.includes(id);
                },
                highlightToday(id) {
                    var today = moment();
                    var year = today.get('year').toString();
                    var month = (today.get('month') + 1).toString();
                    var date = today.get('date').toString();
                    return (year + month + date) == id;
                },
                getTimeLines(id, month, year) {
                    callGetTimeLines(id, month, year);
                },
                setItemList(list) {
                    this.item_list = list;
                    this.item_list_temp = list;
                },
                isAvailable(id) {
                    var _this = this;
                    var available_item_ids = JSON.parse(JSON.stringify(this.available_item_ids))
                    if (this.available_item_ids) {
                        return available_item_ids.includes(id);
                    }
                    return true;
                }
            },
            props: ['title'],
        });
    </script>
@endpush
