@extends('admin.layouts.layout')
@section('page_title', 'ตัวอย่าง Gantt Chart')

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => 'ตัวอย่าง gantt component',
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <x-gantt-chart id="car-table" :searchable="true" table-header="เลือกรถ" :item-list="$car_list" :status-list="$status_list"
            :optionals="[ 
                'search_text' => 'ข้อมูลรถ', 
                'show_count' => false,
                'show_navigate_btn' => true,
                'start_date' => '2023-10-02 12:00:00',
                'end_date' => '2023-10-19 12:00:00',
                ]" >
            </x-gantt-chart>
        </div>

    </div>

</div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.date-input-script')

@push('pre_scripts')
<script>
    function callGetTimeLines(id, month, year) {
        var params = {
            id: id,
            month: month,
            year, year
        };
        axios.post("{{ route('admin.short-term-rentals.get-timelines') }}", params).then(response => {
            if (response.data.success) {
                document.querySelector("#timeline-container-" + id).innerHTML = response.data.html;
            }
        });
    }

</script>
@endpush