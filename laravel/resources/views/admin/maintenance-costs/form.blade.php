@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('content')
    <form id="save-form">
        <input type="hidden" name="repair_oreder_id" value="{{$d?->id}}">
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('maintenance_costs.title_maintenance_title'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                <div class="justify-content-between">
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$d?->repair?->id ?? null" :list="[]"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $d?->repair?->worksheet_no ?? null,
                            ]"
                                                   :label="__('maintenance_costs.search_worksheet_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car" :value="$d?->repair?->car?->id ?? null" :list="[]"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $car_name ?? null,
                            ]"
                                                   :label="__('maintenance_costs.search_car')"/>
                        </div>
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="car_class"
                                                    :value="$d?->repair?->car?->carClass?->full_name ?? '-'"
                                                    :label="__('maintenance_costs.car_class')"/>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="center" :value="$d?->creditor?->id ?? null" :list="[]"
                                                   :optionals="[
                                'placeholder' => __('lang.search_placeholder'),
                                'ajax' => true,
                                'default_option_label' => $d?->creditor?->name,
                            ]"
                                                   :label="__('maintenance_costs.search_center')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="customer"
                                                    :value="$d?->creditor?->name ?? '-'"
                                                    :label="__('maintenance_costs.customer')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="in_center_date" :value="$d?->created_at ?? null"
                                                :label="__('maintenance_costs.search_in_center_date')"/>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="invoice_no" :value="$d?->invoice_no ?? null"
                                                    :label="__('maintenance_costs.table_invoice_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="actual_mileage" :value="$d?->invoice_no ?? null"
                                                    :label="__('maintenance_costs.actual_mileage')"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="repair-list" v-cloak>
            <div class="block {{ __('block.styles') }}">
                @section('block_options_list')
                    <button type="button" class="btn btn-primary" @click="toggleModalRepairData">
                        <i class="icon-add-circle"></i>
                        {{__('maintenance_costs.add_list')}}
                    </button>
                @endsection
                @include('admin.components.block-header', [
                    'text' => __('maintenance_costs.title_repair_list'),
                    'block_icon_class' => 'icon-document',
                    'block_option_id' => '_list',
                ])
                <div class="block-content">
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                            <tr>
                                <th style="width: 1px;">#</th>
                                <th>{{ __('lang.list') }}</th>
                                <th>{{ __('maintenance_costs.amount') }}</th>
                                <th>{{ __('maintenance_costs.price_total') }}</th>
                                <th>{{ __('maintenance_costs.discount') }}</th>
                                <th>{{ __('maintenance_costs.discount_price') }}</th>
                                <th>{{ __('maintenance_costs.add_debt') }}</th>
                                <th>{{ __('maintenance_costs.reduce_debt') }}</th>
                                <th>{{__('maintenance_costs.repair_price_total')}}</th>
                            </tr>
                            </thead>
                            <tbody v-if="repair_list_data.length != 0">
                            <tr v-for="(item,index) in repair_list_data">
                                <input type="hidden" v-model="item.id || null" :name="'repair_list['+index+'][id]'">
                                <td>
                                    @{{ index+1 }}
                                </td>
                                <td>
                                    <input type="hidden" :name="'repair_list['+index+'][repair_list_id]'"
                                           v-model="item.repair_list_id">
                                    @{{ item.repair_list_name || "-" }}
                                </td>
                                <td>
                                    @{{ item.amount || "-" }}
                                </td>
                                <td>
                                    <input-number-format-vue class="form-control number-format"
                                                             :name="'repair_list['+index+'][price_total]'"
                                                             v-model="item.price_total"/>
                                </td>
                                <td>
                                    <input-number-format-vue class="form-control number-format"
                                                             :name="'repair_list['+index+'][discount]'"
                                                             v-model="item.discount"/>
                                </td>
                                <td>
                                    @{{ item.total_discount || 0 }}
                                </td>
                                <td>
                                    <input-number-format-vue class="form-control number-format"
                                                             :name="'repair_list['+index+'][add_debt]'"
                                                             v-model="item.add_debt"/>
                                </td>
                                <td>
                                    <input-number-format-vue class="form-control number-format"
                                                             :name="'repair_list['+index+'][reduce_debt]'"
                                                             v-model="item.reduce_debt"/>
                                </td>
                                <td>
                                    @{{ item.total_repair_price }}
                                </td>
                            </tr>
                            <tr>
                                <td>

                                </td>
                                <td colspan="4" class="text-center">
                                    รวม
                                </td>
                                <td>
                                    @{{ total_discount }}
                                </td>
                                <td>
                                    @{{ summary_add_dept }}
                                </td>
                                <td>
                                    @{{ summary_reduce_debt }}
                                </td>
                                <td>
                                    @{{ summary_total_price }}
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block {{ __('block.styles') }}">
                @include('admin.components.block-header', [
                    'text' => 'ค่าใช้จ่าย',
                    'block_icon_class' => 'icon-document',
                ])
                <div class="block-content">
                    <div class="table-wrap db-scroll">
                        <table class="table table-striped table-vcenter">
                            <thead class="bg-body-dark">
                            <tr>
                                <th>{{ __('maintenance_costs.sub_total') }}</th>
                                <th>{{ __('maintenance_costs.discount_extra') }}</th>
                                <th>{{ __('maintenance_costs.vat') }}</th>
                                <th>{{ __('maintenance_costs.vat_bath') }}</th>
                                <th>{{__('maintenance_costs.total_price')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    @{{ summary_total_price }}
                                    <input type="hidden" class="form-control" name="sub_total" id="sub_total"
                                           :value="summary_total_price">
                                </td>
                                <td>
                                    <input-number-format-vue name="discount_extra" id="discount_extra"
                                                             v-model="discount_extra"/>
                                </td>
                                <td>
                                    <input-number-format-vue class="form-control" name="vat" id="vat" v-model="vat"/>
                                </td>
                                <td>
                                    <input type="hidden" class="form-control" name="vat_price" id="vat_price"
                                           v-model="vat_price">
                                    @{{ vat_price }}
                                </td>
                                <td>
                                    @{{ total_repair_price }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="rubber_week" :value="$d?->rubber_week ?? null"
                                                    :label="__('maintenance_costs.rubber_week')"/>
                        </div>
                        <div class="col-sm-9">
                            <x-forms.input-new-line id="remark_expenses" :value="$d?->remark_expenses ?? null"
                                                    :label="__('maintenance_costs.remark_expenses')"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.maintenance-costs.modals.modal-repair-list')

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <div class="justify-content-between">
                    <x-forms.submit-group
                            :optionals="['url' => 'admin.maintenance-cost.index', 'view' => empty($view) ? null : $view,'manage_permission' => Actions::Manage . '_' . Resources::MainTenanceCost]"/>
                </div>
            </div>
        </div>
    </form>
@endsection
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.maintenance-costs.scripts.script-repair-list')
@include('admin.components.form-save', [
    'store_uri' => route('admin.maintenance-cost.store'),
])
@include('admin.components.select2-ajax', [
    'id' => 'worksheet_no',
    'url' => route('admin.util.select2-repair.repair-worksheet-no-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'car',
    'url' => route('admin.util.select2-repair.repair-car-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'center',
    'url' => route('admin.util.select2-repair.creditor-services'),
])
@include('admin.components.select2-ajax', [
    'id' => 'modal_repair_list_name',
    'url' => route('admin.util.select2-repair.repair-list-item'),
])

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true)
        $('#car').prop('disabled', true)
        $('#car_class').prop('disabled', true)
        $('#center').prop('disabled', true)
        $('#customer').prop('disabled', true)
        $('#in_center_date').prop('disabled', true)
        // $('#invoice_no').prop('readonly', true)
        // $('#actual_mileage').prop('disabled', true)
        @if(isset($view))
        $('#worksheet_no').prop('disabled', true)
        $('#car').prop('disabled', true)
        $('#car_class').prop('disabled', true)
        $('#center').prop('disabled', true)
        $('#customer').prop('disabled', true)
        $('#in_center_date').prop('disabled', true)
        $('#invoice_no').prop('disabled', true)
        $('#actual_mileage').prop('disabled', true)
        $('.number-format').prop('disabled', true)
        $('#discount_extra').prop('disabled', true)
        $('#vat').prop('disabled', true)
        $('#rubber_week').prop('disabled', true)
        $('#remark_expenses').prop('disabled', true)
        @endif
    </script>
@endpush


