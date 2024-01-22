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
                            <x-forms.select-option id="contract" :value="$contract" :list="[]"
                                                   :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $contract_name,
                                                   ]"
                                                   :label="__('finance_contract.search_contract')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="rental" :value="$rental" :list="[]"
                                                   :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $rental_name,
                                                   ]"
                                                   :label="__('finance_request.search_rental')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car" :value="$car" :list="[]"
                                                   :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $car_name,
                                                   ]"
                                                   :label="__('finance_contract.search_car')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_contract.search_status')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="contract_start" :value="$contract_start"
                                                :label="__('finance.search_contract_start')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="first_payment" :value="$first_payment"
                                                :label="__('finance.search_first_payment')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="contract_end" :value="$contract_end"
                                                :label="__('finance.search_contract_start')"/>
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
                            {{__('finance_contract.search_contract')}}
                        </th>
                        <th>
                            {{__('finance_request.search_rental')}}
                        </th>
                        <th>
                            {{ __('finance_contract.car_license_plate') }}
                        </th>
                        <th>
                            {{__('finance.contract_start')}}
                        </th>
                        <th>
                            {{__('finance.pay_installments')}}
                        </th>
                        <th>
                            {{__('finance.contract_end')}}
                        </th>
                        <th>
                            {{__('finance_contract.search_status')}}
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
                                    {{$d?->contract_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->insurance_lot?->creditor?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->car?->license_plate ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->contract_start_date ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->pay_installments ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->contract_end_date ?? '-'}}
                                </td>
                                <td>
                                    {!! badge_render(
                                                                        __('finance_contract.status_' . $d?->status . '_class'),
                                                                        __('finance_contract.status_' . $d?->status),
                                                                        null,
                                                                    ) !!}
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                                                            'view_route' => route('admin.finance.show', ['finance' => $d->id]),
//                                                                            'edit_route' => route('admin.finance-contract.edit', ['finance_contract' => $d]),
                                                                            'view_permission' => Actions::View . '_' . Resources::Finance,
                                                                            'manage_permission' => Actions::Manage . '_' . Resources::Finance,
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
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-ajax', [
    'id' => 'rental',
    'url' => route('admin.util.select2-finance.creditor-leasing-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car',
    'url' => route('admin.util.select2-finance.get-car'),
])
@include('admin.components.select2-ajax', [
    'id' => 'contract',
    'url' => route('admin.util.select2-finance.get-contract'),
])
