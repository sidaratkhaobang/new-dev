<div class="rental-list-list">
    <div id="rental-list" v-cloak data-detail-uri="" data-title="">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th style="width: 13%;">{{ __('short_term_rentals.rental_no') }}</th>
                        <th style="width: 13%;">{{ __('short_term_rentals.branch') }}</th>
                        <th style="width: 18%;">{{ __('short_term_rentals.customer') }}</th>
                        <th style="width: 13%;">{{ __('short_term_rentals.service_type') }}</th>
                        <th>{{ __('short_term_rentals.rental_date') }}</th>
                        <th style="width: 10%;" class="text-center">{{ __('short_term_rentals.status') }}</th>
                    </tr>
                </thead>
                <tbody v-if="rental_list.length > 0">
                    <tr v-for="(item, index) in rental_list">
                        <td>@{{ index + 1 }}</td>
                        <td>
                            <a :href="item.link" target="_blank"> 
                                @{{ item.worksheet_no }}
                            </a>
                        </td>
                        <td>@{{ item.branch_name }}</td>
                        <td>@{{ item.customer_name }}</td>
                        <td>@{{ item.service_type_name }}</td>
                        <td>@{{ item.created_date }}</td>
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
</div>
