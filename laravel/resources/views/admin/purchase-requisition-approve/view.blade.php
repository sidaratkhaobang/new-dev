@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('history')
    @include('admin.components.btns.history')
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">

    <style>
       
    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    {{-- <x-short-term-rental.step-service :rentalid="$rental_id" :success="true" :show="true" :istoggle="true" /> --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        @include('admin.purchase-requisitions.sections.header')
        <div class="block-content">
            <form id="save-form">
                @include('admin.purchase-requisitions.sections.purchase')
                @include('admin.purchase-requisitions.views.pr-car-accessory')
                {{-- @include('admin.purchase-requisitions.views.pr-accessory') --}}
                @include('admin.purchase-requisitions.views.pr-upload')
                {{-- @include('admin.purchase-requisitions.views.dealers')
                @include('admin.purchase-requisitions.views.car-order') --}}
                @include('admin.purchase-requisitions.views.transaction')
                {{-- @dd($approve_line_owner) --}}
                @if ($approve_line_owner)
                    <x-forms.hidden id="approve_line" :value="$approve_line_owner->id" />
                @endif
                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        {{-- all --}}
                        <a class="btn btn-secondary"
                            href="{{ route('admin.purchase-requisition-approve.index') }}">{{ __('lang.back') }}</a>

                        {{-- รออนุมัติ --}}
                        @if ($approve_line_owner)
                            @if ($d->status == PRStatusEnum::PENDING_REVIEW)
                                @can(Actions::Manage . '_' . Resources::PurchaseRequisitionApprove)
                                    <button type="button" class="btn btn-danger btn-not-approve-status"
                                        data-id="{{ $d->id }}"
                                        data-status="{{ PRStatusEnum::REJECT }}">{{ __('purchase_requisitions.reject') }}
                                    </button>
                                    <button type="button" class="btn btn-primary btn-approve-status"
                                        data-id="{{ $d->id }}"
                                        data-status="{{ PRStatusEnum::CONFIRM }}">{{ __('purchase_requisitions.approved') }}</button>
                                @endcan
                            @endif
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.approve-modal')
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.purchase-requisitions.scripts.readonly-field-script')
@include('admin.components.upload-image-scripts')

@push('scripts')
    <script>
        $('.input-number-car-amount').prop('disabled', true);
        $('#ordered_creditor_id').prop('disabled', true);
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-show-approve-modal").on("click", function() {
            document.getElementById("approve_status").value = $(this).attr('data-status');
            document.getElementById("approve_id").value = document.getElementById("id").value;
            document.getElementById("approve_line_id").value = document.getElementById("approve_line").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisition-approve.index') }}";
            $('#modal-approve').modal('show');
        });

        $(".btn-show-reject-modal").on("click", function() {
            document.getElementById("cancel_status").value = $(this).attr('data-status');
            document.getElementById("cancel_id").value = document.getElementById("id").value;
            document.getElementById("approve_line_id").value = document.getElementById("approve_line").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisition-approve.index') }}";
            $("#cancel-modal-label").html('ยืนยันไม่อนุมัติ คำขอสั่งซื้อ');
            $("#cancel-modal-body").html('กรุณาให้เหตุผลการไม่อนุมัติคำขอสั่งซื้อในครั้งนี้');
            $("#cancel-modal-text").html('เหตุผลที่ไม่อนุมัติ');
            $('#modal-cancel').modal('show');
        });
    </script>
@endpush
