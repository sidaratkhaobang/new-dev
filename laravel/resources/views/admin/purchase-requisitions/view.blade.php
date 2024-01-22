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

    </style>
@endpush

@section('content')
    {{-- @if (isset($approve_line_list) && $approve_line_list)
        @include('admin.components.step-progress')
    @endif --}}
    <x-approve.step-approve :configenum="null" :id="$d->id" :model="get_class($d)" />
    <div class="block {{ __('block.styles') }}">
        @include('admin.purchase-requisitions.sections.header')
        <div class="block-content">
            <form id="save-form">

                @include('admin.purchase-requisitions.sections.purchase')
                @include('admin.purchase-requisitions.views.pr-car-accessory')
                {{-- @include('admin.purchase-requisitions.views.pr-accessory') --}}
                @include('admin.purchase-requisitions.views.pr-upload')
                {{-- @if ($d->status != PRStatusEnum::DRAFT)
                    @include('admin.purchase-requisitions.views.dealers')
                    @include('admin.purchase-requisitions.views.car-order')
                @endif --}}
                @include('admin.purchase-requisitions.views.transaction')

                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary"
                            href="{{ route('admin.purchase-requisitions.index') }}">{{ __('lang.back') }}</a>
                        @if ($d->status == PRStatusEnum::DRAFT)
                            <a class="btn btn-danger btn-delete-row"
                                data-route-delete="{{ route('admin.purchase-requisitions.destroy', ['purchase_requisition' => $d]) }}">{{ __('lang.delete') }}</a>
                            <a class="btn btn-primary"
                                href="{{ route('admin.purchase-requisitions.edit', ['purchase_requisition' => $d]) }}">{{ __('lang.edit_draft') }}</a>
                            {{-- <button type="button" class="btn btn-info btn-view-review"
                                data-status="{{ PRStatusEnum::PENDING_REVIEW }}">{{ __('lang.save_send') }}</button> --}}
                        @endif
                        {{-- @if (in_array($d->status, [PRStatusEnum::CONFIRM, PRStatusEnum::PENDING_REVIEW, PRStatusEnum::REJECT]))
                            <button type="button" class="btn btn-danger btn-show-cancel-modal"
                                data-status="{{ PRStatusEnum::CANCEL }}">{{ __('purchase_requisitions.cancel') }}</button>
                            <a class="btn btn-primary"
                                href="{{ route('admin.purchase-requisitions.edit', ['purchase_requisition' => $d]) }}">{{ __('lang.edit') }}</a>
                        @endif --}}
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('admin.purchase-requisition-approve.modals.cancel-modal')
    @include('admin.components.transaction-modal')
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.form-save', [
    'store_uri' => route('admin.purchase-requisitions.store'),
])

@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.purchase-requisitions.scripts.readonly-field-script')
@include('admin.components.upload-image-scripts')
@include('admin.components.date-input-script')

@push('scripts')
    <script>
        $('.input-number-car-amount').prop('disabled', true);
        $('#ordered_creditor_id').prop('disabled', true);

        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(".btn-show-cancel-modal").on("click", function() {
            document.getElementById("cancel_status").value = $(this).attr('data-status');
            document.getElementById("cancel_id").value = document.getElementById("id").value;
            document.getElementById("redirect").value = "{{ route('admin.purchase-requisitions.index') }}";
            $('#modal-cancel').modal('show');
        });

        $(".btn-view-review").on("click", function() {
            var id = document.getElementById("id").value;
            var status = $(this).attr('data-status');
            console.log(id, status);
            var data = {
                status: status,
                purchase_requisitions: [id],
            };
            var updateUri = "{{ route('admin.purchase-requisition.update-status') }}";
            axios.post(updateUri, data).then(response => {
                if (response.data.success) {
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: "{{ __('lang.store_success_message') }}",
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
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
            }).catch(error => {
                mySwal.fire({
                    title: "{{ __('lang.store_error_title') }}",
                    text: error.response.data.message,
                    icon: 'error',
                    confirmButtonText: "{{ __('lang.ok') }}",
                }).then(value => {
                    if (value) {
                        //
                    }
                });
            });
        });
    </script>
@endpush
