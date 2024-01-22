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
        @section('block_options_mail')
            <a class="btn btn-primary" href="#" onclick="openModal()">
                {{ __('lang.btn_mail') }}
            </a>
        @endsection
        @include('admin.components.block-header', [
            'text' => __('debt_collections.table_rental'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_mail',
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
    {{-- overdue --}}
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('debt_collections.table_overdue'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <div class="row push">
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.doc_date') }}</p>
                        <p class="grey-text" id="doc_date"></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.range_date') }}</p>
                        <p class="grey-text" id="range_date"></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.assignment') }}</p>
                        <p class="grey-text" id="assignment"></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.doc_number') }}</p>
                        <p class="grey-text" id="doc_number"></p>
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.license_plate') }}</p>
                        <p class="grey-text" id="license_plate"></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.type') }}</p>
                        <p class="grey-text" id="type"></p>
                    </div>
                    <div class="col-sm-3">
                        <p class="size-text">{{ __('debt_collections.amount') }}</p>
                        <p class="grey-text" id="amount"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- billing --}}
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('debt_collections.table_billing'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <tr>
                                <th style="width: 5%">{{ __('lang.seq') }}</th>
                                <th>{{ __('check_billings.sending_billing_date') }}</th>
                                <th>{{ __('check_billings.check_billing_date') }}</th>
                                <th style="width: 50%;">{{ __('check_billings.detail') }}</th>
                                <th>{{ __('lang.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (sizeof($check_billing_status_list) > 0)
                                @foreach ($check_billing_status_list as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->sending_billing_date }}
                                        </td>
                                        <td>{{ $item->check_billing_date }}
                                        </td>
                                        <td>{{ $item->detail }}</td>
                                        <td>{{ $item->status ? __('check_billings.sub_status_' . $item->status) : null }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="5">" {{ __('lang.no_list') }} "</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- collection --}}
    <div class="block {{ __('block.styles') }}">
        @section('block_options_add')
            @if (empty($view))
                <button type="button" class="btn btn-primary" onclick="addCollectionLine()">เพิ่มการติดตาม</button>
            @endif
        @endsection
        @include('admin.components.block-header', [
            'text' => __('debt_collections.table_channel'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_add',
        ])
        <div class="block-content">
            <div class="mb-3" id="debt-collection-line" v-cloak>
                <div class="table-wrap db-scroll">
                    <table class="table table-striped table-vcenter">
                        <thead class="bg-body-dark">
                            <th style="width: 5%">{{ __('lang.seq') }}</th>
                            <th>{{ __('debt_collections.notification_date') }}</th>
                            <th>{{ __('debt_collections.channel') }}</th>
                            <th style="width: 50%;">{{ __('debt_collections.detail') }}</th>
                            @if (empty($view))
                                <th style="width: 5%;"></th>
                            @endif
                        </thead>
                        <tbody v-if="debt_collection_line.length > 0">
                            <tr v-for="(item,k) in debt_collection_line">
                                <td>@{{ k + 1 }}</td>
                                <td>
                                    <div class="input-group">
                                        <flatpickr id="notification_date"
                                            v-model="debt_collection_line[k].notification_date"
                                            :id="'notification_date' + k"
                                            v-bind:name="'debt_collection_line['+ k+ '][notification_date]'"
                                            :options="{}">
                                        </flatpickr>
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-check"></i>
                                        </span>
                                        <input type="hidden"
                                            v-bind:name="'data_debt_collections['+ k+ '][notification_date]'"
                                            id="notification_date" v-model="debt_collection_line[k].notification_date">
                                    </div>
                                </td>
                                <td>
                                    <select-channel :id="'channel' + k" class="form-control list"
                                        style="width: 100%;" v-model="debt_collection_line[k].channel">
                                    </select-channel>
                                    <input type="hidden" v-bind:name="'data_debt_collections['+ k+ '][channel]'"
                                        id="channel" v-model="debt_collection_line[k].channel">
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="debt_collection_line[k].detail">
                                    <input type="hidden" v-bind:name="'data_debt_collections['+ k+ '][detail]'"
                                        id="detail" v-model="debt_collection_line[k].detail">
                                </td>
                                @if (empty($view))
                                    <td class="text-center">
                                        <a class="btn btn-light" v-on:click="remove(k)"
                                            style="color: red;">{{ __('lang.delete') }}</a>
                                    </td>
                                @endif
                                <input type="hidden" v-bind:name="'data_debt_collections['+ k+ '][id]'" id="id"
                                    v-model="debt_collection_line[k].id">
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
                    'url' => 'admin.debt-collections.index',
                    'view' => empty($view) ? null : $view,
                    'manage_permission' => Actions::Manage . '_' . Resources::DebtCollection,
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
    'store_uri' => route('admin.debt-collections.store'),
])
@include('admin.debt-collections.scripts.debt-collection-script')

@push('scripts')
<script>
    $view = '{{ isset($view) }}';
    if ($view) {
        $('.form-control').prop('disabled', true);
    }

    function openModal() {
        warningAlert("ยังไม่พร้อมให้บริการ");
    }
</script>
@endpush
