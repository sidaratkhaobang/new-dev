@extends('admin.layouts.layout')
@section('page_title', __('import_cars.manage') . __('import_cars.page_title'))

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
                            <x-forms.select-option id="rental_type" :value="$rental_type" :list="$rental_type_list" :label="__('import_cars.car_type')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="null" :list="null"
                                :label="__('import_cars.customer')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="dealer" :value="$dealer" :list="$dealer_list"
                                :label="__('import_cars.dealer')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('import_cars.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('car_tires.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr style="width: 100%;">
                            <th style="width: 1px;">#</th>
                            <th style="width: 10%;">@sortablelink('po_no', __('import_cars.purchase_order_no'))</th>
                            <th style="width: 10%;">@sortablelink('pr_no', __('import_cars.purchase_requisition_no'))</th>
                            <th style="width: 10%;">@sortablelink('rental_type', __('import_cars.car_type'))</th>
                            <th style="width: 8%;">@sortablelink('total_amount', __('import_cars.purchase_count'))</th>
                            <th style="width: 10%;">@sortablelink('delivery', __('import_cars.delivery'))</th>
                            <th style="width: 12%;">@sortablelink('total', __('import_cars.total_price'))</th>
                            <th style="width: 12%;">@sortablelink('customer', __('import_cars.customer'))</th>
                            <th style="width: 10%;">@sortablelink('creditor_name', __('import_cars.dealer'))</th>
                            <th class="text-center" style="width: 12%;">@sortablelink('status', __('import_cars.status'))</th>
                            <th style="width: 100px;" class="sticky-col text-center">{{ __('lang.tools') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($lists) > 0)
                            @foreach ($lists as $index => $d)
                                <tr>
                                    <td>{{ $lists->firstItem() + $index }}</td>
                                    <td>{{ $d->po_no }}</td>
                                    <td>{{ $d->pr_no }}</td>
                                    <td>{{ $d->rental_type }}</td>
                                    <td>{{ $d->total_amount }}</td>
                                    <td></td>
                                    <td>{{ number_format($d->total, 2) }} </td>
                                    <td></td>
                                    <td>{{ $d->creditor_name }}</td>
                                    <td class="text-center">
                                        {!! badge_render(__('import_cars.class_' . $d->status), __('import_cars.status_' . $d->status)) !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        @if (!($d->status == \App\Enums\ImportCarStatusEnum::CANCEL))
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.import-cars.show', [
                                                    'import_car' => $d,
                                                ]),
                                                'edit_route' => route('admin.import-cars.edit', [
                                                    'import_car' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::ImportCar,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::ImportCar,
                                            ])
                                        @else
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.import-cars.show', [
                                                    'import_car' => $d,
                                                ]),
                                                'view_permission' => Actions::View . '_' . Resources::ImportCar,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::ImportCar,
                                            ])
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')
