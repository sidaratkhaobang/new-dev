@extends('admin.layouts.layout')
@section('page_title', $page_title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                @include('admin.purchase-requisitions.sections.purchase')
                @include('admin.purchase-requisitions.views.pr-car-accessory')
                @include('admin.purchase-requisitions.views.pr-upload')
                @include('admin.purchase-order-open.sections.po-detail')
                <x-forms.hidden id="id" :value="$d->id" />
                <div class="row push">
                    <div class="col-sm-12 text-end">
                        <a class="btn btn-secondary" href="{{ route('admin.purchase-order-open.index') }}">{{ __('lang.back') }}</a>
                        @can(Actions::Manage . '_' . Resources::OpenPurchaseOrder)
                        @if (strcmp($d->status, PRStatusEnum::CONFIRM) == 0)
                            <a class="btn btn-danger btn-update-pr-status" id="{{ $d->id }}" data-status="{{ PRStatusEnum::COMPLETE }}">{{ __('purchase_requisitions.status_complete_text') }}</a>
                            <a class="btn btn-primary" href="{{ route('admin.purchase-order-open.create', ['purchase_requisition_id' => $d->id]) }}">{{ __('purchase_orders.page_title') }}</a>
                        @endif
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.purchase-requisition-approve.scripts.update-status')
@include('admin.purchase-requisitions.scripts.readonly-field-script')
@include('admin.components.upload-image-scripts')


@push('scripts')
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers(['js-flatpickr', 'js-datepicker']);
        });
        $('.toggle-table').click(function() {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });
    </script>
@endpush
