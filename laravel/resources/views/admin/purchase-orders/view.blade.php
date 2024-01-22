@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <style>
        .block-content-full {
            background-color: #FFF8E6;
        }

        .block-bordered-custom {
            border: 1px solid #EFB008 !important;
        }

        /* .form-progress-bar {
                                color: #888888;
                                padding: 30px;
                            } */

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
                @include('admin.purchase-orders.sections.pr-car-accessory')
                @include('admin.purchase-orders.sections.views.dealers')
                @include('admin.purchase-orders.sections.views.car-order')
                @include('admin.purchase-orders.sections.transaction')

                <x-forms.hidden id="id" name="id" :value="$d->id" />
                <x-forms.hidden id="purchase_requisition_id" name="purchase_requisition_id" :value="$d->purchase_requisition_id" />

                <div class="row push">
                    <div class="text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.purchase-orders.index') }}">{{ __('lang.back') }}</a>
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
@include('admin.purchase-orders.scripts.update-cancel-status')
@include('admin.purchase-orders.scripts.pr-car-script')
@include('admin.purchase-orders.scripts.pr-accessory-script')

@include('admin.components.select2-ajax', [
    'id' => 'creditor_id_field',
    'modal' => '#modal-purchase-order-dealer',
    'url' => route('admin.util.select2.dealers'),
])

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

        //modal
        $('#car_class_field').prop('disabled', true);
        $('#car_color_field').prop('disabled', true);
        $('#amount_car_field').prop('disabled', true);
        $('#remark_car_field').prop('disabled', true);

        $(".btn-purchase-order-update-status").on("click", function() {
            var data = {
                purchase_order_status: $(this).attr('id'),
                purchase_order_id: document.getElementById("id").value,
            };
            updatePurchaseOrderStatus(data);
        });
    </script>
@endpush
