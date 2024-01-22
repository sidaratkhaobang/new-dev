<div class="block {{ __('block.styles') }}">
    @section('block_options_list')

    @endsection

    @include('admin.components.block-header', [
        'text' => __('sign_yellow_tickets.lawsuit_detail'),
        'unique_identifier' => '_list',
        'is_toggle' => true,
    ])

    <div class="block-content">
        <div id="lawsuit-vue" v-cloak data-detail-uri="" data-title="">
            <div class="table-wrap">
                <table class="table table-striped">
                    <thead class="bg-body-dark">
                        <th colspan="5" style="border-right: 2px solid #f6f8fc;" class="text-center">
                            {{ __('sign_yellow_tickets.lawsuit_detail') }}</th>
                        <th colspan="2" style="border-right: 2px solid #f6f8fc;" class="text-center">
                            {{ __('sign_yellow_tickets.responsibility') }} /
                            {{ __('sign_yellow_tickets.accident_place') }}</th>
                        <th colspan="4" style="border-right: 2px solid #f6f8fc;" class="text-center">
                            {{ __('sign_yellow_tickets.pay_receipt_detail') }}</th>
                        <th colspan="2" class="text-center">{{ __('sign_yellow_tickets.pay_find_detail') }}</th>
                        @if (!isset($view))
                            <th></th>
                        @endif
                    </thead>
                    <thead class="bg-body-dark">
                        <th class="text-center">{{ __('sign_yellow_tickets.incident_date') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.lawsuit') }} /
                            {{ __('sign_yellow_tickets.accident_place') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.amount') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.driver') }} /
                            {{ __('sign_yellow_tickets.tel') }}</th>
                        <th style="border-right: 2px solid #f6f8fc;" class="text-center">
                            {{ __('sign_yellow_tickets.is_wrong') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.responsible') }}</th>
                        <th class="text-center" style="border-right: 2px solid #f6f8fc;">
                            {{ __('sign_yellow_tickets.announ_pay_find_date') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.receipt_no') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.paid_date') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.amount') }}</th>
                        <th class="text-center" style="border-right: 2px solid #f6f8fc;">
                            {{ __('sign_yellow_tickets.dlt_receipt') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.payment') }}</th>
                        <th class="text-center">{{ __('sign_yellow_tickets.paid_date') }}</th>
                        @if (!isset($view))
                            <th class="sticky-col text-center"></th>
                        @endif
                    </thead>
                    <tbody v-if="lawsuit_list.length > 0">
                        <tr v-for="(item, index) in lawsuit_list">
                            <td class="text-center">@{{ format_date(item.incident_date) }}</td>
                            <td style="white-space: normal;" class="text-center">@{{ item.lawsuit_detail }} <br>
                                @{{ item.province_text }}</td>
                            <td class="text-center">@{{ getNumberWithCommas(item.amount) }}
                            </td>
                            <td class="text-center">@{{ item.driver }} <br> @{{ item.tel }}</td>
                            <td class="text-center">@{{ item.is_mistake == 1 ? '/' : item.is_mistake == 0 ? 'X' : '' }}</td>
                            <td class="text-center">@{{ item.responsible_text }}</td>
                            <td class="text-center">@{{ item.notification_date ? format_date(item.notification_date) : '' }}</td>
                            <td class="text-center">@{{ item.receipt_no }}</td>
                            <td class="text-center">@{{ item.payment_fine_date ? format_date(item.payment_fine_date) : '' }}</td>
                            <td class="text-center">@{{ item.amount_total }}</td>
                            <td class="text-center">
                                <template v-if="item.receipt_files && item.receipt_files.length > 0">
                                    <template v-if="item.receipt_files[0].saved == true">
                                        <a :href="item.receipt_files[0].url" class="text-primary" target="_blank">
                                            @{{ item.receipt_files.length > 0 ? item.receipt_files[0].file_name : '' }}
                                        </a>
                                    </template>
                                    <template v-else>
                                        <a class="text-muted" disabled>
                                            {{ __('customers.pending_file') }}
                                        </a>
                                    </template>
                                </template>
                            </td>
                            <td class="text-center">@{{ item.is_payment_fine == 1 ? '/' : 'X' }}</td>
                            <td class="text-center">@{{ item.payment_date ? format_date(item.payment_date) : '' }}</td>
                            @if (!isset($view))
                                <td class="sticky-col text-center">
                                    <div class="btn-group">
                                        <div class="col-sm-12">
                                            <div class="dropdown dropleft">
                                                <button type="button"
                                                    class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                    id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                    <a class="dropdown-item" v-on:click="editCar(index)"><i
                                                            class="far fa-edit me-1"></i> แก้ไข</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            @endif
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][id]'" v-bind:value="item.id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][incident_date]'"
                                v-bind:value="item.incident_date">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][amount]'"
                                v-bind:value="item.amount">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][driver]'"
                                v-bind:value="item.driver">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][driver_type]'"
                                v-bind:value="item.driver_type">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][lawsuit_detail]'"
                                v-bind:value="item.lawsuit_detail">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][province_id]'"
                                v-bind:value="item.province_id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][province_text]'"
                                v-bind:value="item.province_text">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][responsible_id]'"
                                v-bind:value="item.responsible_id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][responsible_text]'"
                                v-bind:value="item.responsible_text">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][tel]'"
                                v-bind:value="item.tel">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][training_id]'"
                                v-bind:value="item.training_id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][training_text]'"
                                v-bind:value="item.training_text">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][mistake_id]'"
                                v-bind:value="item.mistake_id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][mistake_text]'"
                                v-bind:value="item.mistake_text">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][notification_date]'"
                                v-bind:value="item.notification_date">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][receipt_no]'"
                                v-bind:value="item.receipt_no">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][payment_fine_date]'"
                                v-bind:value="item.payment_fine_date">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][amount_total]'"
                                v-bind:value="item.amount_total">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][payment_date]'"
                                v-bind:value="item.payment_date">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][is_payment_fine_id]'"
                                v-bind:value="item.is_payment_fine_id">
                            <input type="hidden" v-bind:name="'lawsuit_data['+ index+ '][is_payment_fine_text]'"
                                v-bind:value="item.is_payment_fine_text">
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr class="table-empty">
                            <td class="text-center" colspan="8">"
                                {{ __('lang.no_list') }} "</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-12 text-end">

                </div>
            </div>
        </div>
        <br>


    </div>
</div>
@include('admin.sign-yellow-tickets.modals.lawsuit-modal')
