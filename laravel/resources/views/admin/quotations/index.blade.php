@extends('admin.layouts.layout')
@section('page_title', __('quotations.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        {{--        <div class="block-header"> --}}
        {{--            <h3 class="block-title">{{ __('quotations.total_items') }}</h3> --}}
        {{--        </div> --}}
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        {{-- <div class="col-sm-3">
                            <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                            <input type="text" id="s" name="s" class="form-control"
                                placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                        </div> --}}
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_id" :list="$worksheet_list" :label="__('long_term_rentals.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_id" :value="$rental_id" :list="$rental_list" :label="__('quotations.type_rental')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="$customer_id" :list="$customer_list" :label="__('quotations.customer')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="qt_id" :value="$qt_id" :list="$qt_worksheet_list" :label="__('quotations.quotation_no')" />
                        </div>
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="customer" :value="$customer_id" :list="$customer_list" :label="__('long_term_rentals.customer')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="won_auction" :value="$won_auction_status_id" :list="$won_auction_list" :label="__('long_term_rentals.won_auction')" />
                        </div> --}}
                    </div>
                    <div class="form-group row push mb-4">
                        {{-- <div class="col-sm-3">
                            <x-forms.select-option id="spec_status" :value="$spec_status_id" :list="$spec_status_list"
                                :label="__('long_term_rentals.spec_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="comparison_price_status" :value="$comparison_price_status_id" :list="$comparison_price_status_list"
                                :label="__('long_term_rentals.comparison_price_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental_price_status" :value="$rental_price_status_id" :list="$rental_price_status_list"
                                :label="__('long_term_rentals.rental_price_status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="quotation_no" :value="$quotation_id" :list="$quotation_list"
                                :label="__('long_term_rentals.quotation_no')" />
                        </div> --}}
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th>@sortablelink('worksheet_no', __('quotations.worksheet_no'))</th>
                            <th>@sortablelink('branch', __('quotations.branch'))</th>
                            <th>@sortablelink('customer', __('quotations.customer'))</th>
                            <th>@sortablelink('type_job', __('quotations.type_job'))</th>
                            <th>@sortablelink('type_rental', __('quotations.type_rental'))</th>
                            <th>@sortablelink('created_at', __('quotations.created_at'))</th>
                            <th>@sortablelink('qt_no', __('quotations.quotation'))</th>
                            <th class="text-center">{{ __('quotations.bill_payment') }}</th>
                            <th class="text-center">{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($list->count()))
                            @foreach ($list as $index => $d)
                                {{-- @if (!empty($d->reference->worksheet_no)) --}}
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>
                                        @if ($d->reference_type === $long_term_model)
                                            {{-- @can(Actions::View . '_' . Resources::LongTermRental) --}}
                                            <a target="_blank"
                                                href="{{ route('admin.long-term-rentals.show', ['long_term_rental' => $d->reference_id]) }}">
                                                {{ $d->reference ? $d->reference->worksheet_no : null }}
                                            </a>
                                            {{-- @endcan   --}}
                                        @elseif ($d->reference_type === $short_term_model)
                                            {{-- @can(Actions::View . '_' . Resources::ShortTermRental) --}}
                                            <a target="_blank"
                                                href="{{ route('admin.short-term-rentals.show', ['short_term_rental' => $d->reference_id]) }}">
                                                {{ $d->reference ? $d->reference->worksheet_no : null }}
                                            </a>
                                            {{-- @endcan --}}
                                        @else
                                            <a target="_blank" href="#">
                                                {{ $d->reference ? $d->reference->worksheet_no : null }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $d->reference && $d->reference->branch ? $d->reference->branch->name : '-' }}
                                    </td>
                                    <td>{{ $d->customer_name }}</td>
                                    <td>{{ $d->reference && $d->reference->serviceType ? $d->reference->serviceType->name : '-' }}
                                    </td>
                                    <td>{{ __('quotations.type_rental_' . $d->reference_type) }}</td>
                                    <td>{{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : null }}</td>
                                    <td>
                                        <a target="_blank"
                                            @if ($d->reference_type === $long_term_model) href="{{ route('admin.quotations.long-term-rental-pdf', ['quotation' => $d]) }}"
                                           @elseif ($d->reference_type === $short_term_model)
                                               href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $d->rental_bill_id]) }}"
                                           @else
                                               href="#" @endif>
                                            {{ $d->qt_no }}@if (isset($d->edit_count))
                                                Rev.{{ strval(sprintf('%02d', $d->edit_count)) }}
                                            @endif
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if ($d->reference_type === $short_term_model)
                                        <a target="_blank" href="{{ route('admin.quotations.short-term-rental-payment-pdf', ['rental_bill_id' => $d->rental_bill_id]) }}" >
                                            <i class="icon-document-download text-primary"></i>
                                        </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($d->status != QuotationStatusEnum::DRAFT)
                                            {!! badge_render(
                                                __('quotations.quotation_class_' . $d->status),
                                                __('quotations.quotation_status_' . $d->status),
                                            ) !!}
                                        @else
                                            -
                                        @endif

                                    </td>
                                    @if (!empty($d->reference))
                                        <td class="sticky-col">
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
                                                            @can(Actions::View . '_' . Resources::Quotation)
                                                                {{-- @if ($d->reference_type === $long_term_model) --}}
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.quotations.show', ['quotation' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i>ดูข้อมูล</a>
                                                                {{-- @endif --}}
                                                            @endcan
                                                            {{-- @if (strcmp($d->status, QuotationStatusEnum::PENDING_REVIEW) == 0) --}}
                                                            @can(Actions::Manage . '_' . Resources::Quotation)
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.quotations.edit', ['quotation' => $d]) }}"><i
                                                                        class="far fa-edit me-1"></i>แก้ไข</a>
                                                                {{-- @if (strcmp($d->status, QuotationStatusEnum::PENDING_REVIEW) == 0)
                                                        <a class="dropdown-item btn-approve-status"
                                                            data-id="{{ $d->id }}"
                                                            data-status="{{ QuotationStatusEnum::CONFIRM }}"><i
                                                                class="fa fa-check"></i> {{ __('lang.approve') }}</a>

                                                        <a class="dropdown-item btn-disapprove-status"
                                                            data-id="{{ $d->id }}"
                                                            data-status="{{ QuotationStatusEnum::REJECT }}"><i
                                                                class="fa fa-times"></i> {{ __('lang.disapprove') }}
                                                        </a> --}}
                                                            @endcan
                                                            {{-- @endif --}}
                                                            {{-- @endif --}}

                                                            @if ($d->reference_type === $long_term_model)
                                                                @can(Actions::View . '_' . Resources::LongTermRental)
                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('admin.quotations.long-term-rental-pdf', ['quotation' => $d]) }}">
                                                                        <i class="fa fa-upload"></i> พิมพ์ใบเสนอราคา
                                                                    </a>
                                                                @endcan
                                                            @elseif ($d->reference_type === $short_term_model)
                                                                @can(Actions::View . '_' . Resources::ShortTermRental)
                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('admin.quotations.short-term-rental-pdf', ['rental_bill_id' => $d->rental_bill_id]) }}">
                                                                        <i class="fa fa-upload"></i> พิมพ์ใบเสนอราคา
                                                                    </a>
                                                                @endcan
                                                            @else
                                                                <a class="dropdown-item" target="_blank" href="#">
                                                                    <i class="fa fa-upload"></i> พิมพ์ใบเสนอราคา
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @else
                                        <td></td>
                                    @endif
                                </tr>
                                {{-- @endif --}}
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>

@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.quotations.scripts.update-status')
@include('admin.components.select2-default')
