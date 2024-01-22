@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <div id="export-excel">
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
                                <x-forms.select-option id="lot_no" :value="$lot_no" :list="[]"
                                                       :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $lot_name,
                                                   ]"
                                                       :label="__('finance_request.search_lot_no')"/>
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
                                <x-forms.select-option id="contract" :value="$contract" :list="[]"
                                                       :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   'ajax' => true,
                                                   'default_option_label' => $contract_name,
                                                   ]"
                                                       :label="__('finance_contract.search_contract')"/>
                            </div>
                        </div>
                        <div class="form-group row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.date-input id="date_create" :value="$date_create"
                                                    :label="__('finance_request.search_date_create')"/>
                            </div>
                            <div class="col-sm-3">
                                <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                       :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                       :label="__('finance_contract.search_status')"/>
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
                                {{ __('finance_contract.car_license_plate') }}
                            </th>
                            <th>
                                {{__('finance_contract.car_engine_no')}}
                            </th>
                            <th>
                                {{__('finance_contract.car_chassis_no')}}
                            </th>
                            <th>
                                {{__('finance_contract.search_contract')}}
                            </th>
                            <th>
                                {{__('finance_request.search_date_create')}}
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
                            <tr>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>

                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                                                            'view_route' => route('admin.finance-request.show', ['finance_request' => $d]),
                                                                            'edit_route' => route('admin.finance-request.edit', ['finance_request' => $d]),
                                                                            'view_permission' => Actions::View . '_' . Resources::FinanceRequest,
                                                                            'manage_permission' => Actions::Manage . '_' . Resources::FinanceRequest,
                                                                        ])
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="row push mt-4">
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary btn-custom-size"
                                onclick="window.history.back();">{{ __('lang.back') }}</button>
                        @if(!isset($view))
                            <button type="button"
                                    @click="alertWaiting"
                                    class="btn btn-primary btn-custom-size btn-excel-download">{{ __('finance_request.btn_download_excel') }}</button>
                        @endif
                    </div>
                </div>
                {{--            {!! $list->appends(\Request::except('page'))->render() !!}--}}
            </div>
        </div>
    </div>
@endsection
@include('admin.finance-contract.scripts.script-export-excel')
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
@include('admin.components.select2-ajax', [
    'id' => 'car',
    'url' => route('admin.util.select2-finance.get-car'),
])
@include('admin.components.select2-ajax', [
    'id' => 'contract',
    'url' => route('admin.util.select2-finance.get-contract'),
])
