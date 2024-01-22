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
                                                :label="__('finance_contract.start_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :optionals="['placeholder' => __('lang.search_placeholder')]"
                                                   :label="__('finance_contract.search_status')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                    @include('admin.finance-contract.modals.modal-export-excel')
                </form>
            </div>
        </div>
    </div>

    {{--    Table Section --}}
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <a class="btn btn-primary" onclick="openModalExcel()">
                {{__('finance_request.btn_download_excel')}}
            </a>
        @endsection
        @include('admin.components.block-header', [
               'text' => __('lang.total_list'),
               'block_icon_class' => 'icon-document',
               'block_option_id' => '_list'
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
                            {{__('finance_contract.start_date')}}
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
                                    {{$d?->insurance_lot?->lot_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->insurance_lot?->creditor?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->car?->license_plate ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->car?->engine_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->car?->chassis_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->contract_no ?? '-'}}
                                </td>
                                <td>
                                    {{$d?->finance_date ?? '-'}}
                                </td>
                                <td>
                                    {!! badge_render(
                                                                                                                    __('finance_contract.status_' . $d->status_contract . '_class'),
                                                                                                                    __('finance_contract.status_' . $d->status_contract),
                                                                                                                    null,
                                                                                                                ) !!}
                                </td>
                                <td>
                                    @include('admin.components.dropdown-action', [
                                                                            'view_route' => route('admin.finance-contract.show', ['finance_contract' => $d->id]),
                                                                            'edit_route' => route('admin.finance-contract.edit', ['finance_contract' => $d->id]),
                                                                            'view_permission' => Actions::View . '_' . Resources::FinanceContract,
                                                                            'manage_permission' => Actions::Manage . '_' . Resources::FinanceContract,
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
@include('admin.components.select2-ajax', [
    'id' => 'modal_contract_no',
    'modal' => '#export-excel-modal',
    'url' => route('admin.util.select2-finance.get-contract'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_lot_no',
    'modal' => '#export-excel-modal',
    'url' => route('admin.util.select2-finance.get-lot'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_rental',
    'modal' => '#export-excel-modal',
    'url' => route('admin.util.select2-finance.creditor-leasing-list'),
    ])

@include('admin.components.select2-ajax', [
    'id' => 'modal_car',
    'modal' => '#export-excel-modal',
    'url' => route('admin.util.select2-finance.get-car'),
])
