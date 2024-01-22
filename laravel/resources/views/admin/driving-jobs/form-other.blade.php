@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    <x-blocks.block :title="__('driving_jobs.worksheet_table')" >
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="job_type_name" :value="__('driving_jobs.job_type_' . $d->job_type)" :label="__('driving_jobs.worksheet_type')" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('driving_jobs.description')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="self_drive_type" :value="$d->self_drive_type" :list="$self_drive_types" :label="__('driving_jobs.job_type')"
                    :optionals="['required' => true]" />
            </div>
        </div>

        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="start_date" :value="$d->start_date" :label="__('driving_jobs.start_date')" :optionals="['placeholder' => __('lang.select_date'), 'required' => true, 'date_enable_time' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="end_date" :value="$d->end_date" :label="__('driving_jobs.end_date')" :optionals="['placeholder' => __('lang.select_date'), 'required' => true, 'date_enable_time' => true]" />
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6" >
                <x-forms.select-option id="driver_id" :value="$d->driver_id" :list="null"
                :label="__('driving_jobs.driver_name')" :optionals="[
                    'ajax' => true,
                    'default_option_label' => $driver_name,
                ]" />
            </div>
            <div class="col-sm-6 origin-wrap" style="display: none;" >
                <x-forms.input-new-line id="origin" :value="$d->origin" :label="__('driving_jobs.origin')" />
            </div>
            <div class="col-sm-6 destination-wrap" style="display: none;" >
                <x-forms.input-new-line id="destination" :value="$d->destination" :label="__('driving_jobs.destination')" />
            </div>
        </div>
    </x-blocks.block>

    <x-blocks.block :title="__('car_park_transfers.car_table')" >
        <div class="row push">
            <div class="col-sm-6">
                <x-forms.select-option id="car_id" :value="$d->car_id" :list="null" :label="__('cars.license_plate_chassis_engine')" :optionals="[
                    'ajax' => true,
                    'default_option_label' => $car?->display_name,
                    'required' => true,
                ]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="car_class_name" :value="($car ? $car->car_class_name : null)" :label="__('car_classes.class')" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <x-forms.input-new-line id="rental_type_name" :value="($car ? $car->rental_type_name : null)" :label="__('cars.rental_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_color_name" :value="($car ? $car->car_color_name : null)" :label="__('car_classes.color')" />
            </div>
             <div class="col-sm-3">
                <x-forms.input-new-line id="zone_code" :value="($car ? $car->zone_code : null)" :label="__('cars.zone')" /> 
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_park_number" :value="($car ? $car->car_park_number : null)" :label="__('cars.slot_no')" />
            </div>
        </div>
        
    </x-blocks.block>

    <x-blocks.block>
        <x-forms.submit-group :optionals="['url' => 'admin.driving-jobs.index', 'view' => empty($view) ? null : $view]" />
    </x-blocks.block>

    <x-forms.hidden id="id" :value="$d->id" />
    <x-forms.hidden id="job_type" :value="$d->job_type" />
    <x-forms.hidden id="driving_job_type" :value="$d->driving_job_type" />
</form>
@include('admin.driving-jobs.modals.complete-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')

@include('admin.components.form-save', [
    'store_uri' => route('admin.driving-jobs.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'driver_id',
    'url' => route('admin.util.select2.driver'),
])

@push('scripts')
<script>
    $('#job_type_name').prop("readonly", true);
    $('#rental_type_name').prop("readonly", true);
    $('#car_class_name').prop("readonly", true);
    $('#car_color_name').prop("readonly", true);
    $('#zone_code').prop("readonly", true);
    $('#car_park_number').prop("readonly", true);
    $view = '{{ isset($view) }}';
    if ($view) {
        $('#remark').prop('disabled', true);
        $('#self_drive_type').prop('disabled', true);
        $('#start_date').prop('disabled', true);
        $('#end_date').prop('disabled', true);
        $('#driver_id').prop('disabled', true);
        $('#origin').prop('disabled', true);
        $('#destination').prop('disabled', true);
        $('#car_id').prop('disabled', true);
    }
    $(document).ready(() => {
        $('#self_drive_type').on('change', function(){
            var val = $(this).val();
            console.log('change self_drive_type', val);
            if(val == 'SEND'){
                $('.origin-wrap').hide();
                $('.destination-wrap').show();
            } else if(val == 'PICKUP'){
                $('.origin-wrap').show();
                $('.destination-wrap').hide();
            }
        });
        $('#self_drive_type').trigger('change');
        var car_id = $("#car_id").val();
        if(car_id != null && car_id != ''){
            $("#car_id").val(car_id).trigger('change.select2');
        }
    });

    $("#car_id").select2({
        placeholder: "{{ __('lang.select_option') }}",
        allowClear: true,
        ajax: {
            delay: 250,
            url: function(params) {
                return "{{ route('admin.util.select2.car-license-plate') }}";
            },
            type: 'GET',
            data: function(params) {
                parent_id = $("#car_type").val();
                parent_id_2 = $("#job_id").val();
                parent_type = $("#job_type").val();
                return {
                    parent_id: parent_id,
                    parent_id_2: parent_id_2,
                    parent_type: parent_type,
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

    $("#car_id").on('select2:select', function(e) {
        var data = e.params.data;
        var job_id = $("#job_id").val();
        var job_type = $("#job_type").val();
        axios.get("{{ route('admin.util.helper.car-detail') }}", {
            params: {
                car_id: data.id,
                job_id: job_id,
                job_type: job_type,
            }
        }).then(response => {
            if(response.data.success){
                $('#car_class_name').val(response.data.data.car_class_name);
                $('#car_color_name').val(response.data.data.car_color_name);
                $('#zone_code').val(response.data.data.zone_code);
                $('#car_park_number').val(response.data.data.car_park_number);
                $('#rental_type_name').val(response.data.data.rental_type_name);
            } else {
                $('#car_class_name').val('');
                $('#car_color_name').val('');
                $('#zone_code').val('');
                $('#car_park_number').val('');
                $('#rental_type_name').val('');
            }
        });
    });
</script>
@endpush
