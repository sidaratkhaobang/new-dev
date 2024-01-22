<div id="equipment-list" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th>{{ __('install_equipments.install_equipment_no') }}</th>
                    <th>{{ __('install_equipments.install_equipment_po_no') }}</th>
                    <th>{{ __('install_equipments.accessory') }}</th>
                    <th>{{ __('install_equipments.supplier_en') }}</th>
                    <th>{{ __('install_equipments.created_at') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('lang.status') }}</th>
                </tr>
            </thead>
            <tbody v-if="equipment_list.length > 0">
                <tr v-for="(item, index) in equipment_list">
                    <td>@{{ index + 1 }}</td>
                    <td>
                        <a :href="item.ie_link" target="_blank"> 
                            @{{ item.worksheet_no }}
                        </a>
                    </td>
                    <td>
                        <a :href="item.po_link" target="_blank"> 
                            @{{ item.po_worksheet_no }}
                        </a>
                    </td>
                    <td>@{{ item.name }}</td>
                    <td>@{{ item.supplier_name }}</td>
                    <td>@{{ item.created_date }}</td>
                    <td class="text-center">
                        <span v-if="['OVERDUE', 'INSTALL_IN_PROCESS'].includes(item.status)" 
                            :class="'badge badge-custom badge-bg-' + item.class_status + ' text-' + tem.class_status " >
                            @{{ item.status_text  + ' (' + item.day_amount + ') วัน'}} 
                        </span>
                        <span  v-else :class="'badge badge-custom badge-bg-' + item.class_status" >@{{ item.status_text }}</span>
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