@extends('admin.layouts.layout')

@section('page_title', $page_title . ' ' . $accident_order->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(
            __('accident_orders.class_job_' . $accident_order->status),
            __('accident_orders.status_job_' . $accident_order->status),
            null,
        ) !!}
    @endif
@endsection
@section('history')
    @include('admin.components.btns.history')
@endsection
@section('btn-nav')
    {{-- <span class="d-flex justify-content-end"> --}}
    {{-- <a href="#" class="btn btn-primary float-end ">
        <i class="icon-printer"></i>
        {{ __('accident_informs.print_repair_sheet') }}
    </a> --}}
    @if (!in_array($accident_order->status, [AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST]))
        <a target="_blank"
            href="{{ route('admin.accident-orders.accident-order-pdf', ['accident_order' => $accident_order->id]) }}"
            class="btn btn-primary">
            <i class="icon-printer"></i>
            {{ __('accident_informs.print_repair_sheet') }}
        </a>
    @endif
    {{-- </span> --}}

@endsection

@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }

        .img-fluid {
            /* width: 250px; */
            height: 100px;
            object-fit: cover;
        }

        .car-border {
            border: 1px solid #CBD4E1;
            width: 400px;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
        }

        .hide {
            display: none !important;
        }

        .show {
            display: block !important;
            opacity: 1;
            animation: fade 1s;
        }

        @keyframes fade {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush

@push('custom_styles')
    <style>
        .badge-custom {
            min-width: 20rem;
        }
    </style>
@endpush
@section('content')
    @include('admin.components.creator')
    @if (isset($btn_group_sheet))
        @include('admin.accident-informs.sections.btn-group-sheet')
    @else
        @include('admin.accident-orders.sections.btn-group')
    @endif
    <form id="save-form-edit-order">
        @include('admin.accident-orders.sections.car-info')
        {{-- @include('admin.accident-informs.sections.replacement-car-detail-edit') --}}
        @include('admin.accident-informs.sections.accident-detail-edit')
        @include('admin.accident-informs.sections.folklift-detail-edit')
        @include('admin.accident-informs.sections.replacement-car-detail-edit')
        @include('admin.accident-informs.sections.cost-detail-edit')
        @if (strcmp($accident_order->status, AccidentRepairStatusEnum::TTL) === 0)
            @include('admin.accident-orders.sections.total-loss')
        @endif

        <x-forms.hidden id="id" :value="$d->id" />
        <x-forms.hidden id="job_type" :value="null" />
        <x-forms.hidden id="job_id" :value="null" />
        <x-forms.hidden id="accident_order_id" :value="$accident_order->id" />
        @include('admin.accident-orders.submit')
    </form>
    @include('admin.components.transaction-modal')
@endsection

@include('admin.accident-informs.scripts.folklift-script')
@include('admin.accident-orders.scripts.replacement-script')
@include('admin.accident-informs.scripts.cost-script')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.accident-informs.scripts.input-tag')
{{-- @if (isset($btn_group_sheet))
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-inform-sheets.store-edit-accident'),
    ])
@else
    @include('admin.components.form-save', [
        'store_uri' => route('admin.accident-informs.store-edit-accident'),
    ])
@endif --}}

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
    'id' => 'replacement_car_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $replacement_car_files,
])

@include('admin.components.upload-image', [
    'id' => 'slide_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
])

@include('admin.components.upload-image', [
    'id' => 'total_loss_files',
    'max_files' => 100,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $total_loss_files,
])


@include('admin.components.upload-image', [
    'id' => 'replacment_file',
    'max_files' => 1,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => [],
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

        $status_gps = '{{ $gps_remove_stop_signal->id }}';
        if ($status_gps) {
            $("#compensation").prop('readonly', true);
            $("#carcass_cost").prop('readonly', true);
            $("#inform_date").prop('disabled', true);
            $("input[type=radio][name='is_check_gps']").attr('disabled', true);
            $("input[type=checkbox][name='is_stop_gps[]']").attr('disabled', true);
            $("input[type=checkbox][name='is_status_rental_car[]']").attr('disabled', true);
            $("input[type=checkbox][name='is_pick_up_book[]']").attr('disabled', true);
            // $("#is_stop_gps").prop('disabled', true);
            // $("#inform_date").prop('disabled', true);
            // $("#inform_date").prop('disabled', true);
            // $("#inform_date").prop('disabled', true);
        }

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


        $(document).ready(function() {
            var amount_wounded_parties = parseInt($('#amount_wounded_parties').val());
            var amount_wounded_driver = parseInt($('#amount_wounded_driver').val());
            var amount_wounded_total = amount_wounded_driver + amount_wounded_parties;
            if (isNaN(amount_wounded_total)) {
                amount_wounded_total = 0
            }
            $('#amount_wounded_total').val(amount_wounded_total);


            var amount_deceased_parties = parseInt($('#amount_deceased_parties').val());
            var amount_deceased_driver = parseInt($('#amount_deceased_driver').val());
            var amount_deceased_total = amount_deceased_driver + amount_deceased_parties;
            if (isNaN(amount_deceased_total)) {
                amount_deceased_total = 0
            }
            $('#amount_deceased_total').val(amount_deceased_total);

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


        $('input[name="is_stop_gps[]"]').on("click", function() {
            if ($('input[name="is_stop_gps[]"]:checked').val() === '{{ BOOL_TRUE }}') {
                $('.gps').show();
            } else {
                $('.gps').hide();
            }
        });

        $("#license_plate").on('select2:select', function(e) {
            var data = e.params.data;
            axios.get("{{ route('admin.accident-informs.default-car-license') }}", {
                params: {
                    license_plate: data.id,
                }
            }).then(response => {
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

        $(".btn-save-review-form").on("click", function() {
            var check_route = @json(isset($btn_group_sheet));
            let storeUri = null;
            storeUri = "{{ route('admin.accident-orders.store-edit-accident') }}";
            var formData = new FormData(document.querySelector('#save-form-edit-order'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }

            if (window.addAccidentVue) {
                let data = window.addAccidentVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.slide_files && item.slide_files.length > 0) {
                            item.slide_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('slide_file[table_row_' + item.index +
                                        '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
                // deleted exists files
                let delete_ids = window.addAccidentVue.getPendingDeleteMediaIds();
                if (delete_ids && delete_ids.length > 0) {
                    delete_ids.forEach((item) => {
                        if (item.pending_delete_slide_files && item.pending_delete_slide_files.length >
                            0) {
                            item.pending_delete_slide_files.forEach(function(id) {
                                formData.append('pending_delete_slide_files[]', id);
                            });
                        }
                    });
                }
                //delete slide row
                let delete_slide_ids = window.addAccidentVue.pending_delete_slide_ids;
                if (delete_slide_ids && (delete_slide_ids.length > 0)) {
                    delete_slide_ids.forEach(function(delete_slide_ids) {
                        formData.append('delete_slide_ids[]', delete_slide_ids);
                    });
                }
            }

            if (window.addAccidentReplacementVue) {
                let data = window.addAccidentReplacementVue.getFiles();
                //delete replacement row
                let delete_replacement_ids = window.addAccidentReplacementVue.pending_delete_replacement_ids;
                if (delete_replacement_ids && (delete_replacement_ids.length > 0)) {
                    delete_replacement_ids.forEach(function(delete_replacement_ids) {
                        formData.append('delete_replacement_ids[]', delete_replacement_ids);
                    });
                }
            }

            if (window.addAccidentCostVue) {
                //delete cost row
                let delete_cost_ids = window.addAccidentCostVue.pending_delete_cost_ids;
                if (delete_cost_ids && (delete_cost_ids.length > 0)) {
                    delete_cost_ids.forEach(function(delete_cost_ids) {
                        formData.append('delete_cost_ids[]', delete_cost_ids);
                    });
                }
            }


            saveForm(storeUri, formData);

        });
    </script>
@endpush
