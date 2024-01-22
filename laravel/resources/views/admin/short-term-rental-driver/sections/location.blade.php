<x-blocks.block :title="__('short_term_rentals.location_detail')" :optionals="['is_toggle' => false]">
    <div id="location-vue">
        <div v-if="car_data.length > 0">
            <template v-for="(value,key) in car_data">
                <div class="row gx-0 mb-2">
                    <div class="col-12 d-flex pt-2 ps-2 pb-2 car-header">
                        <div class="d-flex justify-content-start align-items-center flex-grow-1 h-100">
                            <img class="img-block car-image" :src="value.image_url" alt="">
                            <div class="ms-3 me-3 d-block ">
                                <p class="car-class-text mb-0">
                                    @{{ value.class_full_name ?? "" }}
                                </p>
                                <p class="car-name-text mb-0">
                                    @{{ value.license_plate ?? "-" }}
                                </p>
                            </div>
                        </div>
                        <div style="position: sticky;left: 100%;"
                             class="d-flex justify-content-center align-items-center">
                            <div class="block-options-item ms-2 ">
                                <button type="button" class="btn btn-primary me-3"
                                        @click="addStopOverData(key)">
                                    <i class="icon-add-circle"></i> เพิ่ม
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="collapse ps-0 pe-0 show" :id="'car-' + key">
                        <div class="table-wrap db-scroll">
                            <table class="table table-striped table-vcenter" style="border-radius:0px">
                                <thead class="bg-body-dark">
                                <tr>
                                    <th style="width: 1px;">#</th>
                                    <th width="30%">{{__('short_term_rentals.location')}}</th>
                                    <th></th>
                                    <th>{{__('short_term_rentals.stopover_start_date')}}</th>
                                    <th>{{__('short_term_rentals.stopover_end_date')}}</th>
                                    <th style="width: 1px;" class="sticky-col"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="value.rental_checkins.length > 0">
                                    <tr v-for="(item,index) in value.rental_checkins">
                                        <td>
                                            @{{ index+1 }}
                                            <input type="hidden" :value="item.id"
                                                   :name="'location['+value.car_id+']['+index+'][id]'">
                                            <input type="hidden" :value="item.location_name"
                                                   :name="'location['+value.car_id+']['+index+'][location_name]'">
                                            <input type="hidden" :value="item.lat"
                                                   :name="'location['+value.car_id+']['+index+'][lat]'">
                                            <input type="hidden" :value="item.lng"
                                                   :name="'location['+value.car_id+']['+index+'][lng]'">
                                        </td>
                                        <td>
                                            <select-2-ajax :id="'select_'+key+'_'+index"
                                                           :value="item.location_id"
                                                           :defaultname="item.location_name ?? item.default_name"
                                                           v-bind:name="'location['+value.car_id+']['+index+'][location_id]'"
                                                           url="{{ route('admin.util.select2-short-term-rental.get-location') }}">
                                            </select-2-ajax>

                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-mini text-center"
                                                    @click="openModalAddLocation(key,index)"
                                                    style="max-width: 100% !important;">
                                                <i class="icon-add-circle"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <input-date-vue
                                                    :name="'location['+value.car_id+']['+index+'][arrived_at]'"
                                                    :date_enable_time="true"
                                                    v-model="item.arrived_at"/>
                                        </td>
                                        <td>
                                            <input-date-vue
                                                    :name="'location['+value.car_id+']['+index+'][departured_at]'"
                                                    :date_enable_time="true"
                                                    v-model="item.departured_at"/>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-mini" @click="remove(key,index)">
                                                <i class="fa-solid fa-trash-can pe-none" style="color: red"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="12" class="text-center">" ไม่มีรายการ "</td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
            <template v-for="(value,key) in del_input_id">
                <input type="hidden"
                       v-bind:name="'location_del[' + key +']'"
                       v-bind:value="value">
            </template>
        </div>
    </div>
</x-blocks.block>