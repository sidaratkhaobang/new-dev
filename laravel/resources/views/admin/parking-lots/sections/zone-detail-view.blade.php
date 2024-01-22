<div id="car-slots" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap db-scroll">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('parking_lots.start_slot_no') }}</th>
                <th>{{ __('parking_lots.end_slot_no') }}</th>
                <th>{{ __('parking_lots.group_car') }}</th>
                <th>{{ __('parking_lots.slot_size') }}</th>
                <th>{{ __('parking_lots.zone_type') }}</th>
                <th>{{ __('parking_lots.total_slot') }}</th>
                <th>{{ __('parking_lots.total_unavailable_slot') }}</th>
                <th>{{ __('parking_lots.total_available_slot') }}</th>
                <th>{{ __('lang.status') }}</th>
            </thead>
            <tbody v-if="car_slot_list.length > 0">
                <tr v-for="(item, index) in car_slot_list">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{ item.start_number }}</td>
                    <td>@{{ item.end_number }}</td>
                    <td>@{{ item.car_group_text }}</td>
                    <td>@{{ item.area_size_text }}</td>
                    <td> @{{ item.zone_type_name }}</td>
                    <td>@{{ item.total_slot }}</td>
                    <td>@{{ item.unavailable_car_slot_count }}</td>
                    <td>@{{ item.available_car_slot_count }}</td>
                    <td>
                        <template v-if="item.status">
                            <span class="badge larger-badge badge-pill text-white"
                                :class="{ 'bg-success': item.status == 1, 'bg-danger': item.status == 2 }">
                                @{{ item.status == 1 ? 'กำลังใช้งาน' : 'ปิดการใช้งาน' }}
                            </span>
                        </template>
                    </td>
                </tr>
                <tr>
                    <th colspan="6" class="text-end">{{ __('lang.sum') }}</th>
                    <th>@{{ sum_total_slot }}</th>
                    <th>@{{ sum_unavailable_car_slot_count }}</th>
                    <th>@{{ sum_available_car_slot_count }}</th>
                    <th></th>  
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="10">" {{ __('lang.no_list') }} "</td>
                </tr>
            </tbody>
        </table>
        <div v-for="(item, index) in deleted_area_list">
            <input type="hidden" v-bind:name="'deleted_car_park_area['+ index+ ']'" id="deleted_car_park_area"
                v-bind:value="item">
        </div>
        <div v-for="(item, index) in delete_car_park_list">
            <input type="hidden" v-bind:name="'delete_car_park['+ index+ ']'" id="delete_car_park" v-bind:value="item">
        </div>
        <div v-for="(item, index) in add_car_park_list">
            <input type="hidden" v-bind:name="'add_car_park['+ index+ ']'" id="add_car_park" v-bind:value="item">
        </div>
    </div>
</div>
