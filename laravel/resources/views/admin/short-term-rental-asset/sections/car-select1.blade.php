
<x-gantt-chart id="car-table" :searchable="true" table-header="เลือกรถ" :item-list="$car_list" :status-list="$status_list"
:optionals="[ 
    'search_text' => 'ข้อมูลรถ', 
    'show_count' => false,
    'show_navigate_btn' => true,
    'start_date' => $d->pickup_date,
    'end_date' => $d->return_date,
    'available_item_ids' => $available_car_ids,
    'select_multiple' => $select_multiple
    ]" >
</x-gantt-chart>


@push('pre_scripts')
<script>
    function callGetTimeLines(id, month, year) {
        showLoading();
        var params = {
            id: id,
            month: month,
            year, year
        };
        axios.post("{{ route('admin.short-term-rentals.get-timelines') }}", params).then(response => {
            if (response.data.success && response.data.html) {
                var selector = document.querySelector("#timeline-container-" + id);
                if (selector) {
                    document.querySelector("#timeline-container-" + id).innerHTML = response.data.html;
                }
            }
            hideLoading();
        });
    }
</script>
@endpush
@push('scripts')
<script>
$('input[type=radio][name=car_brand_id]').change(function() {
    var brand_id = this.value;
    var service_type_id = '{{ $d->service_type_id }}';
    var product_id = '{{ $d->product_id }}';
    var params = {
        service_type_id: service_type_id,
        product_id: product_id,
    };
    if (brand_id !== 'all') {
        params.brand_id = brand_id;
    }
    showLoading();
    axios.get("{{ route('admin.short-term-rentals.asset-cars') }}", {params}).then(response => {
        if (response.data.success) {
            var data = response.data.data;
            GanttChartVue.setItemList(data);
        }
        hideLoading();
    });
});

function canSelect(car_id, year, month) {
    showLoading();
    axios.get("{{ route('admin.short-term-rentals.asset-cars') }}", {params}).then(response => {
        if (response.data.success) {
            var data = response.data.data;
            GanttChartVue.setItemList(data);
        }
        hideLoading();
    });
}
</script>
@endpush
