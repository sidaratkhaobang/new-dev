@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
    <style>
        .block-content-full {
            background-color: #FFF8E6;
        }

        .block-bordered-custom {
            border: 1px solid #EFB008 !important;
        }

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
        }

        div.check-status {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .form-progress-bar .form-progress-bar-steps li,
        .form-progress-bar .form-progress-bar-labels li {
            width: 16.6%;
            float: left;
            position: relative;
        }

        .form-progress-bar-line {
            background-color: #f3f3f3;
            content: "";
            height: 2px;
            left: 0;
            /* position: absolute; */
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            /* width: 70%; */
            border-bottom: 1px solid #dddddd;
            border-top: 1px solid #dddddd;
            margin-left: 20px;
            margin-right: 30px;
        }

        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: #dddddd;
            border-radius: 50%;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            position: relative;
            text-align: center;
            width: 40px;
        }

        .form-progress-bar .form-progress-bar-steps span.check,
        .form-progress-bar .form-progress-bar-steps span.check {
            background-color: #6f9c40;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending,
        .form-progress-bar .form-progress-bar-steps span.pending {
            background-color: #e69f17;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.pending-secondary,
        .form-progress-bar .form-progress-bar-steps span.pending-secondary {
            background-color: #909395;
            color: #ffffff;
        }

        .form-progress-bar .form-progress-bar-steps span.reject,
        .form-progress-bar .form-progress-bar-steps span.reject {
            background-color: red;
            color: #ffffff;
        }

        .bg-pending-previous {
            background-color: #909395;
        }

        .bg-check {
            background-color: #6f9c40;
        }

        .bg-pending {
            background-color: #e69f17;
        }
    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        <div class="block-header">
            <h3 class="block-title">ข้อมูลใบขอซื้อ: {{ $d->pr_no }}</h3>
        </div>
        <div class="block-content">
            <form id="save-form">

                @include('admin.purchase-requisitions.sections.purchase')
                @include('admin.purchase-requisitions.sections.pr-car-accessory')
                @include('admin.purchase-requisitions.sections.pr-upload')

                <x-forms.hidden id="id" :value="$d->id" />
                @include('admin.purchase-requisitions.sections.submit')
                {{-- <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.purchase-requisitions.index') }}">{{ __('lang.back') }}</a>
                        @if ($d->status == PRStatusEnum::DRAFT)
                            <button type="button"
                                class="btn btn-primary btn-save-form">{{ __('lang.save_draft') }}</button>
                        @endif
                    </div>
                </div> --}}
            </form>
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.purchase-requisitions.store'),
])

@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.purchase-requisitions.scripts.pr-car-script')
@include('admin.purchase-requisitions.scripts.pr-accessory-script')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_class_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-class'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_color_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.car-colors'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accessory_field',
    'modal' => '#modal-car-accessory',
    'url' => route('admin.util.select2.accessories-type-accessory'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'rental_images',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $rental_images_files,
    'show_url' => true
])

@include('admin.components.upload-image', [
    'id' => 'approve_images',
    // 'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $approve_images_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'replacement_approve_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $replacement_approve_files,
    'show_url' => true,
])

@include('admin.components.upload-image', [
    'id' => 'refer_images',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf',
    'mock_files' => $refer_images_files,
    'show_url' => true
])

@include('admin.components.select2-ajax', [
    'id' => 'parent_id',
    'url' => route('admin.util.select2.pr-parent'),
])

@push('scripts')
    <script>
        // Readonly Field
        $('#purchaser').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#request_date').prop("readonly", true);
        $('#pr_no').prop("readonly", true);
        $('#review_by').prop('readonly', true);
        $('#reviewed_at').prop('readonly', true);
        $('#review_department').prop('readonly', true);
        $('#reject_reason').prop('readonly', true);
        //rental
        $('#customer_type').prop('disabled', true);
        $('#customer_name').prop('disabled', true);
        $('#job_type').prop('disabled', true);
        $('#rental_duration').prop('disabled', true);

        $(".btn-show-cancel-modal").on("click", function() {
            document.getElementById("cancel_status").value = $(this).attr('data-status');
            document.getElementById("cancel_id").value = document.getElementById("id").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
            $('#modal-cancel').modal('show');
        });

        $("#rental_type").on("change.select2", function(e) {
            $('#reference_id').val(null).trigger('change');
            $('#customer_type').val('');
            $('#customer_name').val('');
            $('#rental_refer').val('');
            $('#contract_refer').val('');
            $('#job_type').val('');
            $('#rental_duration').val('');
            var rental_type = $("#rental_type").val();
            if (rental_type === '{{ \App\Enums\RentalTypeEnum::SHORT }}') {
                document.getElementById("replacement_section").style.display = "none"
                document.getElementById("rental_select").style.display = "block"
                document.getElementById("short_rental_1").style.display = "block"
                document.getElementById("short_rental_2").style.display = "block"
                document.getElementById("long_rental_1").style.display = "none"
                document.getElementById("long_rental_2").style.display = "none"
                document.getElementById("short_rental_3").style.display = "block"
                document.getElementById("short_rental_4").style.display = "block"
            } else if (rental_type === '{{ \App\Enums\RentalTypeEnum::LONG }}') {
                document.getElementById("replacement_section").style.display = "none"
                document.getElementById("rental_select").style.display = "block"
                document.getElementById("short_rental_1").style.display = "none"
                document.getElementById("short_rental_2").style.display = "none"
                document.getElementById("long_rental_1").style.display = "block"
                document.getElementById("long_rental_2").style.display = "block"
                document.getElementById("short_rental_3").style.display = "none"
                document.getElementById("short_rental_4").style.display = "none"
            } else if (rental_type === '{{ \App\Enums\RentalTypeEnum::REPLACEMENT }}') {
                document.getElementById("rental_select").style.display = "none"
                document.getElementById("replacement_section").style.display = "block"
            } else {
                document.getElementById("rental_select").style.display = "none"
                document.getElementById("replacement_section").style.display = "none"
            }
        });

        $("#reference_id").select2({
            placeholder: "{{ __('lang.select_option') }}",
            allowClear: true,
            ajax: {
                delay: 250,
                url: function(params) {
                    return "{{ route('admin.purchase-requisition.rental-type-by-id') }}";
                },
                type: 'GET',
                data: function(params) {
                    rental_type = $("#rental_type").val();
                    return {
                        rental_type: rental_type,
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

        $(".btn-save-draft").on("click", function() {
            let storeUri = "{{ route('admin.purchase-requisitions.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
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

                    let pending_add_ids = dropzone.options.params.pending_add_ids;
                    if (pending_add_ids.length > 0) {
                        pending_add_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_add_ids[]', id);
                        });
                    }
                });
            }
            formData.append('status_draft', true);

            saveForm(storeUri, formData);
        });

        $('#amount_accessory_field').prop('readonly', true);
        $("#amount_per_car_accessory_field").keyup(function() {
            var amount_car = document.getElementById("amount_car_field").value;
            var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field").value;

            $('#amount_accessory_field').val(amount_per_car_accessory * amount_car);
        });

        $("#amount_car_field").keyup(function() {
            var amount_car = document.getElementById("amount_car_field").value;
            var amount_per_car_accessory = document.getElementById("amount_per_car_accessory_field").value;

            $('#amount_accessory_field').val(amount_per_car_accessory * amount_car);
        });

        // function transaction() {
        //     $('#transaction').modal('show');
        // }
    </script>
@endpush
