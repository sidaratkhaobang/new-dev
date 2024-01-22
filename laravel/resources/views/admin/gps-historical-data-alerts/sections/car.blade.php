<div class="row push mb-3">
    <div class="col-sm-6">
        <h5>{{ __('gps.request_table') }}</h5>
    </div>
    <div class="col-sm-6 text-end">
        @if (!isset($view))
            <div class="file btn btn-purple">
                <i class="fa fa-fw fa-upload me-1"></i>
                {{ __('gps.upload_excel') }}
                <input id="upload" type=file name="file[]" />
            </div>
            <button type="button" class="btn btn-primary" onclick="addCar()">{{ __('gps.add_new') }}</button>
        @endif
    </div>
</div>
<div id="historical-car" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>{{ __('gps.license_plate') }}</th>
                <th>{{ __('gps.date') }}</th>
                <th>{{ __('gps.start_time') }}</th>
                <th>{{ __('gps.end_time') }}</th>
                @if (!isset($view))
                    <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
                @endif
            </thead>
            <tbody v-if="car_list.length > 0">
                <template v-for="(item, index) in car_list">
                    <tr>
                        <td>@{{ item.license_plate_text }}</td>
                        <td>@{{ formatDate(item.start_date) }} <span v-if="item.end_date">-</span> @{{ formatDate(item.end_date) }}</td>
                        <td>@{{ item.start_time }}</td>
                        <td>@{{ item.end_time }}</td>
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
                                                <a class="dropdown-item" v-on:click="editHistoricalCar(index)"><i
                                                        class="far fa-edit me-1"></i> แก้ไข</a>
                                                <a class="dropdown-item btn-delete-row" href="javascript:void(0)"
                                                    v-on:click="removeHistoricalCar(index)"><i
                                                        class="fa fa-trash-alt me-1"></i> ลบ
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                    <input type="hidden" v-bind:name="'cars['+ index +'][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'cars['+ index +'][license_plate_id]'" id="license_plate_id"
                        v-bind:value="item.license_plate_id">
                    <input type="hidden" v-bind:name="'cars['+ index +'][start_date]'" id="start_date"
                        v-bind:value="item.start_date">
                    <input type="hidden" v-bind:name="'cars['+ index +'][end_date]'" id="end_date"
                        v-bind:value="item.end_date">
                    <input type="hidden" v-bind:name="'cars['+ index +'][start_time]'" id="start_time"
                        v-bind:value="item.start_time">
                    <input type="hidden" v-bind:name="'cars['+ index +'][end_time]'" id="end_time"
                        v-bind:value="item.end_time">
                </template>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">"
                        {{ __('lang.no_list') . __('gps.request_table') }} "</td>
                </tr>
            </tbody>
        </table>
        <div v-for="(item) in pending_delete_car_ids">
            <input type="hidden" v-bind:name="'pending_delete_car_ids[]'" v-bind:value="item">
        </div>
    </div>
</div>
<br>
@include('admin.gps-historical-data-alerts.modals.car-modal')
