@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            @include('admin.cars.sections.tab-btn-group', [
                'car' => $d->id,
                'page' => $state,
            ])
        </div>
    </div>

    {{-- page 1 --}}
    <section class="section-page" id="page-1">
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('cars.car_detail'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.cars.sections.car-basic-detail')
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('cars.storage_detail'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.cars.sections.store-data')
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('cars.car_element_detail'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.cars.sections.car-element')
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('cars.accessory_detail'),
                'is_toggle' => true,
            ])
            <div class="block-content pt-0">
                @include('admin.cars.sections.car-accessory')
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.submit-group :optionals="['url' => 'admin.cars.index', 'view' => empty($view) ? null : $view]" />
                <x-forms.hidden id="id" :value="$d->id" />
            </div>
        </div>
    </section>

    @if (Route::is('*.edit') || Route::is('*.show'))
        @include('admin.cars.sections.insurance-page')
        @include('admin.cars.sections.rental-page')
        @include('admin.cars.sections.equipment-page')
        @include('admin.cars.sections.accident-page')
    @endif
</form>
@endsection
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.cars.store'),
])
@include('admin.cars.scripts.car-accessory-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_id',
    'url' => route('admin.util.select2.car-class-by-car-brand'),
    'parent_id' => 'car_brand_id',
])
{{--@include('admin.components.select2-ajax', [--}}
{{--    'id' => 'car_categorie_id',--}}
{{--    'url' => route('admin.util.select2.car-category-by-car-class'),--}}
{{--    'parent_id' => 'car_class_id',--}}
{{--])--}}
{{--@include('admin.components.select2-ajax', [--}}
{{--    'id' => 'car_group_id',--}}
{{--    'url' => route('admin.util.select2.car-groups'),--}}
{{--    'parent_id' => 'car_categorie_id',--}}
{{--])--}}
@include('admin.components.select2-ajax', [
    'id' => 'car_color_id',
    'url' => route('admin.util.select2.car-class-colors'),
])

{{-- @include('admin.components.select2-ajax', [
    'id' => 'slot_no',
    'url' => route('admin.util.select2-car.car-parks'),
    'parent_id' => 'zone',
]) --}}

@push('scripts')
    <script>
        $(".btn-tab-page").on("click", function () {
            const page = $(this).attr('data-page');
            $('.btn-tab-page').removeClass('active');
            $('.section-page').hide();
            $('#page-' + page).show();
            $('.page-' + page).addClass('active')
        }); 

        $('#car_group_name').prop('disabled', true);
        $('#car_categorie_name').prop('disabled', true);

        var status = '{{ isset($view) }}';
        if (status) {
            $('#car_code').prop('disabled', true);
            $('#license_plate').prop('disabled', true);
            $('#engine_no').prop('disabled', true);
            $('#chassis_no').prop('disabled', true);
            $('#car_class_id').prop('disabled', true);
            $('#car_color_id').prop('disabled', true);
            $('#last_mile').prop('disabled', true);
            $('#rental_type').prop('disabled', true);
            $('#status').prop('disabled', true);
            $('#engine_size').prop('disabled', true);
            $('#car_seat_id').prop('disabled', true);
            $('#central_lock_id').prop('disabled', true);
            $('#gear_id').prop('disabled', true);
            $('#drive_system_id').prop('disabled', true);
            $('#air_bag_id').prop('disabled', true);
            $('#side_mirror_id').prop('disabled', true);
            $('#anti_thift_system_id').prop('disabled', true);
            $('#abs_id').prop('disabled', true);
            $('#front_brake_id').prop('disabled', true);
            $('#rear_brake_id').prop('disabled', true);
            $('#car_tire_id').prop('disabled', true);
            $('#car_battery_id').prop('disabled', true);
            $('#oil_type').prop('disabled', true);
            $('#oil_tank_capacity').prop('disabled', true);
            $('#car_wiper_id').prop('disabled', true);
            $('.form-control').prop('disabled', true);
        }

        $('#zone').prop('disabled', true);
        $('#slot_no').prop('disabled', true);
        $('#car_age_start').prop('disabled', true);
        $('#car_age').prop('disabled', true);

        $("#registered_date").change(function() {
            car_register = $('#registered_date').val();

            var date1 = new Date(car_register);
            var date2 = new Date();
            if (date2.getTime() > date1.getTime()) {
                var diff = new Date(date2.getTime() - date1.getTime());
            } else {
                var diff = new Date(date1.getTime() - date2.getTime());
            }

            year = diff.getUTCFullYear() - 1970;
            month = diff.getUTCMonth();
            day = diff.getUTCDate() - 1;

            date = year + ' ปี ' + month + ' เดือน ' + day + ' วัน ';
            $('#car_age').val(date).change();
        });

        $("#start_date").change(function() {
            start_date = $('#start_date').val();

            var date1 = new Date(start_date);
            var date2 = new Date();
            if (date2.getTime() > date1.getTime()) {
                var diff = new Date(date2.getTime() - date1.getTime());
            } else {
                var diff = new Date(date1.getTime() - date2.getTime());
            }

            year = diff.getUTCFullYear() - 1970;
            month = diff.getUTCMonth();
            day = diff.getUTCDate() - 1;

            date = year + ' ปี ' + month + ' เดือน ' + day + ' วัน ';
            $('#car_age_start').val(date).change();
        });

        jQuery(function() {
            flatpickr("#registered_date", {
                maxDate: "today"
            });

            flatpickr("#start_date", {
                maxDate: "today"
            });
        });

        let cmiVue = new Vue({
            el: '#cmi-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                cmi_list: [],
            },
            async mounted() {
                this.getCMIList();
            },
            methods: {
                display: function() {
                    $("#cmi-list").show();
                },
                getCMIList: async function() {
                    const url = "{{ route('admin.cars.car-cmis') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var cmi_list = [...data];
                    this.cmi_list = cmi_list;
                },

            },
        });

        let vmiVue = new Vue({
            el: '#vmi-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                vmi_list: [],
            },
            async mounted() {
                this.getVMIList();
            },
            methods: {
                display: function() {
                    $("#vmi-list").show();
                },
                getVMIList: async function() {
                    const url = "{{ route('admin.cars.car-vmis') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var vmi_list = [...data];
                    this.vmi_list = vmi_list;
                },

            },
        });

        let rentalVue = new Vue({
            el: '#rental-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                rental_list: [],
            },
            async mounted() {
                this.getRentalList();
            },
            methods: {
                display: function() {
                    $("#rental-list").show();
                },
                getRentalList: async function() {
                    const url = "{{ route('admin.cars.car-rentals') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var rental_list = [...data];
                    this.rental_list = rental_list;
                },

            },
        });

        let equipmentVue = new Vue({
            el: '#equipment-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                equipment_list: [],
            },
            async mounted() {
                this.getEquipmentList();
            },
            methods: {
                display: function() {
                    $("#equipment-list").show();
                },
                getEquipmentList: async function() {
                    const url = "{{ route('admin.cars.car-install-equipments') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var equipment_list = [...data];
                    this.equipment_list = equipment_list;
                },

            },
        });

        let accidentVue = new Vue({
            el: '#accident-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                accident_list: [],
            },
            async mounted() {
                this.getAccidentList();
            },
            methods: {
                display: function() {
                    $("#accident-list").show();
                },
                getAccidentList: async function() {
                    const url = "{{ route('admin.cars.car-accidents') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var accident_list = [...data];
                    this.accident_list = accident_list;
                },

            },
        });

        let repairVue = new Vue({
            el: '#repair-list',
            data: {
                car_id: @if(isset($d->id)) @json($d->id) @else null @endif,
                repair_list: [],
            },
            async mounted() {
                this.getRepairList();
            },
            methods: {
                display: function() {
                    $("#repair-list").show();
                },
                getRepairList: async function() {
                    const url = "{{ route('admin.cars.car-repairs') }}";
                    const { data } = await axios.get(url, { params: {car_id: this.car_id } });
                    var repair_list = [...data];
                    this.repair_list = repair_list;
                },
            },
        });
    </script>
@endpush