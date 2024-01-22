<h4>{{ __('long_term_rentals.car_table') }}</h4>
<hr>
<div id="car-accessory2" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table table-striped">
            <thead class="bg-body-dark">
                <th>#</th>
                <th style="max-width:200px; word-break: break-all; white-space: normal;">
                    {{ __('long_term_rentals.tor_detail') }}</th>
                <th>{{ __('long_term_rentals.car_class') }}</th>
                <th>{{ __('long_term_rentals.car_color') }}</th>
                <th>{{ __('long_term_rentals.accessories') }}</th>
                <th>{{ __('long_term_rentals.car_amount') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="car_list.length > 0">
                <tr v-for="(item, index) in car_list">
                    <td>@{{ index + 1 }}</td>
                    <td style="max-width:200px; word-break: break-all; white-space: normal;">
                        @{{ item.remark_tor }}</td>
                    <td>@{{ item.car_class_text }}</td>
                    <td>@{{ item.car_color_text }}</td>
                    <td>@{{ convertToText(item.have_accessories) }}</td>
                    <td>@{{ item.amount_car }} {{ __('long_term_rentals.car_unit') }}</td>
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
                                        <a class="dropdown-item" v-on:click="viewCar(index)"><i
                                                class="fa fa-eye me-1"></i> ดูข้อมูล</a>
                                        @if (!isset($view))
                                            @if ($d->comparison_price_status != ComparisonPriceStatusEnum::CONFIRM)
                                                <a class="dropdown-item" v-on:click="editCar(index)"><i
                                                        class="far fa-edit me-1"></i> แก้ไข</a>
                                                <a class="dropdown-item btn-delete-row" v-on:click="removeCar(index)"><i
                                                        class="fa fa-trash-alt me-1"></i> ลบ</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" v-bind:name="'data['+ index+ '][id]'" v-bind:value="item.id">
                    <input type="hidden" v-bind:name="'data['+ index+ '][car_class_id]'" id="car_class_id"
                        v-bind:value="item.car_class_id">
                    <input type="hidden" v-bind:name="'data['+ index+ '][car_color_id]'" id="car_color_id"
                        v-bind:value="item.car_color_id">
                    <input type="hidden" v-bind:name="'data['+ index+ '][amount_car]'" id="amount_car"
                        v-bind:value="item.amount_car">
                    <input type="hidden" v-bind:name="'data['+ index +'][remark]'" id="car_remark"
                        v-bind:value="item.remark">
                    <input type="hidden" v-bind:name="'data['+ index +'][have_accessories]'" id="have_accessories"
                        v-bind:value="item.have_accessories">
                    @if (!isset($view))
                        <template v-if="item.accessory_list.length > 0">
                            <template v-for="(accessory, accessory_index) in item.accessory_list">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][id]'"
                                    id="accessory_id" v-bind:value="accessory.accessory_id">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][amount_per_car]'"
                                    id="accessory_amount_per_car" v-bind:value="accessory.amount_per_car_accessory">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][amount]'"
                                    id="accessory_amount" v-bind:value="accessory.amount_accessory">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][tor_section]'"
                                    id="accessory_tor_section" v-bind:value="accessory.tor_section">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][remark]'"
                                    id="accessory_remark" v-bind:value="accessory.remark">
                                <input type="hidden"
                                    v-bind:name="'data['+ index +'][accessory]['+ accessory_index +'][type_accessories]'"
                                    id="type_accessories" v-bind:value="accessory.type_accessories">
                            </template>
                        </template>
                    @endif
                </tr>
                <tr>
                    <td></td>
                    <td>{{ __('long_term_rentals.summary_car_detail') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td id="total_car" v-bind:value="total_car">@{{ total_car }}
                        {{ __('long_term_rentals.car_unit') }}</td>
                    <td></td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">“
                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} “</td>
                </tr>
            </tbody>
        </table>
        <div v-for="(item, index) in all_accessories">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_id]'"
                v-bind:value="item.accessory_id">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][car_index]'" v-bind:value="item.car_index">
            <input type="hidden" v-bind:name="'accessories['+ index+ '][accessory_amount]'"
                v-bind:value="item.amount_accessory">

        </div>
        <div v-for="(item) in pending_delete_car_ids">
            <input type="hidden" v-bind:name="'pending_delete_car_ids[]'" v-bind:value="item">
        </div>
    </div>
    @if (!isset($view))
        @if (!isset($cannot_add))
            <div class="row">
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary" onclick="addCar()"
                        id="openModal">{{ __('lang.add') }}</button>
                </div>
            </div>
        @endif
    @endif
</div>
<br>
@include('admin.long-term-rental-compare-price.modals.car-accessory-modal')
