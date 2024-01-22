@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.parking-lots.sections.zone-origin-detail')
                @include('admin.parking-lots.sections.zone-moving')
                @include('admin.parking-lots.sections.select-moving-car')

                <x-forms.hidden id="area_id" :value="$area_id" />
                <x-forms.submit-group :optionals="['url' => 'admin.parking-lots.index']" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.parking-lots.shift-car-area'),
])

@include('admin.components.select2-ajax', [
    'id' => 'zone_id',
    'url' => route('admin.util.select2.car-park-zone-code-name'),
])

@include('admin.components.select2-ajax', [
    'id' => 'slot_number',
    'parent_id' => 'zone_id',
    'url' => route('admin.util.select2.car-park-area-number'),
])

@push('scripts')
    <script>
        jQuery(function() {
            Dashmix.helpers(['dm-table-tools-checkable']);
        });
        $('#car_group_list').prop('disabled', true);
        $('#code').prop('disabled', true);
        $('#name').prop('disabled', true);
        $('#number').prop('disabled', true);
        $('#car_group_list_1').prop('disabled', true);
        $('#remaining_car_in_park_amount').prop('disabled', true);
        $('#slot_in_use').prop('disabled', true);
        $('#total_car_park').prop('disabled', true);
        $('#total_empty_car_park').prop('disabled', true);

        var areaSelect = $("#slot_number");
        areaSelect.on("select2:select", function(e) {
            var id = e.params.data.id;
            axios.get("{{ route('admin.parking-lots.car-park-area-detail') }}", {
                params: {
                    car_park_area_id: id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data) {
                        var car_park_area = response.data.data;
                        console.log(car_park_area.car_group_text);
                        $('#car_group_list').val(car_park_area.car_group_text);
                        $('#total_car_park').val(car_park_area.total_slots);
                        $('#total_empty_car_park').val(car_park_area.available_slots);
                        $('#free_slot').html(car_park_area.available_slots);
                    }
                }
            });
        });

        var zoneSelect = $("#zone_id");
        zoneSelect.on("select2:open", function(e) {
            $('#car_group_list').val('');
            $('#total_car_park').val('');
            $('#total_empty_car_park').val('');
        });

        countUsedSlot()

        function countUsedSlot() {
            var sum = 0;
            $('.reserve-size').each(function() {
                var val = parseInt($(this).text());
                if (val) {
                    sum += parseInt(val);
                }
            });
            $('#slot_in_use').val(sum);
        }

        $('.js-table-checkable').on('click', function() {
            var sum = 0;
            $('.js-table-checkable > tbody  > tr').each(function() {
                var self = $(this);
                if (self.hasClass('table-active')) {
                    var val = parseInt(self.children('.reserve-size').text());
                    if (val) {
                        sum += parseInt(val);
                    }
                }
            });
            $('.total-reserve-size').html(sum);
        });
    </script>
@endpush
