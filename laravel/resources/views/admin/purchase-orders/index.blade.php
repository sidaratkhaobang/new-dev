@extends('admin.layouts.layout')
@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
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
                            <x-forms.input-new-line id="purchase_order_no" :value="$purchase_order_no" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('purchase_orders.purchase_order_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="purchase_requisition_no" :value="$purchase_requisition_no" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('purchase_orders.purchase_requisition_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$rental_type" id="rental_type" :list="$rental_type_list" :label="__('purchase_requisitions.rental_type')"
                                :optionals="[]" />
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_delivery_date">{{ __('purchase_orders.delivery_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_delivery_date" name="from_delivery_date" value="{{ $from_delivery_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_delivery_date" name="to_delivery_date" value="{{ $to_delivery_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
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
                            <th>@sortablelink('po_no', __('purchase_orders.purchase_order_no'))</th>
                            <th>@sortablelink('pr_no', __('purchase_orders.purchase_requisition_no'))</th>
                            <th>@sortablelink('leasing_requisition_no', __('purchase_orders.leasing_requisition_no'))</th>
                            <th>@sortablelink('rental_type', __('purchase_orders.car_type'))</th>
                            <th>@sortablelink('total_amount', __('purchase_orders.car_amount'))</th>
                            <th>@sortablelink('require_date', __('purchase_orders.delivery_date'))</th>
                            <th class="text-center">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->po_no }}</td>
                                    <td>{{ $d->pr_no }}</td>
                                    <td>
                                        @if ($d->st_worksheet_no)
                                            {{ $d->st_worksheet_no }}
                                        @elseif ($d->lt_worksheet_no)
                                            {{ $d->lt_worksheet_no }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $d->rental_type }}</td>
                                    <td>{{ $d->purchaseOrderLines?->sum('amount') }} / {{ $d->total_amount }}</td>
                                    <td>{{ $d->require_date ? get_thai_date_format($d->require_date, 'd/m/Y') : '-' }}</td>
                                    <td>
                                        {!! badge_render(__('purchase_orders.class_' . $d->status), __('purchase_orders.status_' . $d->status)) !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        @if ($d->status == \App\Enums\POStatusEnum::DRAFT)
                                            {{-- @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.purchase-orders.show', [
                                                    'purchase_order' => $d,
                                                ]),
                                                'other_route' => route('admin.purchase-orders.edit', [
                                                    'purchase_order' => $d,
                                                ]),
                                                'other_icon' => 'fa fa-file',
                                                'other_text' => __('purchase_orders.open_po'),
                                                'delete_route' => route('admin.purchase-orders.destroy', [
                                                    'purchase_order' => $d,
                                                ]),
                                            ]) --}}

                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    @can(Actions::View . '_' . Resources::PurchaseOrder)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.purchase-orders.show', ['purchase_order' => $d->id]) }}"><i
                                                                class="fa fa-eye me-1"></i>
                                                            {{ __('purchase_orders.view') }}
                                                        </a>
                                                    @endcan
                                                    @can(Actions::Manage . '_' . Resources::PurchaseOrder)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.purchase-orders.edit', ['purchase_order' => $d->id]) }}"><i
                                                                class="far fa-edit me-1"></i>
                                                            {{ __('purchase_orders.edit') }}
                                                        </a>
                                                        @include('admin.purchase-orders.scripts.update-cancel-status')
                                                        <a class="dropdown-item  btn-cancel-status-index" id="id"
                                                            data-id="{{ $d->id }}"><i class="fa fa-trash-alt me-1"></i>
                                                            {{ __('purchase_orders.cancel') }}
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @elseif (isset($view_only))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.purchase-order-approve.show', [
                                                    'purchase_order_approve' => $d,
                                                ]),
                                                'view_permission' =>
                                                    Actions::View . '_' . Resources::PurchaseOrderApprove,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::PurchaseOrderApprove,
                                            ])
                                        @elseif (in_array($d->status, [
                                                \App\Enums\POStatusEnum::CANCEL,
                                                \App\Enums\POStatusEnum::COMPLETE,
                                                \App\Enums\POStatusEnum::CONFIRM,
                                            ]))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.purchase-orders.show', [
                                                    'purchase_order' => $d,
                                                ]),
                                                'view_permission' =>
                                                    Actions::View . '_' . Resources::PurchaseOrderApprove,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::PurchaseOrderApprove,
                                            ])
                                        @else
                                            {{-- @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.purchase-orders.show', [
                                                    'purchase_order' => $d,
                                                ]),
                                                'edit_route' => route('admin.purchase-orders.edit', [
                                                    'purchase_order' => $d,
                                                ]),
                                            ]) --}}

                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    @can(Actions::View . '_' . Resources::PurchaseOrder)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.purchase-orders.show', ['purchase_order' => $d->id]) }}"><i
                                                                class="fa fa-eye me-1"></i>
                                                            {{ __('purchase_orders.view') }}
                                                        </a>
                                                    @endcan
                                                    @can(Actions::Manage . '_' . Resources::PurchaseOrder)
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.purchase-orders.edit', ['purchase_order' => $d->id]) }}"><i
                                                                class="far fa-edit me-1"></i>
                                                            {{ __('purchase_orders.edit') }}
                                                        </a>
                                                        @include('admin.purchase-orders.scripts.update-cancel-status')
                                                        <a class="dropdown-item  btn-cancel-status-index" id="id"
                                                            data-id="{{ $d->id }}"><i
                                                                class="fa fa-trash-alt me-1"></i>
                                                            {{ __('purchase_orders.cancel') }}
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endif
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
@include('admin.components.date-input-script')
@include('admin.purchase-orders.scripts.update-status')

@include('admin.components.select2-ajax', [
    'id' => 'car_brand_id',
    'url' => route('admin.util.select2.car-brand'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_type_id',
    'url' => route('admin.util.select2.car-type'),
    'parent_id' => 'car_brand_id',
])

@push('scripts')
    <script>
        $(".btn-cancel-status-index").on("click", function() {
            var data = {
                purchase_order_status: '{{ \App\Enums\POStatusEnum::CANCEL }}',
                purchase_order_id: $(this).attr('data-id'),
                redirect_route: '{{ route('admin.purchase-orders.index') }}',
            };
            console.log(data);
            mySwal.fire({
                title: "{{ __('purchase_orders.cancel_confirm') }}",
                html: 'กรุณากรอกเหตุผล ยกเลิกใบสั่งซื้อในครั้งนี้ <span class="text-danger">*</span>',
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
                    var reason = result.value;
                    data.reject_reason = reason;
                    updatePurchaseOrderStatus(data);
                } else {
                    if (typeof result.value !== 'undefined') {
                        warningAlert("{{ __('lang.required_field_inform') }}")
                    }
                }
            })
        });
    </script>
@endpush
