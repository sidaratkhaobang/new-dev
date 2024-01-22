@extends('admin.layouts.layout')

@section('page_title', $page_title)
@push('styles')
    <style>
        .tag-field {
            display: flex;
            flex-wrap: wrap;
            padding: 3px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-control.js-tag-input {
            border: none;
            transition: none;
        }

        input {
            border: 0;
            outline: 0;
        }

        .tag {
            display: flex;
            align-items: center;
            height: 30px;
            margin-right: 5px;
            margin-bottom: 1px;
            padding: 0 8px;
            color: #fff;
            background: #0665d0;
            border-radius: 100px;
            cursor: pointer;
        }

        .tag-close {
            display: inline-block;
            margin-left: 0;
            width: 0;
            transition: 0.2s all;
            overflow: hidden;
        }

        .tag:hover .tag-close {
            margin-left: 10px;
            width: 10px;
        }

        .btn-sm-rounded {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endpush
@section('content')
    <form id="save-form">
        @include('admin.components.creator')
        @include('admin.accident-informs.sections.car-accident-detail')
        @include('admin.accident-informs.sections.car-detail')
        @include('admin.accident-informs.sections.accident-detail')
        @include('admin.accident-informs.sections.folklift-detail')
        @include('admin.accident-informs.sections.replacement-car-detail')
        @include('admin.accident-informs.sections.contact-accident-detail')
        @include('admin.accident-informs.submit')
        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="job_type" :value="null" />
        <x-forms.hidden id="job_id" :value="null" />
    </form>
@endsection
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.accident-informs.scripts.input-tag')
@if (isset($btn_group_sheet))
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-inform-sheets.store'),
    ])
@else
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-informs.store'),
    ])
@endif

@include('admin.components.select2-ajax', [
    'id' => 'province',
    'url' => route('admin.util.select2-garage.province'),
])

@include('admin.components.select2-ajax', [
    'id' => 'district',
    'url' => route('admin.util.select2-garage.amphure'),
    'parent_id' => 'province',
])

@include('admin.components.select2-ajax', [
    'id' => 'subdistrict',
    'url' => route('admin.util.select2-garage.district'),
    'parent_id' => 'district',
])

@include('admin.components.select2-ajax', [
    'id' => 'cradle',
    'url' => route('admin.util.select2-garage.cradle'),
    'parent_id' => 'province',
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'optional_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $optional_files,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'replacement_car_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $replacement_car_files,
    'show_url' => true
])


@push('scripts')
    <script>
        $("#chassis_no").prop('readonly', true);
        $("#car_class").prop('readonly', true);
        $("#worksheet_no_ref").prop('readonly', true);
        $("#customer_name").prop('readonly', true);
        $("#policy_number").prop('readonly', true);
        $("#insurance_company").prop('readonly', true);
        $("#insurance_tel").prop('readonly', true);
        $("#coverage_start_date").prop('readonly', true);
        $("#coverage_end_date").prop('readonly', true);
        $("#amount_deceased_total").prop('readonly', true);
        $("#amount_wounded_total").prop('readonly', true);
        $("#contact_fullname_1").prop('disabled', true);
        $("#contact_fullname_2").prop('disabled', true);
        $("#contact_fullname_3").prop('disabled', true);
        $("#contact_department_1").prop('disabled', true);
        $("#contact_department_2").prop('disabled', true);
        $("#contact_department_3").prop('disabled', true);
        $("#contact_tel_1").prop('disabled', true);
        $("#contact_tel_2").prop('disabled', true);
        $("#contact_tel_3").prop('disabled', true);

        $status = '{{ isset($view) }}';
        if ($status) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $("input[type=checkbox]").attr('disabled', true);
        }
        $('#zip_code').prop('disabled', true);

        $("#subdistrict").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.garages.zip-code') }}", {
                params: {
                    id: data.id,
                }
            }).then(response => {
                if (response.data.success) {
                    $("#zip_code").val(response.data.data.zip_code);
                }
            });
        });

        $('input[name="is_parties"]').on("click", function() {
            if ($('input[name="is_parties"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("wrong_type_id").style.display = "block"
            } else {
                document.getElementById("wrong_type_id").style.display = "none"
                $('#wrong_type').val('').change();
            }
        });

        $('input[name="is_wounded"]').on("click", function() {
            if ($('input[name="is_wounded"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("amount_wounded_driver_id").style.display = "block"
                document.getElementById("amount_wounded_parties_id").style.display = "block"
                document.getElementById("amount_wounded_total_id").style.display = "block"
            } else {
                document.getElementById("amount_wounded_driver_id").style.display = "none"
                document.getElementById("amount_wounded_parties_id").style.display = "none"
                document.getElementById("amount_wounded_total_id").style.display = "none"
                $('#amount_wounded_driver').val('');
                $('#amount_wounded_parties').val('');
                $('#amount_wounded_total').val('');
            }
        });

        $('input[name="is_deceased"]').on("click", function() {
            if ($('input[name="is_deceased"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("amount_deceased_driver_id").style.display = "block"
                document.getElementById("amount_deceased_parties_id").style.display = "block"
                document.getElementById("amount_deceased_total_id").style.display = "block"
            } else {
                document.getElementById("amount_deceased_driver_id").style.display = "none"
                document.getElementById("amount_deceased_parties_id").style.display = "none"
                document.getElementById("amount_deceased_total_id").style.display = "none"
                $('#amount_deceased_driver').val('');
                $('#amount_deceased_parties').val('');
                $('#amount_deceased_total').val('');
            }
        });

        $('input[name="is_repair"]').on("click", function() {
            if ($('input[name="is_repair"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("cradle_id").style.display = "block"
            } else {
                document.getElementById("cradle_id").style.display = "none"
                $('#cradle').val('').change();
            }
        });

        $('input[name="first_lifting"]').on("click", function() {
            if ($('input[name="first_lifting"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("first_lifter_id").style.display = "block"
                document.getElementById("first_lift_date_id").style.display = "block"
                document.getElementById("first_lift_price_id").style.display = "block"
                document.getElementById("first_lift_tel_id").style.display = "block"
            } else {
                document.getElementById("first_lifter_id").style.display = "none"
                document.getElementById("first_lift_date_id").style.display = "none"
                document.getElementById("first_lift_price_id").style.display = "none"
                document.getElementById("first_lift_tel_id").style.display = "none"
                $('#first_lifter').val('').change();
                $('#first_lift_date').val('').change();
                $('#first_lift_price').val('').change();
                $('#first_lift_tel').val('').change();
            }
        });

        $('input[name="need_folklift"]').on("click", function() {
            if ($('input[name="need_folklift"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("lift_date_id").style.display = "block"
                document.getElementById("lift_from_id").style.display = "block"
                document.getElementById("lift_to_id").style.display = "block"
                document.getElementById("lift_price_id").style.display = "block"
            } else {
                document.getElementById("lift_date_id").style.display = "none"
                document.getElementById("lift_from_id").style.display = "none"
                document.getElementById("lift_to_id").style.display = "none"
                document.getElementById("lift_price_id").style.display = "none"
                $('#lift_date').val('').change();
                $('#lift_from').val('').change();
                $('#lift_to').val('').change();
                $('#lift_price').val('').change();
            }
        });

        $('input[name="is_replacement"]').on("click", function() {
            if ($('input[name="is_replacement"]:checked').val() === '{{ BOOL_TRUE }}') {
                document.getElementById("replacement_expect_date_id").style.display = "block"
                document.getElementById("replacement_type_id").style.display = "block"
                document.getElementById("is_driver_replacement_id").style.display = "block"
                document.getElementById("replacement_expect_place_id").style.display = "block"
                document.getElementById("replacement_car_files_id").style.display = "block"
            } else {
                document.getElementById("replacement_expect_date_id").style.display = "none"
                document.getElementById("replacement_type_id").style.display = "none"
                document.getElementById("is_driver_replacement_id").style.display = "none"
                document.getElementById("replacement_expect_place_id").style.display = "none"
                document.getElementById("replacement_car_files_id").style.display = "none"
                $('#replacement_expect_date').val('').change();
                $('#replacement_type').val('').change();
                $('#is_driver_replacement').val('').change();
                $('#replacement_expect_place').val('').change();
                $('#replacement_car_files').val('').change();
                $('input[name="is_driver_replacement"]').prop('checked', false);
            }
        });

        // wounded
        $(document).ready(function() {
            $('#amount_wounded_driver').on('input', function() {
                var amount_wounded_driver = parseInt($(this).val());
                var amount_wounded_parties = parseInt($('#amount_wounded_parties').val());
                if (isNaN(amount_wounded_driver)) {
                    amount_wounded_driver = 0
                }
                if (isNaN(amount_wounded_parties)) {
                    amount_wounded_parties = 0
                }
                var amount_wounded_total = amount_wounded_driver + amount_wounded_parties;
                if (isNaN(amount_wounded_total)) {
                    amount_wounded_total = 0
                }

                $('#amount_wounded_total').val(amount_wounded_total);
            });
        });

        $(document).ready(function() {
            $('#amount_wounded_parties').on('input', function() {
                var amount_wounded_parties = parseInt($(this).val());
                var amount_wounded_driver = parseInt($('#amount_wounded_driver').val());
                if (isNaN(amount_wounded_driver)) {
                    amount_wounded_driver = 0
                }
                if (isNaN(amount_wounded_parties)) {
                    amount_wounded_parties = 0
                }
                var amount_wounded_total = amount_wounded_driver + amount_wounded_parties;
                if (isNaN(amount_wounded_total)) {
                    amount_wounded_total = 0
                }

                $('#amount_wounded_total').val(amount_wounded_total);
            });
        });

        // deceased
        $(document).ready(function() {
            $('#amount_deceased_driver').on('input', function() {
                var amount_deceased_driver = parseInt($(this).val());
                var amount_deceased_parties = parseInt($('#amount_deceased_parties').val());
                if (isNaN(amount_deceased_parties)) {
                    amount_deceased_parties = 0
                }
                if (isNaN(amount_deceased_driver)) {
                    amount_deceased_driver = 0
                }
                var amount_deceased_total = amount_deceased_driver + amount_deceased_parties;
                if (isNaN(amount_deceased_total)) {
                    amount_deceased_total = 0
                }

                $('#amount_deceased_total').val(amount_deceased_total);
            });
        });

        $(document).ready(function() {
            $('#amount_deceased_parties').on('input', function() {
                var amount_deceased_parties = parseInt($(this).val());
                var amount_deceased_driver = parseInt($('#amount_deceased_driver').val());
                if (isNaN(amount_deceased_parties)) {
                    amount_deceased_parties = 0
                }
                if (isNaN(amount_deceased_driver)) {
                    amount_deceased_driver = 0
                }
                var amount_deceased_total = amount_deceased_driver + amount_deceased_parties;
                if (isNaN(amount_deceased_total)) {
                    amount_deceased_total = 0
                }

                $('#amount_deceased_total').val(amount_deceased_total);
            });
        });

        $("#license_plate").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.accident-informs.default-car-license') }}", {
                params: {
                    license_plate: data.id,
                }
            }).then(response => {
                $("#worksheet_no_ref").val('');
                $("#worksheet_no_ref").val('');
                if (response.data.success) {
                    if (response.data.data.length > 0) {
                        response.data.data.forEach((e) => {
                            $("#car_class").val(e.car_class_name);
                            $("#chassis_no").val(e.chassis_no);
                        });
                    }

                    if (response.data.worksheet) {
                        $("#worksheet_no_ref").val(response.data.worksheet.worksheet_no);
                        $("#customer_name").val(response.data.worksheet.customer_name);


                    }

                    if (response.data.job_type) {
                        $("#job_type").val(response.data.job_type);
                    }

                    if (response.data.job_id) {
                        $("#job_id").val(response.data.job_id);
                    }
                }
            });
        });

        $(document).ready(function() {
            $("#garage-all").click(function(e) {
                e.preventDefault();
                var province = $('#province').val();
                if (province != null) {
                    var routeUrl = "{{ route('admin.garages.index', ['province_id' => '']) }}";
                    var newUrl = routeUrl.replace('province_id=', 'province_id=' + province);
                } else {
                    return warningAlert("{{ __('lang.require_province') }}");
                }
                var win = window.open(newUrl, '_blank');
                win.focus();

            });
        });

        $(".btn-save-review").on("click", function() {
            var check_route = @json(isset($btn_group_sheet));
            let storeUri = null;
            if (check_route == true) {
                storeUri = "{{ route('admin.accident-inform-sheets.store') }}";
            } else {
                storeUri = "{{ route('admin.accident-informs.store') }}";
            }
            var formData = new FormData(document.querySelector('#save-form'));
            var $tags = document.querySelector('.js-tags');

            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }

            formData.append('tags', tags);
            saveForm(storeUri, formData);

        });
    </script>
@endpush
