@extends('admin.layouts.layout')
@section('page_title', __('purchase_orders.open_po'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="pr_no" :value="$pr_no" :list="$pr_list" :label="__('purchase_requisitions.pr_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_type" :value="$rental_type" :list="$rental_type_list" :label="__('purchase_requisitions.rental_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('purchase_requisitions.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('purchase_orders.all_item'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th>#</th>
                            <th>@sortablelink('pr_no', __('purchase_requisitions.pr_no_car'))</th>
                            <th>@sortablelink('rental_no', __('purchase_requisitions.rental_no'))</th>
                            <th>@sortablelink('rental_type', __('purchase_requisitions.rental_type'))</th>
                            <th>@sortablelink('po_count', __('purchase_requisitions.po_count'))</th>
                            <th>@sortablelink('total_amount', __('purchase_requisitions.car_amount_2'))</th>
                            <th>@sortablelink('require_date', __('purchase_requisitions.require_date'))</th>
                            <th class="text-center">@sortablelink('status', __('purchase_requisitions.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->pr_no }}</td>
                                    <td>-</td>
                                    <td>{{ __('purchase_requisitions.rental_type_' . $d->rental_type) }}</td>
                                    <td>{{ intval($d->po_count) }}</td>
                                    <td>{{ intval($d->po_amount) . '/' . intval($d->total_amount) }}</td>
                                    <td>{{ $d->require_date ? get_thai_date_format($d->require_date, 'd/m/Y') : '-' }}</td>
                                    <td class="text-center">
                                        @if (strcmp($d->status, \APP\Enums\PRStatusEnum::CONFIRM) == 0)
                                            {!! badge_render(
                                                __('purchase_requisitions.status_pending_class'),
                                                __('purchase_requisitions.status_pending_text'),
                                            ) !!}
                                        @else
                                            {!! badge_render(
                                                __('purchase_requisitions.status_complete_class'),
                                                __('purchase_requisitions.status_complete_text'),
                                            ) !!}
                                        @endif
                                    </td>
                                    <td class="sticky-col text-center">
                                        <div class="btn-group">
                                            <div class="col-sm-12">
                                                <div class="dropdown dropleft">
                                                    <button type="button"
                                                        class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                        @can(Actions::View . '_' . Resources::OpenPurchaseOrder)
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.purchase-order-open.show', ['purchase_order_open' => $d]) }}"><i
                                                                    class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                        @endcan
                                                        @if (strcmp($d->status, PRStatusEnum::CONFIRM) == 0)
                                                            @can(Actions::Manage . '_' . Resources::OpenPurchaseOrder)
                                                                @if (intval($d->po_amount) < intval($d->total_amount))
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('admin.purchase-order-open.create', ['purchase_requisition_id' => $d->id]) }}"><i
                                                                            class="far fa-edit me-1"></i>
                                                                        {{ __('purchase_orders.page_title') }}
                                                                    </a>
                                                                @endif
                                                                <a class="dropdown-item btn-update-pr-status"
                                                                    id="{{ $d->id }}"
                                                                    data-status="{{ PRStatusEnum::COMPLETE }}"><i
                                                                        class="far fa-circle-check me-1"></i> ปิดงาน
                                                                </a>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>

@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.purchase-requisition-approve.scripts.update-status')

@push('scripts')
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers(['js-flatpickr', 'js-datepicker']);
        });
    </script>
@endpush
