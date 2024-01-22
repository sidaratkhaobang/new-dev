<div class="row push mb-3">
    <div class="col-sm-3">
        <h4>{{ __('gps.car_data') }}</h4>
    </div>
    <div class="col-sm-9 text-end">
        @if (isset($create))
            <button type="button" class="btn btn-primary" onclick="addCar()">{{ __('gps.add_car') }}</button>
        @endif
    </div>
</div>
<div id="gps-car" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>{{ __('gps.license_plate') }}</th>
                <th>{{ __('gps.chassis_no') }}</th>
                <th>{{ __('gps.vid') }}</th>
                <th class="text-center">{{ __('gps.remove_gps') }}</th>
                <th class="text-center">{{ __('gps.stop_gps') }}</th>
                <th>{{ __('gps.remark') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="car_list.length > 0">
                <template v-for="(item, index) in car_list">
                    <tr>
                        <td>@{{ item.license_plate_text }}</td>
                        <td>@{{ item.chassis_no_text }}</td>
                        <td>@{{ item.vid_text }}</td>
                        <td class="text-center">
                            <div v-if="item.is_check_gps == 1">
                                <i class="far fa-circle-check" aria-hidden="true" style="color: green;"></i>
                            </div>
                        </td>
                        <td class="text-center">
                            <div v-if="item.is_check_gps == 2">
                                <i class="far fa-circle-check" aria-hidden="true" style="color: green;"></i>
                            </div>
                        </td>
                        <td>@{{ item.gps_remark }}</td>
                        @if (!isset($view))
                            <td class="sticky-col text-center">
                                <div class="btn-group">
                                    <div class="col-sm-12">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                <a class="dropdown-item" v-on:click="editGpsCar(index)"><i
                                                        class="far fa-edit me-1"></i> แก้ไข</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                    <input type="hidden" v-bind:name="'cars['+ index +'][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'cars['+ index +'][gps_id]'" id="gps_id"
                        v-bind:value="item.gps_id">
                    <input type="hidden" v-bind:name="'cars['+ index +'][license_plate_id]'" id="license_plate_id"
                        v-bind:value="item.license_plate_id">
                    <input type="hidden" v-bind:name="'cars['+ index +'][is_check_gps]'" id="is_check_gps"
                        v-bind:value="item.is_check_gps">
                    <input type="hidden" v-bind:name="'cars['+ index +'][gps_remark]'" id="gps_remark"
                        v-bind:value="item.gps_remark">
                </template>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">"
                        {{ __('lang.no_list') . __('gps.car_data') }} "</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
@include('admin.gps-remove-stop-signal-alerts.modals.car-modal')
