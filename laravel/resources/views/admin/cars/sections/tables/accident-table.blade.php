<div id="accident-list" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th style="width: 25%;">{{ __('accident_informs.worksheet_no') }}</th>
                    <th style="width: 25%;">{{ __('accident_orders.repair_worksheet_no') }}</th>
                    <th style="width: 25%;">{{ __('accident_informs.accident_type') }}</th>
                    <th style="width: 25%;">{{ __('accident_informs.main_license_plate') }}</th>
                    <th style="width: 25%;">{{ __('accident_informs.accident_datetime') }}</th>
                    <th style="width: 25%;">{{ __('accident_informs.case') }}</th>
                    <th style="width: 25%;">{{ __('accident_informs.customer') }}</th>
                    <th style="width: 20%;" class="text-center">{{ __('accident_informs.status') }}</th>
                </tr>
            </thead>
            <tbody v-if="accident_list.length > 0">
                <tr v-for="(item, index) in accident_list">
                    <td>@{{ index + 1 }}</td>
                    <td>
                        <a :href="item.accident_inform_link" target="_blank"> 
                            @{{ item.worksheet_no }}
                        </a>
                    </td>
                    <td>
                        <a :href="item.accident_order_link" target="_blank"> 
                            @{{ item.accident_order_worksheet_no }}
                        </a>
                    </td>
                    <td>@{{ item.accident_type_text }}</td>
                    <td>@{{ item.license_plate }}</td>
                    <td>@{{ item.accident_date }}</td>
                    <td>@{{ item.case_text }}</td>
                    <td>@{{ item.customer_name }}</td>
                    <td class="text-center">
                        <span :class="'badge badge-custom badge-bg-' + item.class_status" >@{{ item.status_text }}</span>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>