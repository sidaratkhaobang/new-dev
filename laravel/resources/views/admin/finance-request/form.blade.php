@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <form id="save-form">
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_request.form_header'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <input type="hidden" name="prepare_id" value="{{$prepare?->id ?? null}}">
                            <input type="hidden" name="lot_id" value="{{$lot_id ?? null}}">
                            <x-forms.input-new-line id="lot_name" :value="$lot_name ?? null"
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
                            <x-forms.date-input id="date_create" :value="$prepare?->creation_date ?? null"
                                                :label="__('finance_request.search_date_create')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="bill_date" :value="$prepare?->billing_date ?? null"
                                                :label="__('finance_request.bill_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="payment_date" :value="$prepare?->payment_date ?? null"
                                                :label="__('finance_request.payment_date')"/>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_request.car_list'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    {{ __('finance_request.po_id') }}
                                </th>
                                <th>
                                    {{__('finance_request.engine_no')}}
                                </th>
                                <th>
                                    {{__('finance_request.chassis_no')}}
                                </th>
                                <th>
                                    {{__('finance_request.number_installments')}}
                                </th>
                                <th>
                                    {{__('finance_request.accessory_price_total')}}
                                </th>
                                <th>
                                    {{__('finance_request.car_vat_price')}}
                                </th>
                                <th>
                                    {{__('finance_request.accessory_car_vat')}}
                                </th>
                                <th>
                                    {{__('finance_request.make_finance')}}
                                </th>
                                <th>

                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!$list?->isEmpty())
                                @foreach($list as $key => $d )
                                    <input type="hidden" name="finance_car_data[{{$key}}][finance_id]"
                                           value="{{$d?->id}}">
                                    <tr>
                                        <td>
                                            {{$list->currentPage() * $list->perPage() - $list->perPage() + 1 +$key}}
                                        </td>
                                        <td>
                                            {{$d?->purchase_order?->po_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car?->engine_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car?->chassis_no ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->number_installments ?? '-'}}
                                        </td>
                                        <td>
                                            {{$d?->accessory_price? number_format($d?->accessory_price): '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car_price_vat? number_format($d?->car_price_vat): '-'}}
                                        </td>
                                        <td>
                                            {{$d?->car_total_price? number_format($d?->car_total_price): '-'}}
                                        </td>
                                        <td>
                                            <x-forms.select-option id="finance_car_data[{{$key}}][finance_type]"
                                                                   :value="$d?->type_car_financing ?? null"
                                                                   :list="$d?->finance_type_list ?? []"
                                                                   :optionals="['placeholder' => __('lang.search_placeholder'),'select_class' => 'finance_car_data js-select2-default']"
                                                                   :label="null"/>
                                        </td>
                                        <td>
                                            @include('admin.components.dropdown-action', [
                                                                                    'view_route' => route('admin.finance-request.finance-request-car-detail.show', ['finance_request_id' => $d->id]),
    //                                                                                'edit_route' => route('admin.finance-request.edit', ['finance_request' => $d->id]),
                                                                                    'view_permission' => Actions::View . '_' . Resources::FinanceRequest,
                                                                                    'manage_permission' => Actions::Manage . '_' . Resources::FinanceRequest,
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
                </div>
            </div>
        </div>
    </form>
    {{--    submit button   --}}
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="justify-content-between">
                <x-forms.submit-group
                    :optionals="['url' => 'admin.finance-request.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::FinanceRequest]"/>
            </div>
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
@include('admin.components.form-save', [
    'store_uri' => route('admin.finance-request.store'),
])

@push('scripts')
    <script>
        @if(isset($view))
        $('#lot_name').prop('readonly', true)
        $('#rental').prop('disabled', true)
        $('#bill_date').prop('disabled', true)
        $('#date_create').prop('disabled', true)
        $('#payment_date').prop('disabled', true)
        $('.finance_car_data').prop('disabled', true)
        @else
        $('#lot_name').prop('readonly', true)
        $('#rental').prop('disabled', true)
        @endif


    </script>
@endpush
