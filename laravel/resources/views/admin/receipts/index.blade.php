@extends('admin.layouts.layout')
@section('page_title', __('receipts.page_title'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
           'text' => __('lang.search'),
           'block_icon_class' => 'icon-search',
           'is_toggle' => true
       ])
{{--        <div class="block-header">--}}
{{--            <h3 class="block-title">{{ __('receipts.total_items') }}</h3>--}}
{{--        </div>--}}
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list"
                                :label="__('receipts.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="receipt_type" :value="$receipt_type" :list="$receipt_type_list"
                                :label="__('receipts.receipt_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_id" :value="$customer_id" :list="$customer_list" :label="__('receipts.customer_name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
      'text' => __('lang.total_list'),
  ])
        <div class="block-content">

            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 2px;">#</th>
                        <th>@sortablelink('worksheet_no', __('receipts.worksheet_no'))</th>
                        <th>@sortablelink('parent_id', __('receipts.reference_no'))</th>
                        <th>@sortablelink('receipt_type', __('receipts.receipt_type'))</th>
                        <th>@sortablelink('customer_name', __('receipts.customer_name'))</th>
                        <th>@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-end">{{ __('lang.tools') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->parent ? $d->parent->worksheet_no : null }}</td>
                                <td>{{ __('receipts.receipt_type_' . $d->receipt_type) }}</td>
                                <td>{{ $d->customer_name }}</td>
                                <td>{{ __('receipts.status_' . $d->status) }}</td>
                                <td class="sticky-col text-end">
                                    @can(Actions::Manage . '_' . Resources::Receipt)
                                        <div class="btn-group">
                                            <div class="col-sm-12">
                                                <div class="dropdown dropleft">
                                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                        @if (strcmp($d->status, ReceiptStatusEnum::ACTIVE) == 0)
                                                            @can(Actions::View . '_' . Resources::Receipt)
                                                                <a class="dropdown-item"
                                                                   href="{{ route('admin.receipts.show', ['receipt' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                            @can(Actions::Manage . '_' . Resources::Receipt)
                                                                <a class="dropdown-item"
                                                                   href="{{ route('admin.receipts.edit', ['receipt' => $d]) }}">
                                                                    <i class="far fa-edit me-1"></i> แก้ไข
                                                                </a>
                                                                <a class="dropdown-item" target="_blank"
                                                                   href="{{ route('admin.receipts.pdf', ['receipt' => $d]) }}">
                                                                    <i class="fa fa-upload"></i> {{ __('receipts.print_receipt') }}
                                                                </a>
                                                            @endcan
                                                        @else
                                                            @can(Actions::View . '_' . Resources::Receipt)
                                                                <a class="dropdown-item"
                                                                   href="{{ route('admin.receipts.show', ['receipt' => $d]) }}"><i
                                                                        class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center">" {{ __('lang.no_list') }} "</td>
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
