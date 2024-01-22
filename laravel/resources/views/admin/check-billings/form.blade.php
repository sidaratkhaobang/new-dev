@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush
@section('content')
    <form id="save-form">
        {{-- rental --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('debt_collections.table_rental'),
                'block_icon_class' => 'icon-document',
            ])
            @include('admin.debt-collections.sections.rental-info')
        </div>
        {{-- customer --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('debt_collections.table_customer'),
                'block_icon_class' => 'icon-document',
            ])
            @include('admin.debt-collections.sections.customer-info')
        </div>
        {{-- car --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('check_billings.table_car'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="row push">
                        <div class="col-sm-6">
                            <p class="size-text">{{ __('cars.brand') }}</p>
                            <p class="grey-text" id="car_class_name">{{ $d->car_class_name ? $d->car_class_name : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('cars.engine_no') }}</p>
                            <p class="grey-text" id="engine_no">{{ $d->engine_no ? $d->engine_no : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('cars.chassis_no') }}</p>
                            <p class="grey-text" id="chassis_no">{{ $d->chassis_no ? $d->chassis_no : null }}</p>
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('cars.license_plate') }}</p>
                            <p class="grey-text" id="license_plate">{{ $d->license_plate ? $d->license_plate : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('cars.car_age') }}</p>
                            <p class="grey-text" id="car_age">{{ $d->car_age ? $d->car_age : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('cars.register_date') }}</p>
                            <p class="grey-text" id="register_date">{{ $d->register_date ? $d->register_date : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="size-text">{{ __('check_billings.car_status') }}</p>
                            <p class="grey-text" id="car_status">{{ $d->car_status ? $d->car_status : null }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- invoice --}}
        <div class="block {{ __('block.styles') }}">
            @include('admin.components.block-header', [
                'text' => __('check_billings.table_invoice'),
                'block_icon_class' => 'icon-document',
            ])
            <div class="block-content">
                <div class="justify-content-between mb-4">
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.select-option id="schedule_billing" :value="null" :list="null"
                                :label="__('check_billings.schedule_billing')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="start_bill_date" :value="null" :label="__('check_billings.start_bill_date')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="type_bill" :value="null" :list="null" :label="__('check_billings.type_bill')" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-3">
                            <x-forms.select-option id="invoice_no" :value="null" :list="null" :label="__('check_billings.invoice_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="invoice_date" :value="null" :label="__('check_billings.invoice_date')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="period_no" :value="null" :label="__('check_billings.period_no')" />
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="document" :value="$d->document" :label="__('check_billings.doc_bill')" />
                        </div>
                        <div class="col-sm-6">
                            <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('check_billings.remark')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Chekc Billing Status --}}
        <div class="block {{ __('block.styles') }}">
        @section('block_options_add')
            @if (empty($view))
                <button type="button" class="btn btn-primary" onclick="addBillingStatusLine()">เพิ่มการติดตาม</button>
            @endif
        @endsection
        @include('admin.components.block-header', [
            'text' => __('check_billings.table_status'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_add',
        ])
        <div class="block-content">
            <div class="mb-3" id="check-billing-status-line" v-cloak>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <th style="width: 5%">{{ __('lang.seq') }}</th>
                            <th>{{ __('check_billings.sending_billing_date') }}</th>
                            <th>{{ __('check_billings.check_billing_date') }}</th>
                            <th style="width: 50%;">{{ __('check_billings.detail') }}</th>
                            <th>{{ __('lang.status') }}</th>
                            @if (empty($view))
                                <th style="width: 5%;"></th>
                            @endif
                        </thead>
                        <tbody v-if="check_billing_status_line.length > 0">
                            <tr v-for="(item,k) in check_billing_status_line">
                                <td>@{{ k + 1 }}</td>
                                <td>
                                    <div class="input-group">
                                        <flatpickr id="sending_billing_date"
                                            v-model="check_billing_status_line[k].sending_billing_date"
                                            :id="'sending_billing_date' + k"
                                            v-bind:name="'check_billing_status_line['+ k+ '][sending_billing_date]'"
                                            :options="{}">
                                        </flatpickr>
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-check"></i>
                                        </span>
                                        <input type="hidden"
                                            v-bind:name="'data_check_billing_status['+ k+ '][sending_billing_date]'"
                                            id="sending_billing_date"
                                            v-model="check_billing_status_line[k].sending_billing_date">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <flatpickr id="check_billing_date"
                                            v-model="check_billing_status_line[k].check_billing_date"
                                            :id="'check_billing_date' + k"
                                            v-bind:name="'check_billing_status_line['+ k+ '][check_billing_date]'"
                                            :options="{}">
                                        </flatpickr>
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-check"></i>
                                        </span>
                                        <input type="hidden"
                                            v-bind:name="'data_check_billing_status['+ k+ '][check_billing_date]'"
                                            id="check_billing_date"
                                            v-model="check_billing_status_line[k].check_billing_date">
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control"
                                        v-model="check_billing_status_line[k].detail">
                                    <input type="hidden" v-bind:name="'data_check_billing_status['+ k+ '][detail]'"
                                        id="detail" v-model="check_billing_status_line[k].detail">
                                </td>
                                <td>
                                    <select-status :id="'status' + k" class="form-control list"
                                        style="width: 100%;" v-model="check_billing_status_line[k].status">
                                    </select-status>
                                    <input type="hidden" v-bind:name="'data_check_billing_status['+ k+ '][status]'"
                                        id="status" v-model="check_billing_status_line[k].status">
                                </td>
                                @if (empty($view))
                                    <td class="text-center">
                                        <a class="btn btn-light" v-on:click="remove(k)"
                                            style="color: red;">{{ __('lang.delete') }}</a>
                                    </td>
                                @endif
                                <input type="hidden" v-bind:name="'data_check_billing_status['+ k+ '][id]'"
                                    id="id" v-model="check_billing_status_line[k].id">
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr class="table-empty">
                                <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        </tbody>
                        <template v-for="(input,k) in del_input_id">
                            <input type="hidden" v-bind:name="'del_section[]'" id="del_input_id"
                                v-bind:value="input">
                        </template>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="justify-content-between">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.submit-group :optionals="[
                    'url' => 'admin.check-billings.index',
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::CheckBillingDate,
                ]" />
            </div>
        </div>
    </div>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.form-save', [
    'store_uri' => route('admin.check-billings.store'),
])
@include('admin.check-billings.scripts.check-billing-status-script')

@push('scripts')
<script>
    $('#schedule_billing').prop('disabled', true);
    $('#start_bill_date').prop('disabled', true);
    $('#type_bill').prop('disabled', true);
    $('#invoice_no').prop('disabled', true);
    $('#invoice_date').prop('disabled', true);
    $('#period_no').prop('disabled', true);

    $view = '{{ isset($view) }}';
    if ($view) {
        $('.form-control').prop('disabled', true);
    }

    function openModal() {
        warningAlert("ยังไม่พร้อมให้บริการ");
    }
</script>
@endpush
