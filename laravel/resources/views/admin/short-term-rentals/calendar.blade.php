@extends('admin.layouts.layout')
@section('page_title', 'ตารางจองรถเช่าระยะสั้น')

@section('content')
    <div class="block">
        <div class="block-content">
            <x-gantt-chart id="car-table" :searchable="true" table-header="เลือกรถ" :item-list="$car_list" :status-list="$status_list"
                :optionals="[
                    'search_text' => 'ข้อมูลรถ',
                    'show_count' => false,
                    'show_navigate_btn' => true,
                    'start_date' => null,
                    'end_date' => null,
                    'available_item_ids' => [],
                    'select_multiple' => false,
                    'can_select' => false,
                ]">
            </x-gantt-chart>
        </div>
    </div>
@endsection

@push('pre_scripts')
    <script>
        async function callGetTimeLines(id, month, year) {
            showLoading();
            var params = {
                id: id,
                month: month,
                year,
                year
            };
            await axios.post("{{ route('admin.short-term-rentals.get-timelines') }}", params).then(response => {
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
