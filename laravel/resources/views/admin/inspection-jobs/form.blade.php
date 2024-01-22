@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
<form id="save-form">
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
                <h4>{{ __('inspection_cars.inspection') }}</h4>
                <div class="row">
                    <div class="col-sm-3 mb-3">
                        <x-forms.select-option id="inspection_type" :value="$d->inspection_flow_id" :list="$inspection_type" :label="__('inspection_cars.inspection_type')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3 worksheet_no">
                        <x-forms.select-option id="worksheet_no" :value="null" :list="null" :label="__('inspection_cars.worksheet_no')"
                            :optionals="[
                                'ajax' => true,
                                'default_option_label' => $worksheet,
                                'required' => true,
                            ]" />
                    </div>
                    <div class="col-sm-3" id="out_date">
                        <x-forms.date-input id="inspection_must_date_out" :value="$d->inspection_must_date_out" :label="__('inspection_cars.out_must_date')"
                            :optionals="[
                                'placeholder' => __('lang.select_date'),
                                'ajax' => true,
                                // 'default_option_label' => $worksheet,
                                'required' => true,
                            ]" />
                    </div>
                    <div class="col-sm-3" id="in_date">
                        <x-forms.date-input id="inspection_must_date_in" :value="$d->inspection_must_date_in" :label="__('inspection_cars.in_must_date')"
                            :optionals="[
                                'placeholder' => __('lang.select_date'),
                                'ajax' => true,
                                // 'default_option_label' => $worksheet,
                                'required' => true,
                            ]" />
                    </div>
                </div>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 20%;">@sortablelink('seq', __('inspection_cars.inspection_seq'))</th>
                                <th style="width: 25%;">@sortablelink('worksheet_type', __('inspection_cars.worksheet_type'))</th>
                            </tr>
                        </thead>
                        <tbody id="step_table">
                            <tr>
                                <td class="text-center" colspan="3">" ไม่มีรายการ "</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <h4>{{ __('inspection_cars.car_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_type" :value="$d->inspection_flow_id" :list="$car_type_list" :label="__('inspection_cars.car_type')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_id" :value="$d->car_id" :list="null" :label="__('inspection_cars.license_plate_chassis_engine')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('inspection_cars.engine_no')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('inspection_cars.chassis_no')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="car_category" :value="$d->car_categories_name" :label="__('inspection_cars.car_category')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="car_class" :value="$d->car_class_name" :label="__('inspection_cars.class')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="car_color" :value="$d->car_colors_name" :label="__('inspection_cars.color')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="engine_size" :value="$d->engine_size" :label="__('inspection_cars.engine_size')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="gear" :value="$d->car && $d->car_gear_name" :label="__('inspection_cars.gear')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="fuel_type" :value="$d->oil_type" :label="__('inspection_cars.fuel_type')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="tire" :value="$d->car && $d->car->carColor ? $d->car->carColor->name : null" :label="__('inspection_cars.tire')" />
                    </div>
                </div>

                <br>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="['url' => 'admin.inspection-jobs.index', 'view' => empty($view) ? null : $view, 'manage_permission' => Actions::Manage . '_' . Resources::Accessory]" />
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.inspection-jobs.store'),
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
        $('#engine_size').prop('disabled', true);
        $('#gear').prop('disabled', true);
        $('#fuel_type').prop('disabled', true);
        $('#tire').prop('disabled', true);

        $view = '{{ isset($view) }}';
        if ($view) {
            $('#transfer_type').prop('disabled', true);
            $('#reason').prop('disabled', true);
            $('#est_transfer_date').prop('disabled', true);
            $('#start_date').prop('disabled', true);
            $('#end_date').prop('disabled', true);
            $('#car_type').prop('disabled', true);
            $('#car_id').prop('disabled', true);
            $('#car_zone_id').prop('disabled', true);
            $('#car_park_id').prop('disabled', true);
            $('#cancel_reason').prop('disabled', true);
            $('#inspection_type').prop('disabled', true);
        }
        $("#worksheet_no").on('select2:opening', function(e) {
            clearCarDetailInput();
            clearCarID();
            clearCarType();
        });
        $('#car_type').on("select2:opening", function(e) {
            clearCarID();
            clearCarDetailInput();
        });

        //fill car detail
        $("#worksheet_no").on('select2:select', function(e) {
            var data = e.params.data;
            var inspection_type = $('#inspection_type').val();
            axios.get("{{ route('admin.inspection-jobs.default-car') }}", {
                params: {
                    worksheet_no: data.id,
                    inspection_type: inspection_type
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length === 1) {
                        response.data.data.forEach((e) => {
                            $("#engine_no").val(e.engine_no);
                            $("#chassis_no").val(e.chassis_no);
                            $("#car_class").val(e.car_class_name);
                            $("#car_color").val(e.car_colors_name);
                            $("#car_category").val(e.car_categories_name);
                            $("#engine_size").val(e.engine_size);
                            $("#gear").val(e.car_gear_name);
                            $("#tire").val(e.car_tire_name);
                            $("#fuel_type").val(e.oil_type);
                            $("#car_type").val(e.rental_type).trigger('change');
                            var defaultCarOption = {
                                id: e.car_id,
                                text: e.text,
                            };
                            var tempCarOption = new Option(defaultCarOption.text, defaultCarOption
                                .id, false, false);
                            $("#car_id").append(tempCarOption).trigger('change');
                            $("#car_id").val(e.car_id).trigger('change');
                            // $("#inspection_must_date_out").val(e.pickup_date).trigger('change');
                            // $("#inspection_must_date_in").val(e.return_date).trigger('change');
                            flatpickr("#inspection_must_date_out", {
                                defaultDate: e.pickup_date
                            });
                            flatpickr("#inspection_must_date_in", {
                                defaultDate: e.return_date
                            });
                        });
                    }
                    if (response.data.data.length > 1) {
                        response.data.data.forEach((e) => {
                            var defaultCarOption = {
                                id: e.car_id,
                                text: e.text,
                            };
                            var tempCarOption = new Option(defaultCarOption.text, defaultCarOption
                                .id, false, false);
                            $("#car_id").append(tempCarOption).trigger('change');
                        });
                    }
                }
            });
        });

        $("#car_id").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.inspection-jobs.select-option-car') }}";
                },
                type: 'GET',
                data: function(params) {
                    inspection_type = $("#inspection_type").val();
                    worksheet_no = $("#worksheet_no").val();
                    car_type = $("#car_type").val();
                    return {
                        inspection_type: inspection_type,
                        worksheet_no: worksheet_no,
                        car_type: car_type,
                        s: params.term
                    }
                },
                processResults: function(data) {
                    clearCarDetailInput();
                    return {
                        results: data

                    };
                },
            }
        });

        $("#car_id").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.inspection-jobs.default-car-license') }}", {
                params: {
                    car_id: data.id
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#engine_no").val(e.engine_no);
                            $("#chassis_no").val(e.chassis_no);
                            $("#car_class").val(e.car_class_name);
                            $("#car_color").val(e.car_colors_name);
                            $("#car_category").val(e.car_categories_name);
                            $("#engine_size").val(e.engine_size);
                            $("#gear").val(e.car_gear_name);
                            $("#tire").val(e.car_tire_name);
                            $("#fuel_type").val(e.oil_type);
                            $("#car_type").val(e.rental_type).trigger('change');
                            $("#car_id").val(e.car_id).trigger('change');
                        });
                    }
                }
            });
        });

        $('#out_date').hide();
        $('#in_date').hide();
        $("#inspection_type").change(function() {
            $('#worksheet_no').val(null).trigger('change');
            clearCarDetailInput();
            clearCarID();
            clearCarType();
            var id = document.getElementById("inspection_type").value;
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.inspection-jobs.get-data-inspection-type') }}",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id
                },
                success: function(data) {
                    $('#out_date').hide();
                    $('#in_date').hide();
                    $('#inspection_must_date_in').val('');
                    $('#inspection_must_date_out').val('');
                    $('#step_table').empty();
                    if (data.step_form.length > 0 && data.step_form[0].inspection_step_name != null) {
                        data.step_form.forEach((element, index) => {
                            if (element.transfer_reason_enum ==
                                '{{ TransferReasonEnum::DELIVER_CUSTOMER }}' || element
                                .transfer_reason_enum ==
                                '{{ TransferReasonEnum::DELIVER_GARAGE }}') {
                                $('#out_date').show();
                            }
                            if (element.transfer_reason_enum ==
                                '{{ TransferReasonEnum::RECEIVE_WAREHOUSE }}') {
                                $('#in_date').show();
                            }

                            $('#step_table').append(`<tr><td>${index+1}</td>
                            <td> ${element.transfer_reason} </td><td>${element.inspection_step_name}</td></tr>`)
                        });
                    } else if (data.step_form[0].inspection_step_name == null) {
                        $('#step_table').append(`<tr class="table-empty"><td class="text-center" colspan="5">
                            " {{ __('lang.no_list') }} " 
                            </td></tr>`)
                    } else {
                        $('#step_table').append(`<tr class="table-empty"><td class="text-center" colspan="5">
                            " {{ __('lang.no_list') }} " 
                            </td></tr>`)
                    }
                }
            });
        });


        function clearCarDetailInput() {
            $("#engine_no").val('');
            $("#chassis_no").val('');
            $("#car_class").val('');
            $("#car_color").val('');
            $("#car_category").val('');
            $("#engine_size").val('');
            $("#gear").val('');
            $("#tire").val('');
            $("#fuel_type").val('');
        }

        function clearCarID() {
            $("#car_id").val(null).trigger('change');
        }

        function clearCarType() {
            $("#car_type").val(null).trigger('change');
        }

        $("#worksheet_no").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.util.select2-rental.worksheet-by-flow') }}";
                },
                type: 'GET',
                data: function(params) {
                    parent_id = $("#inspection_type").val();
                    return {
                        parent_id: parent_id,
                        s: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
            }
        });
    </script>
@endpush
