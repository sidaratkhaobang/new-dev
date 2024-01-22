<div class="vmi-wrap">
    <div id="vmi-list" v-cloak data-detail-uri="" data-title="">
        <div class="table-wrap">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('cmi_cars.lot') }}</th>
                        <th>{{ __('cmi_cars.worksheet_no') }}</th>
                        <th>{{ __('cmi_cars.cmi_type') }}</th>
                        <th>{{ __('cmi_cars.year_insurance') }}</th>
                        <th>{{ __('cmi_cars.po_no') }}</th>
                        <th>{{ __('cmi_cars.license_plate') }}</th>
                        <th>{{ __('cmi_cars.insurance_company') }}</th>
                        <th>{{ __('cmi_cars.renter') }}</th>
                        <th class="text-center">{{ __('lang.status') }}</th>
                    </tr>
                </thead>
                <tbody v-if="vmi_list.length > 0">
                    <tr v-for="(item, index) in vmi_list">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ item.lot_number }}</td>
                        <td>
                            <a :href="item.link" target="_blank"> 
                                @{{ item.worksheet_no }}
                            </a>
                        </td>
                        <td>@{{ item.type }}</td>
                        <td>@{{ item.year }}</td>
                        <td>@{{ item.job.po_no }}</td>
                        <td>@{{ item.car.license_plate }}</td>
                        <td>@{{ item.insurer.insurance_name_th }}</td>
                        <td>@{{ item.job.customer_name }}</td>
                        <td class="text-center">
                            <span :class="'badge badge-custom badge-bg-' + item.class_status" >@{{ item.status_text }}</span>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr class="table-empty">
                        <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
