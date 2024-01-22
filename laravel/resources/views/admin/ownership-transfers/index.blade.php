@extends('admin.layouts.layout')
@section('page_title', __('ownership_transfers.page_title'))
@push('custom_styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <style>
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
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="leasing_search" :value="$leasing" :list="null" :label="__('ownership_transfers.leasing')"
                                :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $leasing_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="contract_no_search" :value="$contract_no" :list="null"
                                :label="__('ownership_transfers.contract_no')" :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $contract_no_text,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status_search" :value="$status" :list="$status_list"
                                :label="__('ownership_transfers.status')" />
                        </div>

                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="actual_last_payment_date_search" :value="$actual_last_payment_date"
                                :label="__('ownership_transfers.actual_last_payment_date')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>

    </div>

    <div class="block {{ __('block.styles') }}">
    @section('block_options_list2')
        <div class="block-options">
            <div class="block-options-item">
                <button type="button" class="btn btn-primary" onclick="openModalAvanceSelectCar()"><i
                        class="icon-menu-money"></i>
                    {{ __('ownership_transfers.withdraw_avance') }}</button>
            </div>
            <div class="block-options-item">
                <button type="button" class="btn btn-primary" onclick="openModalFaceSheetSelectCar()"><i
                        class="icon-printer"></i>
                    {{ __('registers.face_sheet') }}</button>
            </div>
            <div class="btn-group" style="position: sticky; min-width:270px;">
                <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                    style="width: 220px;"><i
                        class="icon-info-circle"></i>{{ __('ownership_transfers.request_document') }}</button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                    style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden"></span>
                </button>

                <ul class="dropdown-menu">
                    <li><button class="dropdown-item btn-request" onclick="selectCarPowerAttorneyPdf()">ขอหนังสือมอบอำนาจ
                            (TLS)</button>
                    </li>
                    <li>
                        <button class="dropdown-item btn-request"
                            onclick="selectCarTransferPdf()">ขอชุดโอน/เล่มทะเบียน/มอบอำนาจ</button>
                    </li>
                </ul>
            </div>

            <div class="btn-group" style="position: sticky;left: 100%; min-width:270px;">
                <button type="button" class="btn btn-primary dropdown-toggle-split" aria-expanded="false"
                    style="width: 220px;"><i class="icon-edit"></i>{{ __('registers.edit_multiple') }}</button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split arrow"
                    style="min-width: 5px !important;" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>

                <ul class="dropdown-menu">
                    <li><button class="dropdown-item btn-request" onclick="openModalTemplateSelectCar()">ดาวน์โหลด
                            Templete</button>
                    </li>
                    <li>
                        <label for="upload" class="dropdown-item btn-request file"
                            style="cursor: pointer;">อัปโหลดไฟล์</label>
                        <input id="upload" type="file" name="file[]"
                            style="position: absolute; top: -9999px; left: -9999px; overflow: hidden;">
                    </li>
                </ul>
            </div>
        </div>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list2',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>

                        <th style="width: 10%;">#</th>
                        <th style="width: 15%;">@sortablelink('worksheet_no', __('ownership_transfers.leasing'))</th>
                        <th style="width: 15%;">@sortablelink('hirePurchase.contract_no', __('ownership_transfers.contract_no'))</th>
                        <th style="width: 15%;">@sortablelink('car.license_plate', __('ownership_transfers.license_plate'))</th>
                        <th style="width: 15%;">@sortablelink('car.engine_no', __('ownership_transfers.engine_no'))</th>
                        <th style="width: 15%;">@sortablelink('actual_last_payment_date', __('ownership_transfers.actual_last_payment_date'))</th>
                        <th style="width: 8%;">@sortablelink('status', __('ownership_transfers.status'))</th>
                        <th></th>
                        {{-- <th style="width: 100px;" class="sticky-col"></th> --}}
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->insurance_lot && $d->hirePurchase->insurance_lot->creditor ? $d->hirePurchase->insurance_lot->creditor->name : '' }}
                                </td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->contract_no ? $d->hirePurchase->contract_no : '' }}
                                </td>
                                <td>{{ $d->car && $d->car->license_plate ? $d->car->license_plate : '' }}</td>
                                <td>{{ $d->car && $d->car->engine_no ? $d->car->engine_no : '' }}</td>
                                <td>{{ $d->hirePurchase && $d->hirePurchase->actual_last_payment_date ? get_thai_date_format($d->hirePurchase->actual_last_payment_date, 'd/m/Y') : '' }}
                                </td>

                                <td>
                                    {!! badge_render(
                                        __('ownership_transfers.status_' . $d->status . '_class'),
                                        __('ownership_transfers.status_' . $d->status . '_text'),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::OwnershipTransfer)
                                                @if (in_array($d->status, [OwnershipTransferStatusEnum::WAITING_TRANSFER]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.show', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                @elseif(in_array($d->status, [OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.show', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.edit', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif(in_array($d->status, [OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.show-waiting-transfer', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.edit-waiting-transfer', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif(in_array($d->status, [OwnershipTransferStatusEnum::TRANSFERING]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.show-transfering', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.edit-transfering', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @elseif(in_array($d->status, [OwnershipTransferStatusEnum::TRANSFERED]))
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.ownership-transfers.show-transfering', ['ownership_transfer' => $d->id]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('ownership_transfers.view') }}
                                                    </a>
                                                @endif
                                            @endcan

                                        </div>
                                    </div>
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
@include('admin.ownership-transfers.modals.attorney-select-car-modal')
@include('admin.ownership-transfers.modals.transfer-register-power-attorney-select-car-modal')
{{-- face sheet  --}}
@include('admin.ownership-transfers.modals.face-sheet-select-car-modal')
@include('admin.ownership-transfers.modals.face-sheet-modal')
@include('admin.ownership-transfers.modals.template-import-car-modal')

{{-- advance  --}}
@include('admin.ownership-transfers.modals.avance-select-car-modal')
<div id="avance-selected" v-cloak data-detail-uri="" data-title="">
    @include('admin.ownership-transfers.modals.avance-modal')
</div>
@include('admin.ownership-transfers.modals.template-select-car-modal')
@endsection

@include('admin.ownership-transfers.scripts.index-script')
