@section('block_options_cost')
    <div class="block-options-item">
        @if (Route::is('*.edit') || Route::is('*.create'))
            <button class="btn btn-primary btn-custom-size" type="button" onclick="openCostModal()">
                <i class="fa fa-plus-circle me-1"></i> {{ __('litigations.add_cost') }}
            </button>
        @endif
    </div>
@endsection

@include('admin.litigations.modals.cost-modal')
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('litigations.cost_data'),
        'block_icon_class' => 'icon-document',
        'block_option_id' => '_cost',
    ])
    <div class="block-content">
        <div id="cost-list" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap mb-4">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th style="width: 1px;">#</th>
                        <th style="width: 20%">{{ __('litigations.list') }}</th>
                        <th style="width: 15%">{{ __('litigations.bank') }}</th>
                        <th style="width: 15%">{{ __('litigations.payment_channel') }}</th>
                        <th style="width: 20%">{{ __('litigations.account_no_check') }}</th>
                        <th style="width: 15%">{{ __('litigations.payment_date_ac_check') }}</th>
                        <th class="text-end" style="width: 15%">{{ __('litigations.amount') }}</th>
                        
                        <th style="width: 10%" class="sticky-col text-center"></th>
                    </thead>
                    <tbody v-if="cost_list.length > 0">
                        <tr v-for="(item, index) in cost_list">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.list }}</td>
                            <td>@{{ item.bank_text }}</td>
                            <td>@{{ item.payment_channel_text }}</td>
                            <td>@{{ item.number }}</td>
                            <td>@{{ formatDate(item.date) }}</td>
                            <td class="text-end">@{{ numberWithCommas(item.amount) }}</td>
                            <td v-if="editable(item)" class="sticky-col text-center">
                                @include('admin.components.dropdown-action-vue')
                            </td>
                            <td v-else></td>
                            <input type="hidden" v-bind:name="'costs['+ index +'][id]'" id="cost_id" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'costs['+ index +'][list]'" id="cost_list" v-bind:value="item.list">
                            <input type="hidden" v-bind:name="'costs['+ index +'][bank_id]'" id="cost_bank_id" v-bind:value="item.bank_id">
                            <input type="hidden" v-bind:name="'costs['+ index +'][payment_channel]'" id="cost_payment_channel" v-bind:value="item.payment_channel">
                            <input type="hidden" v-bind:name="'costs['+ index +'][number]'" id="cost_number" v-bind:value="item.number">
                            <input type="hidden" v-bind:name="'costs['+ index +'][date]'" id="cost_date" v-bind:value="item.date">
                            <input type="hidden" v-bind:name="'costs['+ index +'][amount]'" id="cost_amount" v-bind:value="item.amount">
                        </tr>
                        <tr>
                            <td class="text-center" colspan="6">{{ __('lang.sum') }}</td>
                            <td class="text-end">@{{ numberWithCommas(summary) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="pending_delete_cost_ids.length > 0">
                    <template v-for="item in pending_delete_cost_ids"> 
                        <input type="hidden" v-bind:name="'delete_cost_ids[]'" v-bind:value="item">
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>