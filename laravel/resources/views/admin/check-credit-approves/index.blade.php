@extends('admin.layouts.layout')
@section('page_title', __('check_credit.index.title.approve'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search')    ,
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            @include('admin.check-credit-new-customers.sections.search-form')
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.total_items'),
           'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>@sortablelink('worksheet_no', __('check_credit.index.table.worksheet_no'))</th>
                        <th>@sortablelink('customer_type', __('check_credit.index.table.customer_type'))</th>
                        <th>@sortablelink('name', __('check_credit.index.table.customer_name'))</th>
                        <th>@sortablelink('brancheTable.name', __('check_credit.index.table.branch_name'))</th>
                        <th>@sortablelink('status', __('check_credit.index.table.status'))</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach ($list as $item)
                            <tr>
                                <td>{{$item->worksheet_no}}</td>
                                <td>{{isset($item->customer_type) ? __('customers.type_' . $item->customer_type) : null}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->brancheTable?->name}}</td>
                                <td>
                                    {!! badge_render(__('check_credit.status_class_' . $item->status), __('check_credit.status_text_' . $item->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @include('admin.components.dropdown-action', [
                                        'view_route' => route('admin.check-credit-approves.show', ['check_credit_approve' => $item]),
                                        'edit_route' => $item->status != \App\Enums\CheckCreditStatusEnum::REJECT && !$item->is_create_customer ? route('admin.check-credit-approves.edit', ['check_credit_approve' => $item]) : null,
                                        'view_permission' => Actions::View . '_' . Resources::ContractCheckCreditApprove,
                                        'manage_permission' => Actions::Manage . '_' . Resources::ContractCheckCreditApprove,
                                    ])
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
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
