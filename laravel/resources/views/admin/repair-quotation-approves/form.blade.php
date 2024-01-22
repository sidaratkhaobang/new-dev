@extends('admin.layouts.layout')
@section('page_title', $page_title . '' . $d->worksheet_no)
@section('page_title_sub')
    @if (isset($d->status))
        {!! badge_render(__('repairs.repair_class_' . $d->status), __('repairs.repair_text_' . $d->status), null) !!}
    @endif
@endsection

@section('btn-nav')
    <nav class="flex-sm-00-auto ml-sm-3">
        <a target="_blank" href="{{ route('admin.repair-orders.print-pdf', ['repair_order' => $d]) }}"
            class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
            {{ __('repair_orders.btn_print') }}
        </a>
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

        .form-progress-bar .form-progress-bar-header {
            text-align: left;

        }

        .form-progress-bar .form-progress-bar-steps {
            margin: 30px 0 10px 0;
            /* display: flex;
                                                                                                                justify-content: center;
                                                                                                                align-items: center; */
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

@push('custom_styles')
    <style>
        .badge-custom {
            min-width: 20rem;
        }
    </style>
@endpush

@section('content')



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

    @include('admin.components.step-progress')

    @include('admin.repair-orders.sections.repair-info')

    @include('admin.repairs.sections.service-center')

    @include('admin.repairs.sections.replacement')

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

    <form id="save-form">
        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="status" :value="$d->status" />
                <x-forms.hidden id="car_id" :value="null" />
                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line" :value="$approve_line_owner->id" />
                @endif
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.repair-quotation-approves.index') }}">{{ __('lang.back') }}</a>
                        @if ($approve_line_owner)
                            @if (strcmp($d->status, RepairStatusEnum::WAIT_APPROVE_QUOTATION) === 0)
                                {{-- @can(Actions::Manage . '_' . Resources::ReplacementCarApprove) --}}
                                <button type="button" class="btn btn-danger btn-reject-status"
                                    data-status="REJECT">{{ __('long_term_rentals.reject') }}</button>
                                <button type="button" class="btn btn-primary btn-approve-status"
                                    data-status="CONFIRM">{{ __('long_term_rentals.approved') }}</button>
                                {{-- @endcan --}}
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.repair-quotation-approves.store'),
])
@include('admin.repair-orders.scripts.repair-order-line-script')
@include('admin.repair-orders.scripts.repair-line-script')
@include('admin.repair-orders.scripts.repair-script')

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
    'id' => 'expense_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'mock_files' => $expense_files ?? [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
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

        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }

        $(".btn-approve-status").on("click", function() {
            let storeUri = "{{ route('admin.repair-quotation-approves.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var approve_line_id = document.getElementById("approve_line").value;
            formData.append('approve_line_id', approve_line_id);
            formData.append('status_update', status);

            mySwal.fire({
                title: "อนุมัติใบเสนอราคาสั่งซ่อม",
                html: 'เมื่อยืนยันใบเสนอราคาแล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-primary m-1',
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
                if (result.value) {
                    saveForm(storeUri, formData);
                }
            })
        });

        $(".btn-reject-status").on("click", function() {
            let storeUri = "{{ route('admin.repair-quotation-approves.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            var approve_line_id = document.getElementById("approve_line").value;
            formData.append('approve_line_id', approve_line_id);
            formData.append('status_update', status);

            mySwal.fire({
                title: "ไม่อนุมัติใบเสนอราคาสั่งซ่อม",
                html: 'เหตุผลการไม่อนุมัติ ',
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
                var reject_reason = '';
                if (result.value) {
                    reject_reason = result.value;
                }
                formData.append('reject_reason', reject_reason);
                saveForm(storeUri, formData);
            })
        });
    </script>
@endpush
