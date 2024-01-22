@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        <a onclick="openModalPrint()"
            class="btn btn-primary">
            {{ __('car_park_transfers.transfer_print') }}
        </a>
    </nav>
@endsection

@section('content')

@include('admin.components.creator')

<form id="save-form">

    <x-blocks.block :title="__('car_park_transfers.title_info')" >
        @include('admin.car-park-transfers.sections.ref-job')
    </x-blocks.block>

    <x-blocks.block :title="__('car_park_transfers.driver_table')" >
        @include('admin.car-park-transfers.sections.driving-job')
    </x-blocks.block>

    <x-blocks.block :title="__('car_park_transfers.car_table')" >
        @include('admin.car-park-transfers.sections.info-car')
    </x-blocks.block>

    <x-blocks.block :title="__('car_park_transfers.parking_table')" >
        @include('admin.car-park-transfers.sections.info-car-park')
    </x-blocks.block>

    @if (isset($view))
    <x-blocks.block :title="__('car_park_transfers.transaction')" >
        @include('admin.car-park-transfers.transaction')
    </x-blocks.block>
    @endif

    <x-forms.hidden id="id" :value="$d->id" />

    <x-blocks.block>
        <x-forms.submit-group :optionals="['url' => 'admin.car-park-transfers.index', 'view' => empty($view) ? null : $view]" />
    </x-blocks.block>
</form>

@include('admin.car-park-transfers.modals.print-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.car-park-transfers.store'),
])

@push('scripts')
    <script>
        $('#opener_sheet').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#driver').prop('disabled', true);
        $('#car_category').prop('disabled', true);
        $('#car_class').prop('disabled', true);
        $('#car_color').prop('disabled', true);
        $('#user_car').prop('disabled', true);
        $('#engine_no').prop('disabled', true);
        $('#chassis_no').prop('disabled', true);
        $('#driver_name').prop('disabled', true);
        $('#rental_type').prop('disabled', true);
        $('#rental_no').prop('disabled', true);
        $('#car_license_plate').prop('disabled', true);
        $('#car_status').prop('disabled', true);
        $('#car_zone_id').prop('disabled', true);
        $('#car_park_id').prop('disabled', true);

        $view = '{{ isset($view) }}';
        if ($view) {
            $('#transfer_type').prop('disabled', true);
            $('#reason').prop('disabled', true);
            $('#est_transfer_date').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#car_status_id').prop('disabled', true);
            $('#car_id').prop('disabled', true);
            $('#car_zone_id').prop('disabled', true);
            $('#car_park_id').prop('disabled', true);
            $('#cancel_reason').prop('disabled', true);
            $('#driving_job_id').prop('disabled', true);
            $('input[name=is_difference_branch]').prop('disabled', true);
            $('input[name=is_singular]').prop('disabled', true);
            $('#origin_branch_id').prop('disabled', true);
            $('#destination_branch_id').prop('disabled', true);
        }

        $("#driving_job_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.car-park-transfers.default-driving-job') }}", {
                params: {
                    driving_job_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#driver_name").val(e.driver_name);
                            $("#rental_type").val(e.rental_type);
                            $("#rental_no").val(e.rental_no);
                            $("#start_date").val(e.start_date);
                            $("#end_date").val(e.end_date);
                            getCarDetail(e.car_id);
                        });
                    }
                }
            });
        });

        function getCarDetail(car_id) {
            axios.get("{{ route('admin.car-park-transfers.default-car') }}", {
                params: {
                    car_id: car_id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#car_license_plate").val(e.license_plate);
                            $("#car_id").val(e.car_id);
                            $("#car_status").val(e.car_status);
                            $("#car_status_id").val(e.status);
                            $("#engine_no").val(e.engine_no);
                            $("#chassis_no").val(e.chassis_no);
                            $("#car_class").val(e.car_class_name);
                            $("#car_color").val(e.car_colors_name);
                            $("#car_category").val(e.car_categories_name);
                        });
                    } else {
                        $("#car_license_plate").val('');
                        $("#car_id").val('');
                        $("#car_status").val('');
                        $("#car_status_id").val('');
                        $("#engine_no").val('');
                        $("#chassis_no").val('');
                        $("#car_class").val('');
                        $("#car_color").val('');
                        $("#car_category").val('');;
                    }
                }
            });
        }

        // $("#car_id").on('select2:select', function(e) {
        //     var data = e.params.data;
        //     axios.get("{{ route('admin.car-park-transfers.default-car') }}", {
        //         params: {
        //             car_id: data.id
        //         }
        //     }).then(response => {
        //         if (response.data.success) {
        //             if (response.data.data.length > 0) {
        //                 response.data.data.forEach((e) => {
        //                     $("#engine_no").val(e.engine_no);
        //                     $("#chassis_no").val(e.chassis_no);
        //                     $("#car_class").val(e.car_class_name);
        //                     $("#car_color").val(e.car_colors_name);
        //                     $("#car_category").val(e.car_categories_name);
        //                 });
        //             }
        //         }
        //     });
        // });

        // $("#car_id").on("change.select2", function(e) {
        //     $('#car_zone_id').val(null).trigger('change');
        //     $('#car_park_id').val(null).trigger('change');
        // });

        function generateZone() {
            var car_id = document.getElementById("car_id").value;
            if (car_id) {
                axios.get("{{ route('admin.car-park-transfers.default-car-zone') }}", {
                    params: {
                        car_id: car_id
                    }
                }).then(response => {
                    if (response.data.success) {
                        var car_park_zone_id = response.data.data[0].car_park_zone_id;
                        var car_park_zone_name = response.data.data[0].car_park_zone_code + ' : ' + response.data
                            .data[0].car_park_zone_name;
                        var car_park_id = response.data.data[0].car_park_id;
                        var car_park_number = response.data.data[0].car_park_number;

                        $("#car_zone_id").val(car_park_zone_id).change();
                        $("#car_park_id").val(car_park_id).change();
                        var defaultCarClassOption = {
                            id: car_park_zone_id,
                            text: car_park_zone_name,
                        };
                        var tempCarClassOption = new Option(defaultCarClassOption.text, defaultCarClassOption.id,
                            false,
                            false);
                        $("#car_zone_id").append(tempCarClassOption).trigger('change');

                        var defaultCarColorOption = {
                            id: car_park_id,
                            text: car_park_number,
                        };
                        var tempCarColorOption = new Option(defaultCarColorOption.text, defaultCarColorOption.id,
                            false,
                            false);
                        $("#car_park_id").append(tempCarColorOption).trigger('change');
                    }
                }).catch(error => {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: 'ไม่พบข้อมูลช่องจอด',
                        icon: 'warning',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                });
            } else {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: 'กรุณาเลือกเลขทะเบียน',
                    icon: 'warning',
                    confirmButtonText: "{{ __('lang.ok') }}"
                })
            }
        }

        function openModalPrint() {
            $('#modal-print').modal('show');
        }

        bind_on_change_radio('is_difference_branch', (val) => {
            if(val == 0){
                $('.difference-branch-wrap').hide();
            } else {
                $('.difference-branch-wrap').show();
            }
        });
    </script>
@endpush
