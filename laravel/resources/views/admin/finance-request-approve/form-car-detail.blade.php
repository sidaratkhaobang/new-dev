@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('styles')

@endpush
@section('content')
    @include('admin.components.creator')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.car_detail'),
                'block_icon_class' => 'icon-document',
            ])
        <div class="block-content">
            <div class="form-group row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="po_id"
                                   :value="$d?->purchase_order?->po_no ?? '-'"
                                   :label="__('finance_request.po_id')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="po_id"
                                   :value="$d?->purchase_order?->request_date ?? '-'"
                                   :label="__('finance_request.request_date')"/>
                </div>
            </div>
            <div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        @include('admin.finance-request.sections.car-detail')
                    </div>
                    <div class="col-sm-9">
                        <div>
                            @include('admin.components.block-header', [
                'text' => __('finance_request.dealer_detail'),
                'block_icon_class' => 'icon-document',
            ])
                            <div class="block-content">
                                <div class="form-group row push mb-4">
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="$d?->purchase_order?->creditor?->name ?? '-'"
                                                       :label="__('finance_request.dealer')"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="$d?->purchase_order?->creditor?->tel ?? '-'"
                                                       :label="__('finance_request.tel')"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="$d?->purchase_order?->creditor?->email ?? '-'"
                                                       :label="__('finance_request.email')"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            @include('admin.components.block-header', [
                                            'text' => __('finance_request.customer_detail'),
                                            'block_icon_class' => 'icon-document',
                                        ])
                            <div class="block-content">
                                <div class="form-group row push mb-4">
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="$customer_data?->customer?->name ?? '-'"
                                                       :label="__('finance_request.dealer')"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="(!empty($d?->purchase_order?->purchaseRequisiton?->rental_type))? __('rental_categories.rental_type_'.$d?->purchase_order?->purchaseRequisiton?->rental_type)  : '-'"
                                                       :label="__('finance_request.contract_category')"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <x-forms.label id="delivery_date"
                                                       :value="$delivery_date ?? '-'"
                                                       :label="__('finance_request.delivery_date')"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <x-forms.label id="rental_price"
                                                       :value="number_format($rental_price) ?? '-'"
                                                       :label="__('finance_request.rental_price')"/>
                                    </div>

                                </div>
                                <div class="form-group row push mb-4">
                                    <div class="col-sm-3">
                                        <x-forms.label id="po_id"
                                                       :value="$customer_data?->rental_duration ?? '-'"
                                                       :label="__('finance_request.rental_month')"/>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.accessory_detail'),
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
                            {{ __('finance_request.po_id') }}
                        </th>
                        <th>
                            {{__('finance_request.company')}}
                        </th>
                        <th>
                            {{__('finance_request.price')}}
                        </th>
                        <th>

                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$accessory_list->isEmpty())
                        @foreach($accessory_list as $key => $value_accessory )
                            <tr>
                                <td>
                                    {{++$key}}
                                </td>
                                <td>
                                    {{$value_accessory?->worksheet_no ?? '-'}}
                                </td>
                                <td>
                                    {{$value_accessory?->supplier?->name ?? '-'}}
                                </td>
                                <td>
                                    {{$value_accessory?->accessory_price ? number_format($value_accessory?->accessory_price): '-'}}
                                </td>
                                <td class="text-center toggle-table" style="width: 30px">
                                    <i class="fa fa-angle-down text-muted"></i>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td></td>
                                <td class="td-table" colspan="7">
                                    <table class="table table-striped">
                                        <thead class="bg-body-dark">
                                        <th style="width: 1px;">รายการอุปกรณ์</th>
                                        <th style="width: 20%">จำนวนต่อคัน</th>
                                        <th style="width: 20%" class="text-end">จำนวนทั้งหมด</th>
                                        <th style="width: 45%" class="text-center">หมายเหตุ</th>
                                        </thead>
                                        <tbody>
                                        @if(!empty($value_accessory?->accessory_item))
                                            @foreach($value_accessory?->accessory_item as $key_accessory => $value_accessory)
                                                <tr>
                                                    <td>
                                                        {{$value_accessory?->accessory?->name ?? '-'}}
                                                    </td>
                                                    <td>
                                                        {{$value_accessory?->amount ?? '-'}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$value_accessory?->amount ?? '-'}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$value_accessory?->remark ?? '-'}}
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

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.car_price_title'),
                'block_icon_class' => 'icon-document',
            ])
        <div class="block-content">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th class="text-center">
                        {{ __('finance_request.car_price_non_vat') }}
                    </th>
                    <th class="text-center">
                        {{ __('finance_request.car_price_vat') }}
                    </th class="text-center">
                    <th class="text-center">
                        {{__('finance_request.car_accessory_price')}}
                    </th>
                    <th class="text-center">
                        {{__('finance_request.car_accessory_vat_price')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">
                        {{$po_data?->subtotal ? number_format($po_data?->subtotal) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$po_data?->total ? number_format($po_data?->total) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$total_accessory_price ? number_format($total_accessory_price) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$car_accessory_vat_price ? number_format($car_accessory_vat_price) : '-'}}
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.insurance_title'),
                'block_icon_class' => 'icon-document',
            ])
        <div class="block-content">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th class="text-center">
                        {{ __('finance_request.insurance_car_body') }}
                    </th>
                    <th class="text-center">
                        {{ __('finance_request.insurance_accessory') }}
                    </th class="text-center">
                    <th class="text-center">
                        {{__('finance_request.insurance_total')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">
                        {{$vmi?->sum_insured_car ? number_format($vmi?->sum_insured_car) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$vmi?->sum_insured_accessory ? number_format($vmi?->sum_insured_accessory) : '-'}}
                    </td>
                    <td class="text-center">

                        {{$vmi?->insurance_total ? number_format($vmi?->insurance_total) : '-'}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.insurance_cmi_title'),
                'block_icon_class' => 'icon-document',
            ])
        <div class="block-content">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th class="text-center">
                        {{ __('finance_request.cmi_type_1') }}
                    </th>
                    <th class="text-center">
                        {{ __('finance_request.cmi_vat') }}
                    </th class="text-center">
                    <th class="text-center">
                        {{__('finance_request.cmi_total')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">
                        {{$cmi?->premium ? number_format($cmi?->premium) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$cmi?->tax ? number_format($cmi?->tax) : '-'}}
                    </td>
                    <td class="text-center">
                        {{$cmi?->cmi_total ? number_format($cmi?->cmi_total) : '-'}}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
                'text' => __('finance_request.registered_title'),
                'block_icon_class' => 'icon-document',
            ])
        <div class="block-content">
            <div class="form-group row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="registered_price"
                                   :value="$car_registered?->registered_price ? number_format($car_registered?->registered_price): '-'"
                                   :label="__('finance_request.registered_price')"/>
                </div>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="justify-content-between">

                <x-forms.submit-group
                    :optionals="['view' => empty($view) ? true : $view,'manage_permission' => Actions::Manage . '_' . Resources::InsuranceCompanies]"/>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.toggle-table').click(function () {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-up text-muted');
        });
    </script>
@endpush
