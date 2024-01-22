@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.select-option id="type" :value="$type" :list="$type_lists" :label="__('request_receipts.type')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="receipt_no_ref" :value="null" :list="[]" :label="__('request_receipts.receipt_no_ref')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status" :value="$status" :list="$status_lists" :label="__('request_receipts.status')" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>
    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::RequestReceipt)
                <x-btns.add-new btn-text="{{ __('request_receipts.add_new') }}"
                    route-create="{{ route('admin.request-receipts.create') }}" />
            @endcan
        </x-slot>
        <x-tables.table :list="$lists">
            <x-slot name="thead">
                <th style="width: 30%;">@sortablelink('worksheet_no', __('request_receipts.worksheet_no'))</th>
                <th style="width: 15%;">@sortablelink('type', __('request_receipts.type'))</th>
                <th style="width: 30%;">@sortablelink('receipt_no_ref', __('request_receipts.receipt_no_ref'))</th>
                <th style="width: 15%;">@sortablelink('created_at', __('request_receipts.inform_date'))</th>
                <th>@sortablelink('status', __('request_receipts.status'))</th>
            </x-slot>
            @foreach ($lists as $index => $d)
                <tr>
                    <td>{{ $lists->firstItem() + $index }}</td>
                    <td style="width: 30%;">{{ $d->worksheet_no }}</td>
                    <td style="width: 15%;">{{ __('request_receipts.type_' . $d->type) }}</td>
                    <td style="width: 30%;"></td>
                    <td style="width: 15%;">{{ get_date_time_by_format($d->created_at, 'd/m/Y') }}</td>
                    <td> {!! badge_render(__('request_receipts.class_' . $d->status), __('request_receipts.text_' . $d->status), null) !!}</td>
                    @if (in_array($d->status, [RequestReceiptStatusEnum::DRAFT]))
                        <td class="sticky-col text-center">
                            <x-tables.dropdown :id="'dropdown-table'" :routes="[
                                'view_route' => route('admin.request-receipts.show', [
                                    'request_receipt' => $d,
                                ]),
                                'edit_route' => route('admin.request-receipts.edit', [
                                    'request_receipt' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::RequestReceipt,
                                'manage_permission' => Actions::Manage . '_' . Resources::RequestReceipt,
                            ]">
                            </x-tables.dropdown>
                        </td>
                    @else
                        <td class="sticky-col text-center">
                            <x-tables.dropdown :id="'dropdown-table'" :routes="[
                                'view_route' => route('admin.request-receipts.show', [
                                    'request_receipt' => $d,
                                ]),
                                'view_permission' => Actions::View . '_' . Resources::RequestReceipt,
                                'manage_permission' => Actions::Manage . '_' . Resources::RequestReceipt,
                            ]">
                            </x-tables.dropdown>
                        </td>
                    @endif
                </tr>
            @endforeach
        </x-tables.table>
    </x-blocks.block-table>

@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'lot_no_search',
    'url' => route('admin.util.select2-register.lot-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car_class_search',
    'url' => route('admin.util.select2-register.car-class-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'license_plate_search',
    'url' => route('admin.util.select2-register.license-plate-list'),
])

@include('admin.components.select2-ajax', [
    'id' => 'facesheet_status',
    'url' => route('admin.util.select2-register.get-status-facesheet'),
    'modal' => '#face-sheet-modal',
])

@push('scripts')
@endpush
