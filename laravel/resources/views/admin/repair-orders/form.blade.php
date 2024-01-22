@extends('admin.layouts.layout')
@section('page_title', $page_title . '' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('repairs.repair_class_' . $d->status), __('repairs.repair_text_' . $d->status), null) !!}
    @endif
@endsection

@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        @if (in_array($mode, [MODE_CREATE, MODE_UPDATE]) && sizeof($replacement_list) == 0)
        <button class="btn btn-primary" onclick="showReplacementSection()" id="btn_add_replacement">
            <i class="icon-add-circle me-1"></i>
            {{ __('repairs.replacement_create') }}
        </button>
        @endif
        @if (empty($view))
            @if (in_array($d->status, [RepairStatusEnum::PENDING_REPAIR, RepairStatusEnum::IN_PROCESS]))
                <button type="button" onclick="openSendMailModal()" class="btn btn-primary"><i
                        class="fa fa-location-arrow"></i>&nbsp;{{ __('repair_orders.btn_mail') }}</button>
            @endif
        @endif
        @if (in_array($d->status, [
                RepairStatusEnum::PENDING_REPAIR,
                RepairStatusEnum::IN_PROCESS,
                RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                RepairStatusEnum::REJECT_QUOTATION,
                RepairStatusEnum::EXPIRED,
                RepairStatusEnum::COMPLETED,
                RepairStatusEnum::CANCEL,
            ]))
            <a target="_blank" href="{{ route('admin.repair-orders.print-pdf', ['repair_order' => $d]) }}"
                class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
                {{ __('repair_orders.btn_print') }}
            </a>
        @endif
    </nav>
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

        .tag-field {
            display: flex;
            flex-wrap: wrap;
            /* height: 50px; */
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
            border-radius: 6px;
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
    <form id="save-form">
        @include('admin.repair-orders.sections.user')
        @if (in_array($d->status, [
                RepairStatusEnum::PENDING_REPAIR,
                RepairStatusEnum::IN_PROCESS,
                RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                RepairStatusEnum::REJECT_QUOTATION,
                RepairStatusEnum::EXPIRED,
                RepairStatusEnum::COMPLETED,
                RepairStatusEnum::CANCEL,
            ]))
            @include('admin.repair-orders.sections.btn-group')
        @endif

        @include('admin.repair-orders.sections.repair-info')

        @include('admin.repairs.sections.service-center')

        {{-- @include('admin.repairs.sections.replacement') --}}
        @include('admin.repairs.sections.replacement-new')

        @include('admin.repair-orders.sections.car-info')

        @include('admin.repair-orders.sections.repair-order-info')

        @include('admin.repair-orders.sections.repair-order-line')

        @if (in_array($d->status, [
                RepairStatusEnum::IN_PROCESS,
                RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                RepairStatusEnum::REJECT_QUOTATION,
                RepairStatusEnum::EXPIRED,
                RepairStatusEnum::COMPLETED,
                RepairStatusEnum::CANCEL,
            ]))
            @include('admin.repair-orders.sections.center-info')
        @endif


        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                    <x-forms.hidden id="center" :value="$d->center_id" />
                @if ($d->repair_id)
                    <x-forms.hidden id="center" :value="$d->center_id" />
                    <x-forms.hidden id="repair_no" :value="$d->repair_id" />
                    <x-forms.hidden id="check_distance" :value="$d->check_distance" />
                    @if (in_array($d->replacement_date, [BOOL_TRUE, BOOL_FALSE]))
                        <x-forms.hidden id="replacement_date" :value="$d->replacement_date" />
                        <x-forms.hidden id="replacement_type" :value="$d->replacement_type" />
                        <x-forms.hidden id="replacement_place" :value="$d->replacement_place" />
                    @endif
                @endif
                <x-forms.hidden id="car_id" :value="null" />
                <x-forms.hidden id="main_car_id" :value="$d->car_id" />
                <x-forms.hidden id="contact" :value="$d->contact" />
                <x-forms.hidden id="tel" :value="$d->tel" />
                <x-forms.hidden id="redirect_route" :value="$index_uri" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        @if (in_array($d->status, [
                                RepairStatusEnum::PENDING_REPAIR,
                                RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                                RepairStatusEnum::REJECT_QUOTATION,
                                RepairStatusEnum::EXPIRED,
                            ]))
                            <button type="button" class="btn btn-danger btn-cancel-form" data-status="CANCEL"><i
                                    class="far fa-times-circle"></i>
                                {{ __('repair_orders.btn_cancel') }}</button>
                        @endif
                        <a class="btn btn-secondary" href="{{ route($index_uri) }}">{{ __('lang.back') }}</a>
                        @if (empty($view))
                            <button type="button" class="btn btn-primary btn-save-form-data">{{ __('lang.save') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('admin.repair-orders.modals.repair-order-mail')
        @include('admin.repair-orders.modals.expired-modal')
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.repair-orders.store'),
])
@include('admin.repairs.scripts.input-script')
@include('admin.repair-orders.scripts.replacement-script')
@include('admin.repair-orders.scripts.repair-order-line-script')
@include('admin.repair-orders.scripts.repair-line-script')
@include('admin.repair-orders.scripts.repair-script')
@include('admin.repair-orders.scripts.input-tag')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'repair_documents',
    'max_files' => 10,
    'mock_files' => $repair_documents_files ?? [],
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'show_url' => true,
    'view_only' => true,
])
@include('admin.components.upload-image', [
    'id' => 'replacement_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'mock_files' => [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])
@include('admin.components.upload-image', [
    'id' => 'expense_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'mock_files' => $expense_files ?? [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@include('admin.components.select2-ajax', [
    'id' => 'temp_slide_id',
    'modal' => '#replacement-modal',
    'url' => route('admin.util.select2-repair.slide-list'),
])

@push('scripts')
    <script>
        function openModalCondition() {
            $('#modal-condition').modal('show');
        }

        function openModalAccident() {
            $('#modal-accident-history').modal('show');
        }

        function openModalMaintain() {
            $('#modal-maintain-history').modal('show');
        }

        $edit = '{{ isset($edit) }}';
        if ($edit) {
            $('#repair_no').prop('disabled', true);
            $('#check_distance').prop('disabled', true);
            $('#user_file').prop('disabled', true);
            $('#center').prop('disabled', true);
            $is_replacement = '{{ $d->is_replacement }}';
            $('input[name="is_replacement"][value="' + $is_replacement + '"]').prop('checked', true);
            $in_center = '{{ $d->in_center }}';
            $('input[name="in_center"][value="' + $in_center + '"]').prop('checked', true);
            $out_center = '{{ $d->out_center }}';
            $('input[name="out_center"][value="' + $out_center + '"]').prop('checked', true);
            $is_driver_out_center = '{{ $d->is_driver_out_center }}';
            $('input[name="is_driver_out_center"][value="' + $is_driver_out_center + '"]').prop('checked', true);
            $is_driver_in_center = '{{ $d->is_driver_in_center }}';
            $('input[name="is_driver_in_center"][value="' + $is_driver_in_center + '"]').prop('checked', true);
        }
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }

        var status = '{{ $d->status }}';
        if (status == '{{ RepairStatusEnum::EXPIRED }}') {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $('#repair_date_new').prop('disabled', false);
            $('#center_date_new').prop('disabled', false);
            if ($view) {
                $('#repair_date_new').prop('disabled', false);
                $('#center_date_new').prop('disabled', false);
            }

            var date = new Date();
            var today = moment(date).format('YYYY-MM-DD HH:mm');
            $(document).ready(function() {
                $("#repair_date_new").val(today);
                flatpickr("#repair_date_new", {
                    defaultDate: today,
                });
                $('#modal-repair-order-expired').modal('show');
            });
        }

        function openSendMailModal() {
            var tags =
                @if ($d->center_mail)
                    [@json($d->center_mail)]
                @else
                    []
                @endif ;
            var $tags = document.querySelector('.js-tags');
            if (tags.length > 0) {
                render(tags, $tags);
            }
            $("#modal-repair-order-mail").modal("show");
        }

        function sendMail() {
            var repair_order_id = document.getElementById("id").value;
            var $tags = document.querySelector('.js-tags');
            showLoading();
            axios.get("{{ route('admin.repair-orders.send-mail') }}", {
                params: {
                    repair_order_id: repair_order_id,
                    tags: tags
                }
            }).then(response => {
                hideLoading();
                $("#modal-repair-order-mail").modal("hide");
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'ส่ง E-mail เรียบร้อยแล้ว',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                } else {
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            });
        }

        $(".btn-cancel-form").on("click", function() {
            let storeUri = "{{ route('admin.repair-orders.update-status') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            formData.append('status', status);
            mySwal.fire({
                title: 'ยืนยันยกเลิกใบสั่งซ่อม',
                html: 'กรุณากรอกเหตุผล ยกเลิกใบสั่งซ่อมในครั้งนี้ <span class="text-danger">*</span>',
                input: 'text',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-danger m-1',
                    cancelButton: 'btn btn-secondary m-1'
                },
                showCancelButton: true,
                confirmButtonText: "{{ __('lang.confirm') }}",
                cancelButtonText: "{{ __('lang.cancel') }}",
                preConfirm: e => {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve();
                        }, 50);
                    });
                }

            }).then((result) => {
                var reason = '';
                if (result.value) {
                    reason = result.value;
                }
                formData.append('reason', reason);
                saveForm(storeUri, formData);
            })
        });

        function appendFormData() {
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
                });
            }
            if (window.addReplacementVue) {
                let data = window.addReplacementVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.replacement_files && item.replacement_files.length > 0) {
                            item.replacement_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('replacement_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
            }
            return formData;
        }

        $(".btn-save-form-data").on("click", function() {
            let storeUri = "{{ route('admin.repair-orders.store') }}";
            var formData = appendFormData();
            saveForm(storeUri, formData);
        });
    </script>
@endpush
