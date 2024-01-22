@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    {{--    Search Section --}}
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
                            <x-forms.select-option id="lot_no" :value="$lot_no" :list="$lot_no_list"
                                                   :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $lot_name,
                                                   ]"
                                                   :label="__('finance_request.search_lot_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental" :value="$rental" :list="$rental_list"
                                                   :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $rental_name,
                                                   ]"
                                                   :label="__('finance_request.search_rental')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="date_create" :value="$date_create"
                                                :label="__('finance_request.search_date_create')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_request.search_status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    {{--    Table Section --}}
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
                        <th>
                            #
                        </th>
                        <th>
                            {{ __('finance_request.search_lot_no') }}
                        </th>
                        <th>
                            {{__('finance_request.search_rental')}}
                        </th>
                        <th>
                            {{__('finance_request.car_total')}}
                        </th>
                        <th>
                            {{__('finance_request.search_status')}}
                        </th>
                        <th>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$list->isEmpty())
                        @foreach($list as $key => $d)
                            <tr>
                                <td>
                                    {{$list->currentPage() * $list->perPage() - $list->perPage() + 1 +$key}}
                                </td>
                                <td>
                                    {{$d?->insurance_lot?->lot_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->insurance_lot?->creditor?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->car_total ?? '-'}}
                                </td>
                                <td>
                                    {!! badge_render(
                                                                                __('finance_request.status_' . $d->status . '_class'),
                                                                                __('finance_request.status_' . $d->status),
                                                                                null,
                                                                            ) !!}
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                                                                                            'view_route' => route('admin.finance-request-approve.show', ['finance_request_approve' => $d->lot_id]),
//                                                                                                            'edit_route' => route('admin.finance-request-approve.edit', ['finance_request_approve' => $d->lot_id]),
                                                                                                            'view_permission' => Actions::View . '_' . Resources::FinanceRequestApprove,
                                                                                                            'manage_permission' => Actions::Manage . '_' . Resources::FinanceRequestApprove,
                                                                                                        ])
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
            {{--            {!! $list->appends(\Request::except('page'))->render() !!}--}}
        </div>
    </div>
    {{--    @include('admin.finance-request.modals.modal-export-excel')--}}
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'rental',
    'url' => route('admin.util.select2-finance.creditor-leasing-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'lot_no',
    'url' => route('admin.util.select2-finance.get-lot'),
])


