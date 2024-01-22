@extends('admin.layouts.layout')

@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .nav-link {
            color: #343a40;
        }

        .nav-tabs-alt .nav-link.active,
        .nav-tabs-alt .nav-item.show .nav-link {
            color: #0665d0;
        }
    </style>
@endpush

@section('content')
    <form id="save-form">
        @if (strcmp($d->job_type, DrivingJobTypeStatusEnum::OTHER) != 0)
            <x-blocks.block :title="__('driving_jobs.job_parent_table')">
                @include('admin.driving-jobs.sections.job-parent')
            </x-blocks.block>
        @endif

        <x-blocks.block :title="__('driving_jobs.worksheet_table')">
            @include('admin.driving-jobs.sections.info')
        </x-blocks.block>

        <x-blocks.block :title="__('driving_jobs.car_info')">
            @include('admin.driving-jobs.sections.car')
        </x-blocks.block>

        <x-blocks.block :title="__('driving_jobs.car_transfer_info')">
            @include('admin.driving-jobs.sections.car-transfer')
        </x-blocks.block>

        <x-blocks.block :title="__('driving_jobs.wage_job_table')">
            @include('admin.driving-jobs.sections.wage-job')
        </x-blocks.block>

        <x-blocks.block>
            <x-forms.submit-group :optionals="['url' => 'admin.driving-jobs.index', 'view' => empty($view) ? null : $view]" />
        </x-blocks.block>

        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="self_drive_type" :value="$d->self_drive_type" />

    </form>
    @include('admin.driving-jobs.modals.complete-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.driving-jobs.scripts.wage-job-script')
@include('admin.driving-jobs.scripts.job-parent-script')

@include('admin.components.form-save', [
    'store_uri' => route('admin.driving-jobs.store'),
])

@include('admin.components.select2-ajax', [
    'id' => 'driver_id',
    'url' => route('admin.util.select2.driver'),
])

{{-- @include('admin.components.select2-ajax', [
    'id' => 'job_id',
    'url' => route('admin.util.select2-driver.default-job'),
    'parent_id' => 'job_type',
]) --}}

@include('admin.components.select2-ajax', [
    'id' => 'parent_id',
    'url' => route('admin.util.select2-driver.parent-driving-job'),
])

@include('admin.components.select2-ajax', [
    'id' => 'driver_wage_field',
    'modal' => '#modal-wage-job',
    'url' => route('admin.util.select2-driver.driver-wage-not-month'),
])

@include('admin.components.select2-ajax', [
    'id' => 'status',
    'url' => route('admin.util.select2-driver.driving-job-status'),
])

{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
    'parent_id' => 'car_type',
    'parent_id_2' => 'job_id',
]) --}}

@push('scripts')
    <script>
        $('#worksheet_no').prop("readonly", true);
        $('#driving_job_type').prop("readonly", true);
        $('#job_type').prop("readonly", true);
        $('#service_type_rental').prop("readonly", true);
        $("#zone").prop('disabled', true);
        $("#slot_no").prop('disabled', true);
        $view = '{{ isset($view) }}';
        $status = '{{ $d->status }}';
        $is_confirm = '{{ $d->is_confirm_wage }}';
        $is_confirm = '{{ $d->is_confirm_wage }}';
        driving_job_type = '{{ $d->driving_job_type }}';
        if (driving_job_type === '{{ DrivingJobTypeStatusEnum::MAIN_JOB }}') {
            $("#car_type").prop('disabled', true);
            $("#car_id").prop('disabled', true);
            $("#car_class").prop('disabled', true);
            $("#car_color").prop('disabled', true);
            $('#job_type').prop('disabled', true);
            $('#job_id').prop('disabled', true);
            $("#self_drive_type").prop('disabled', true);
        }
        if ($view) {
            $('#job_type').prop('disabled', true);
            $('#job_id').prop('disabled', true);
            $('#parent_id').prop('disabled', true);
            $('#income').prop('disabled', true);
            $('#driver_id').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $("#car_type").prop('disabled', true);
            $("#car_id").prop('disabled', true);
            $("#engine_no").prop('disabled', true);
            $("#chassis_no").prop('disabled', true);
            $("#car_class").prop('disabled', true);
            $("#car_color").prop('disabled', true);
            $("#destination").prop('disabled', true);
            $("#origin").prop('disabled', true);
            $("#end_date").prop('disabled', true);
            $("#start_date").prop('disabled', true);
            $("#status").prop('disabled', true);
            $("#self_drive_type").prop('disabled', true);

            $('input[name="self_drive_type"]').prop('disabled', true);
            if (($status === '{{ DrivingJobStatusEnum::COMPLETE }}') && ($is_confirm === '{{ BOOL_FALSE }}')) {
                var status_job = true;
                defaultStatusJob(status_job);
            } else {
                var status_job = false;
                defaultStatusJob(status_job);
            }
        }

        let dropdownState = 'baht'

        $('#modal-wage-job').on('show.bs.modal', function(e) {
            const amount_type = $('#amount_type_field').val();
            if (amount_type === '{{ \App\Enums\AmountTypeEnum::PERCENT }}') {
                select_amount_type('percent')
            } else {
                select_amount_type('baht')
            }
        })

        $('#modal-wage-job').on('hidden.bs.modal', function(e) {
            select_amount_type('baht')
            $('#input_amount').hide()
            $('#service_type_id_field_hidden').val('')
        })

        $('#dropdown-toggle-select-type').on('show.bs.dropdown', function() {
            if (dropdownState === 'baht') {
                $('#select_amount_type_baht').addClass('active')
                $('#select_amount_type_percent').removeClass('active')
            } else {
                $('#select_amount_type_baht').removeClass('active')
                $('#select_amount_type_percent').addClass('active')
            }
        })

        function select_amount_type(type) {
            if (type === 'baht') {
                dropdownState = 'baht'
                $('#amount_type_text').html('฿');
                $('#amount_type_field').val('{{ \App\Enums\AmountTypeEnum::BAHT }}')
            } else if (type === 'percent') {
                dropdownState = 'percent'
                $('#amount_type_text').html('%');
                $('#amount_type_field').val('{{ \App\Enums\AmountTypeEnum::PERCENT }}')
            }
        }

        @if (Route::is('*.edit'))
            if ($('#job_type').val() === '{{ DrivingJobTypeStatusEnum::OTHER }}') {
                addValidateText($('#job_type').val())
            }
        @endif

        $("#job_type").on("change.select2", function(e) {
            const job_type = $(this).val();
            addValidateText(job_type)
        });

        function addValidateText(job_type) {
            const defaultLabelRemark = '{{ __('driving_jobs.remark') }}'
            const defaultLabelJobId = '{{ __('driving_jobs.job_id') }}'
            if (job_type === '{{ DrivingJobTypeStatusEnum::OTHER }}') {
                $('label[for="remark"]').html(defaultLabelRemark + ` <span class="text-danger">*</span>`);
                $('label[for="job_id"]').html(defaultLabelJobId);
            } else {
                $('label[for="remark"]').html(defaultLabelRemark);
                $('label[for="job_id"]').html(defaultLabelJobId + ` <span class="text-danger">*</span>`);
            }
        }

        $('input[name="status"]').on("click", function() {
            var status = $('input[name="status"]:checked').val();
            if (status === '{{ DrivingJobStatusEnum::COMPLETE }}') {
                var status_job = true;
                defaultStatusJob(status_job);
            } else {
                var status_job = false;
                defaultStatusJob(status_job);
            }
        });

        $('#car_type').on("change.select2", function(e) {
            clearCarID();
            clearCarDetailInput();
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
            axios.get("{{ route('admin.driving-jobs.default-car-license') }}", {
                params: {
                    car_id: data.id,
                    job_id: job_id,
                    job_type: job_type,
                }
            }).then(response => {
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#car_class").val(e.car_class_name);
                            $("#car_color").val(e.car_colors_name);
                        });
                    }
                    if (response.data.data_import) {
                        if (response.data.data_import.job_type === 'App\\Models\\ImportCarLine') {
                            $("#delivery_date").val(response.data.data_import.delivery_date);
                            $("#import_delivery_place").val(response.data.data_import
                                .import_delivery_place);
                        }
                    }
                    $("#zone").val('');
                    $("#slot_no").val('');
                    if (response.data.zone) {
                        $("#zone").val(response.data.zone.code);
                        $("#slot_no").val(response.data.zone.car_park_number);
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
            $("#oil_type").val('');
        }

        function clearCarID() {
            $("#car_id").val(null).trigger('change');
        }

        $(".btn-save-draft").on("click", function() {
            let storeUri = "{{ route('admin.driving-jobs.update-status') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            saveForm(storeUri, formData);
        });

        $(".btn-save-complete-modal").on("click", function() {
            document.getElementById("is_confirm_wage").value = $(this).attr('data-status');
            $('#modal-complete').modal('show');
        });
    </script>
@endpush
