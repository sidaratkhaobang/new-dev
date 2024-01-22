<div id="repair-list" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('repairs.worksheet_no') }}</th>
                    <th>{{ __('repairs.repair_ref') }}</th>
                    <th>{{ __('repairs.type_job') }}</th>
                    <th>{{ __('repairs.contact') }}</th>
                    <th>{{ __('repairs.license_plate') }}</th>
                    <th>{{ __('repairs.alert_date') }}</th>
                    <th>{{ __('repairs.center_date') }}</th>
                    <th>{{ __('repairs.expected_date') }}</th>
                    <th>{{ __('repairs.completed_date') }}</th>
                    <th>{{ __('lang.status') }}</th>
                </tr>
            </thead>
            <tbody v-if="repair_list.length > 0">
                <tr v-for="(item, index) in repair_list">
                    <td>@{{ index + 1}}</td>
                    <td>
                        <a :href="item.repair_link" target="_blank"> 
                            @{{ item.worksheet_no }}
                        </a>
                    </td>
                    <td>
                        <a :href="item.repair_order_link" target="_blank"> 
                            @{{ item.order_worksheet_no }}
                        </a>
                    </td>
                    <td>@{{ item.repair_type_text }}</td>
                    <td>@{{ item.contact }}</td>
                    <td>@{{ item.license_plate }}</td>
                    <td>@{{ item.repair_date }}</td>
                    <td>@{{ item.in_center_date }}</td>
                    <td>@{{ item.expected_repair_date }}</td>
                    <td>@{{ item.completed_date }}</td>
                    <td class="text-center">
                        <span :class="'badge badge-custom badge-bg-' + item.class_status" >@{{ item.status_text }}</span>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>