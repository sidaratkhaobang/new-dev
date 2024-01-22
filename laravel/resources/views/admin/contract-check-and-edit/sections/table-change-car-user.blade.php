<div id="table-change-car-user" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
            <th style="width: 30%">{{ __('ทะเบียน') }}</th>
            <th style="width: 30%">{{ __('ชื่อผู้ใช้') }}</th>
            <th style="width: 30%">{{ __('เบอร์โทร') }}</th>
            <th style="width: 10%" class="sticky-col text-center"></th>
            </thead>
            <tbody v-if="data_list.length > 0">
            <tr v-for="(item, index) in data_list">
                <td>@{{ item.car_license_plate }}</td>
                <td>@{{ item.car_user }}</td>
                <td>@{{ item.car_phone }}</td>
                <td class="sticky-col text-center" @click="removeRow(index)">
                    <i class="far fa-trash-alt" style="cursor: pointer;"></i>
                </td>
            </tr>
            </tbody>
            <tbody v-else>
            <tr class="table-empty">
                <td class="text-center" colspan="4">
                    "{{ __('lang.no_list') }} "
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
