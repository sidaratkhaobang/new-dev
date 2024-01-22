@extends('admin.layouts.layout')
@section('page_title', $page_title)
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
                            <x-forms.select-option id="customer_group" :value="$customer_group" :list="null" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('debt_collections.customer_group')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_code" :value="$customer_code" :list="$customer_code_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('debt_collections.customer_code')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="customer_name" :value="$customer_name" :list="$customer_name_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('debt_collections.customer_name')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="latest_due_date" :value="$latest_due_date" :label="__('debt_collections.latest_due_date')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_no" :value="$invoice_no" :list="$invoice_no_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('debt_collections.invoice_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
    @section('block_options_btn')
        <a class="btn btn-primary" href="#" onclick="openModal()">
            {{ __('debt_collections.download_excel_debt') }}
        </a>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_btn',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('debt_collections.invoice_no') }}</th>
                        <th>{{ __('debt_collections.customer_code') }}</th>
                        <th>{{ __('debt_collections.customer_name') }}</th>
                        <th>{{ __('debt_collections.customer_group') }}</th>
                        <th>{{ __('debt_collections.latest_due_date') }}</th>
                        <th>{{ __('debt_collections.overdue') }}</th>
                        <th>{{ __('lang.status') }}</th>
                        <th class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (!$list->isEmpty())
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d?->invoice_no ?? '-' }}</td>
                                <td>{{ $d?->customer_code ?? '-' }}</td>
                                <td>{{ $d?->customer_name ?? '-' }}</td>
                                <td>
                                    @foreach ($d->customer_group as $item_group)
                                        @php
                                            $last_item = $loop->last ? '' : ', ';
                                        @endphp
                                        {{ $item_group->name }}{{ $last_item }}
                                    @endforeach
                                </td>
                                <td>{{ $d?->due_date ?? '-' }}</td>
                                <td>{{ $d?->sub_total ?? '-' }}</td>
                                <td>
                                    @if ($d->status_debt_collection)
                                        {!! badge_render(
                                            __('debt_collections.status_' . $d->status_debt_collection . '_class'),
                                            __('debt_collections.status_' . $d->status_debt_collection),
                                            null,
                                        ) !!}
                                    @endif
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.debt-collections.show', [
                                            'debt_collection' => $d,
                                        ]),
                                        'edit_route' => route('admin.debt-collections.edit', [
                                            'debt_collection' => $d,
                                        ]),
                                        'view_permission' => Actions::View . '_' . Resources::DebtCollection,
                                        'manage_permission' => Actions::Manage . '_' . Resources::DebtCollection,
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
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
@include('admin.components.date-input-script')

@push('scripts')
<script>
    function openModal() {
        warningAlert("ยังไม่พร้อมให้บริการ");
    }
</script>
@endpush
