@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('page_title_sub')
    {!! badge_render(
         __('finance_contract.status_' . $d?->status . '_class'),
         __('finance_contract.status_' . $d?->status),
         null,
    ) !!}
@endsection
@section('history')
    @include('admin.components.btns.history')
    @include('admin.components.transaction-modal')
@endsection
@section('content')
    @include('admin.components.creator')
    <form id="save-form">
        <input type="hidden" id="hire_purchase_id" name="hire_purchase_id" value="{{$d?->id}}">
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_contract.car_detail_title'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="form-group row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.label id="lot_name"
                                       :value="$d?->car?->carClass?->full_name ?? '-'"
                                       :label="__('finance_contract.car_name')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.label id="lot_name"
                                       :value="$d?->car?->engine_no ?? '-'"
                                       :label="__('finance_contract.engine_no')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.label id="lot_name"
                                       :value="$d?->car?->chassis_no ?? '-'"
                                       :label="__('finance_contract.chassis_no')"/>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.label id="lot_name"
                                       :value="$d?->car?->license_plate ?? '-'"
                                       :label="__('finance_contract.license_plate')"/>
                    </div>
                </div>
            </div>
        </div>


        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_contract.contract_title'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="form-group row push mb-4">
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
                        <x-forms.input-new-line id="contract_no" :value="$d?->contract_no ?? null"
                                                :label="__('finance_contract.contract_no')"
                                                :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="finance_date" :value="$d?->finance_date ?? null"
                                            :label="__('finance_contract.finance_date')"
                                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="number_installments" :value="$d?->number_installments"
                                               :list="$number_installments_list"
                                               :optionals="[
                                                   'placeholder' => __('lang.search_placeholder'),
                                                   ]"
                                               :label="__('finance_contract.number_installments')"/>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="contract_start_date" :value="$d?->contract_start_date ?? null"
                                            :label="__('finance_contract.contract_start_date')"
                                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="contract_end_date" :value="$d?->contract_end_date ?? null"
                                            :label="__('finance_contract.contract_end_date')"
                                            :optionals="['required' => true]"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_contract.payment_detail_title'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.date-input id="first_payment_date" :value="$d?->first_payment_date ?? null"
                                            :label="__('finance_contract.first_payment_date')"
                                            :optionals="['required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="amount_installments" :value="$d?->amount_installments ?? null"
                                                :label="__('finance_contract.amount_installments')"
                                                :optionals="['input_class' => 'number-format','required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="pay_installments" :value="$d?->pay_installments ?? null"
                                                :label="__('finance_contract.pay_installments')"
                                                :optionals="['input_class' => 'number-format','required' => true]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="payment" :value="$d?->payment ?? 2" :list="$payment_list"
                                              :label="__('finance_contract.payment')"
                                              :optionals="['required' => true]"/>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="interest_rate_percent"
                                                :value=" round($d?->interest_rate_percent,4)"
                                                :label="__('finance_contract.interest_rate_percent')"
                                                :optionals="['required' => true,'type' => 'number']"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.radio-inline id="interest_rate" :value="$d?->interest_rate ?? 1"
                                              :list="$interest_rate_list"
                                              :label="__('finance_contract.interest_rate')"
                                              :optionals="['required' => true]"/>
                    </div>
                </div>
            </div>
        </div>

        <div id="payment_detail" class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_contract.down_payment_title'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>{{__('finance_contract.car_price_with_vat')}}</th>
                        <th>{{__('finance_contract.down_payment_percent')}}</th>
                        <th>{{__('finance_contract.down_payment_total')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            {{(!empty($d?->purchase_order?->total)) ? number_format($d?->purchase_order?->total):'-'}}
                        </td>
                        <td>
                            <input id="down_payment_percent" v-model="down_payment_percent" type="text"
                                   name="down_payment_percent"
                                   class="form-control number-format">
                        </td>
                        <td>
                            @{{ down_payment_percent_total }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="vr_detail" v-cloak class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                    'text' => __('finance_contract.rv_title'),
                    'block_icon_class' => 'icon-document',
                ])
            <div class="block-content">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th>{{__('finance_contract.list')}}</th>
                        <th>{{__('finance_contract.total_with_vat')}}</th>
                        <th>{{__('finance_contract.rv_percent')}}</th>
                        <th>{{__('finance_contract.rv_total')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($finance_type) && $finance_type?->type_car_financing == FinanceCarStatusEnum::CAR ||  $finance_type?->is_car_financing_only == FinanceCarStatusEnum::CAR)
                        <tr>
                            <td>
                                รถ
                            </td>
                            <td>
                                {{(!empty($d?->purchase_order?->total)) ? number_format($d?->purchase_order?->total):'-'}}
                            </td>
                            <td>

                                <input id="rv_car_percent" v-model="rv_car_percent" type="number"
                                       name="rv_car_percent"
                                       class="form-control number-format">
                            </td>
                            <td>
                                @{{ rv_car_summary_total }}
                            </td>
                        </tr>
                    @endif

                    @if(!empty($finance_type) && $finance_type?->type_car_financing == FinanceCarStatusEnum::CAR_AND_ACCESSORY)
                        <tr>
                            <td>
                                อุปกรณ์
                            </td>
                            <td>
                                {{(!empty($total_accessory_price)) ? number_format($total_accessory_price):'0'}}
                            </td>
                            <td>
                                <input id="rv_accessory_percent" v-model="rv_car_accessory_percent" type="number"
                                       name="rv_accessory_percent"
                                       class="input_number form-control">
                            </td>
                            <td>
                                @{{ rv_car_accessory_summary_total }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>
                            รวม
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                        <td>
                            @{{ rv_total }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <x-forms.input-new-line id="remark" :value="$d?->remark ?? null"
                                        :label="__('lang.remark')"/>
            </div>
        </div>
    </form>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="justify-content-between">
                <x-forms.submit-group
                    :optionals="['url' => 'admin.finance-contract.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::FinanceContract]"/>
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
    'store_uri' => route('admin.finance-contract.store'),
])
@include('admin.finance-contract.scripts.script-rv')
@include('admin.finance-contract.scripts.script-payment')
@push('scripts')
    <script>
        @if(isset($view))
        $('#contract_no').prop('disabled', true)
        $('#finance_date').prop('disabled', true)
        $('#number_installments').prop('disabled', true)
        $('#contract_start_date').prop('disabled', true)
        $('#contract_end_date').prop('disabled', true)
        $('#first_payment_date').prop('disabled', true)
        $('#interest_rate').prop('disabled', true)
        $('#down_payment_percent').prop('disabled', true)
        $('#rv_car_percent').prop('disabled', true)
        $('#rv_accessory_percent').prop('disabled', true)
        $('#amount_installments').prop('disabled', true)
        $('#pay_installments').prop('disabled', true)
        $('#interest_rate_percent').prop('disabled', true)
        $('#remark').prop('disabled', true)
        @endif
        $('#rental').prop('disabled', true)

        $(document).on('keyup change', '#interest_rate_percent', function () {
            var val = $('#interest_rate_percent').val()
            if (val) {
                val = parseFloat(val).toFixed(4).toLocaleString();
                $('#interest_rate_percent').val(val)
            }

        })

        $(document).on('change', '#first_payment_date,#number_installments', function () {
            let first_payment_date = $('#first_payment_date').val();
            let number_installments = $('#number_installments').val();
            if (first_payment_date && number_installments) {
                number_installments = parseInt(number_installments) - 1;
                let date = new Date(first_payment_date);
                date.setMonth(date.getMonth() + parseInt(number_installments));
                let formattedDate = formatDate(date);
                $('#contract_end_date').val(formattedDate)
            }
        });

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

    </script>
@endpush
