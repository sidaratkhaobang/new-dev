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
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
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
                    <td class="sticky-col text-center">
                        <div class="btn-group">
                            <div class="col-sm-12">
                                <div class="dropdown dropleft">
                                    <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                        id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="fa fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                        @can(Actions::Manage . '_' . Resources::ParkingZone)
                                        <a class="dropdown-item" href="javascript:void(0)" v-on:click="edit(index)"><i
                                                class="far fa-edit me-1"></i> แก้ไขข้อมูล</a>
                                        <a v-if="item.id && item.status == 1" class="dropdown-item"
                                            :href="gotoShiftCarRoute(item.id)"><i class="fa fa-arrows-rotate me-1"></i>
                                            ย้ายรถออกจากโซนจอด</a>
                                        <a v-if="item.id && item.status == 1"
                                            class="dropdown-item btn-car-park-area-update-status"
                                            data-status="{{ \App\Enums\CarParkAreaStatusEnum::INACTIVE }}"
                                            :data-id="item.id" href="javascript:void(0)"><i
                                                class="far fa-circle-xmark me-1"></i>
                                            ปิดโซนจอด</a>
                                        <a v-if="item.id && item.status == 2"
                                            class="dropdown-item btn-car-park-area-update-status"
                                            data-status="{{ \App\Enums\CarParkAreaStatusEnum::ACTIVE }}"
                                            :data-id="item.id" href="javascript:void(0)"><i
                                                class="far fa-circle-check me-1"></i>
                                            เปิดโซนจอด</a>
                                        <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                                            v-on:click="remove(index)"><i class="fa fa-trash-alt me-1"></i> ลบโซนจอด</a>
                                            @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" v-bind:name="'car_zone['+ index+ '][id]'" id="id"
                        v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'car_zone['+ index+ '][start_number]'" id="start_number"
                        v-bind:value="item.start_number">
                    <input type="hidden" v-bind:name="'car_zone['+ index+ '][end_number]'" id="end_number"
                        v-bind:value="item.end_number">
                    <input type="hidden" v-bind:name="'car_zone['+ index+ '][car_groups]'" id="car_groups"
                        v-bind:value="item.car_groups">
                        <input type="hidden" v-bind:name="'car_zone['+ index+ '][zone_type]'" id="zone_type"
                        v-bind:value="item.zone_type">
                    <input type="hidden" v-bind:name="'car_zone['+ index+ '][area_size]'" id="area_size"
                        v-bind:value="item.area_size">

                    {{-- <input type="hidden" v-bind:name="'car_zone['+ index+ '][remark]'" id="remark" v-bind:value="item.remark"> --}}
                </tr>
                <tr>
                    <th colspan="6" class="text-end">{{ __('lang.sum') }}</th>
                    <th>@{{ sum_total_slot }}</th>
                    <th>@{{ sum_unavailable_car_slot_count }}</th>
                    <th>@{{ sum_available_car_slot_count }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="11">" {{ __('lang.no_list') }} "</td>
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
            <input type="hidden" v-bind:name="'add_car_park['+ index+ '][area_id]'" id="add_car_park_area_id" v-bind:value="item.area_id">
            <input type="hidden" v-bind:name="'add_car_park['+ index+ '][number]'" id="number" v-bind:value="item.number">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-primary" onclick="openCarSlotModal()">{{ __('lang.add') }}</button>
        </div>
    </div>
    @include('admin.parking-lots.modals.car-slot-modal')
</div>
