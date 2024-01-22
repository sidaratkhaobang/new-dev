@extends('admin.layouts.layout')

@section('page_title', $page_title)
@section('page_title_no', $d->worksheet_no)
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('page_title_sub')
    {!! badge_render(__('install_equipment_pos.class_' . $d->status), __('install_equipment_pos.status_' . $d->status)) !!}
@endsection
@push('styles')
    <style>
        .block-content-full {
            background-color: #FFF8E6;
        }

        .block-bordered-custom {
            border: 1px solid #EFB008 !important;
        }
    </style>
@endpush
@section('btn-nav')
    <a target="_blank" href="{{ route('admin.install-equipment-purchase-orders.pdf', ['install_equipment_po_id' => $d->id]) }}" class="btn btn-purple">
       <i class="icon-printer me-1"></i> {{ __('install_equipment_pos.print') }}
    </a>
@endsection
@section('content')
    @include('admin.components.step-progress')
    <form id="save-form">
        @include('admin.install-equipment-purchase-orders.modals.history')
        @include('admin.install-equipment-purchase-orders.sections.info')
        @include('admin.install-equipment-purchase-orders.sections.accessory')
        <x-forms.hidden id="id" :value="$d->id" />
        @if ($approve_line_owner)
            <x-forms.hidden id="approve_line_id" :value="$approve_line_owner->id" />
        @endif
        @include('admin.install-equipment-purchase-order-approves.sections.submit')
    </form>
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')

@include('admin.install-equipment-purchase-orders.scripts.accessory-script')
@include('admin.components.form-save', [
    'store_uri' => $store_uri,
])

@push('scripts')
    <script>
        const view_only =
            @if (isset($view_only))
                @json($view_only)
            @else
                false
            @endif ;
        if (view_only) {
            $('#time_of_delivery').prop('disabled', true);
            $('#payment_term').prop('disabled', true);
            $('#contact').prop('disabled', true);
            $('#car_user').prop('disabled', true);
            $('#quotation').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $('#quotation_remark').prop('disabled', true);
        }

        $(".btn-approve-status").on("click", function() {
            let storeUri = "{{ $store_uri }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');
            formData.append('status_update', status);

            mySwal.fire({
                title: "{{ __('install_equipment_pos.approve_confirm') }}",
                html: 'เมื่อยืนยันใบสั่งซื้ออุปกรณ์แล้ว ระบบจะส่งข้อมูลคำขอนี้เพื่อดำเนินการในส่วนต่อไป',
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

        $(".btn-disapprove-status").on("click", function() {
            let storeUri = "{{ $store_uri }}";
            var formData = new FormData(document.querySelector('#save-form'));
            var status = $(this).attr('data-status');

            formData.append('status_update', status);

            mySwal.fire({
                title: "{{ __('install_equipment_pos.disapprove_confirm') }}",
                html: 'กรุณาให้เหตุผลการไม่อนุมัติใบสั่งซื้ออุปกรณ์นี้ ',
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
                    var reject_reason = result.value;
                    formData.append('reject_reason', reject_reason);
                    saveForm(storeUri, formData);
                }
            })
        });

        function viewHistory()
        {
            $('#approve-history-modal').modal('show');
        }
    </script>
@endpush
