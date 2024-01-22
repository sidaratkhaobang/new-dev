@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>

    </style>
@endpush
@section('content')
{{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        @include('admin.purchase-orders.sections.header')
        <div class="block-content">
            <form id="save-form">
                @include('admin.purchase-orders.sections.purchaser')
                @include('admin.purchase-orders.sections.views.pr-upload-view-only')
                @include('admin.purchase-orders.sections.views.dealers')
                @include('admin.purchase-orders.sections.views.car-order')
                @include('admin.purchase-orders.sections.transaction')

                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line" :value="$approve_line_owner->id" />
                @endif
                <x-forms.hidden id="id" name="id" :value="$d->id" />
                <x-forms.hidden id="purchase_requisition_id" name="purchase_requisition_id" :value="$d->purchase_requisition_id" />

                <div class="row push">
                    <div class="text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.purchase-order-approve.index') }}">{{ __('lang.back') }}</a>
                        @if ($approve_line_owner)
                            @if ($d->status == \App\Enums\POStatusEnum::PENDING_REVIEW)
                                @can(Actions::Manage . '_' . Resources::PurchaseOrderApprove)
                                    <a class="btn btn-danger btn-disapprove-status">{{ __('lang.disapprove') }}</a>
                                    <a class="btn btn-primary btn-purchase-order-update-status"
                                        id="{{ \App\Enums\POStatusEnum::CONFIRM }}">{{ __('lang.approve') }}</a>
                                @endcan
                            @endif
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.components.transaction-modal')
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.purchase-orders.scripts.update-status')

@push('scripts')
    <script>
        // set disable field
        $('#purchase_order_no').prop('disabled', true);
        $('#po_request_date').prop('disabled', true);
        $('#require_date').prop('disabled', true);
        $('#remark').prop('disabled', true);
        $('#cancel_reason').prop('disabled', true);
        $('#requester_name').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#purchase_requisition_no').prop('disabled', true);
        $('#request_date').prop('disabled', true);
        $('#purchase_requisition_date').prop('disabled', true);
        $('#delivery_date').prop('disabled', true);
        $('#rental_type').prop('disabled', true);
        $('#purchase_requisition_remark').prop('disabled', true);
        $('.input-number-car-amount').prop('disabled', true);
        $('#ordered_creditor_id').prop('disabled', true);
        $('#reviewer_name').prop('disabled', true);
        $('#reviewer_department').prop('disabled', true);
        $('#review_at').prop('disabled', true);
        $('#reason').prop('disabled', true);
        $('#time_of_delivery').prop('disabled', true);
        $('#payment_condition').prop('disabled', true);

        // $(".btn-purchase-order-update-status").on("click", function() {
        //     var data = {
        //         purchase_order_status: $(this).attr('id'),
        //         purchase_order_id: document.getElementById("id").value,
        //         redirect_route: '{{ route('admin.purchase-order-approve.index') }}',
        //         approve_line_id: document.getElementById("approve_line").value,
        //     };
        //     updatePurchaseOrderStatus(data);
        // });

        $(".btn-purchase-order-update-status").on("click", function () {
            var data = {
                purchase_order_status: $(this).attr('id'),
                purchase_order_id: document.getElementById("id").value,
                redirect_route: '{{ route('admin.purchase-order-approve.index') }}',
                approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: 'ยืนยันอนุมัติ ใบสั่งซื้อ',
                html: 'เมื่อยืนยันใบสั่งซื้อแล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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
                    updatePurchaseOrderStatus(data);
                }
            })
        });


        $(".btn-disapprove-status").on("click", function() {
            var data = {
                purchase_order_status: '{{ \App\Enums\POStatusEnum::REJECT }}',
                purchase_order_id: document.getElementById("id").value,
                redirect_route: '{{ route('admin.purchase-order-approve.index') }}',
                approve_line_id: document.getElementById("approve_line").value,
            };
            mySwal.fire({
                title: "{{ __('purchase_orders.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัติใบสั่งซื้อรถใหม่ในครั้งนี้ <span class="text-danger">*</span>',
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
                if (result.value) {
                    var reject_reason = $('.swal2-input').val();
                    data.reject_reason = reject_reason;
                    updatePurchaseOrderStatus(data);
                } else {
                    warningAlert("{{ __('lang.required_field_inform') }}")
                }
            })
        });
    </script>
@endpush
