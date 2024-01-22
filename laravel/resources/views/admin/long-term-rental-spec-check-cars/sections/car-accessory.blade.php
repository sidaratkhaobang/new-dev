<div class="row push mb-3">
    <div class="col-auto mt-2">
        <h4>{{ __('long_term_rentals.car_table') }}</h4>
    </div>
    @if (!isset($view_only))
        @if (strcmp($d->spec_status, SpecStatusEnum::PENDING_CHECK) !== 0)
            @if (!isset($accessory_controller))
                <div class="col-sm-3">
                    <div class="col-md-3 text-start">
                        <button type="button" class="btn btn-primary" onclick="addBom()">Bom</button>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>
<hr>
<div class="mb-4" id="car-accessory" v-cloak data-detail-uri="" data-title="">
    <div class="table-wrap">
        <table class="table">
            <thead class="bg-body-dark">
                <th>#</th>
                <th>{{ __('long_term_rentals.car_class') }}</th>
                <th>{{ __('long_term_rentals.car_color') }}</th>
                <th>{{ __('long_term_rentals.optional_accessory') }}</th>
                <th>{{ __('long_term_rentals.car_amount') }}</th>
                <th>{{ __('long_term_rentals.remark') }}</th>
                <th class="sticky-col text-center">{{ __('lang.tools') }}</th>
            </thead>
            <tbody v-if="car_list.length > 0">
                <template v-for="(item, index) in car_list">
                    <tr>
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ item.car_class_text }}</td>
                        <td>@{{ item.car_color_text }}</td>
                        <td>@{{ convertToText(item.have_accessories) }}</td>
                        <td>@{{ item.amount_car }} {{ __('long_term_rentals.car_unit') }}</td>
                        <td>@{{ item.remark }}</td>
                        <td class="sticky-col text-center">
                            <div class="btn-group">
                                <div class="col-sm-12">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            @if (isset($view_only)) disabled @endif
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            <a class="dropdown-item" v-on:click="editCar(index)"><i
                                                    class="far fa-edit me-1"></i> แก้ไข</a>
                                            {{-- <a class="dropdown-item btn-delete-row" v-on:click="removeCar(index)"><i
                                                    class="fa fa-trash-alt me-1"></i> ลบ</a> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <input type="hidden" v-bind:name="'cars['+ index +'][id]'" v-bind:value="item.id">
                        <input type="hidden" v-bind:name="'cars['+ index +'][car_class_id]'" id="car_class_id"
                            v-bind:value="item.car_class_id">
                        <input type="hidden" v-bind:name="'cars['+ index +'][car_color_id]'" id="car_color_id"
                            v-bind:value="item.car_color_id">
                        <input type="hidden" v-bind:name="'cars['+ index +'][amount_car]'" id="amount_car"
                            v-bind:value="item.amount_car">
                        <input type="hidden" v-bind:name="'cars['+ index +'][remark]'" id="car_remark"
                            v-bind:value="item.remark">
                        <input type="hidden" v-bind:name="'cars['+ index +'][have_accessories]'" id="have_accessories"
                            v-bind:value="item.have_accessories">
                    </tr>
                    <template v-if="item.accessory_list.length > 0">
                        <tr class="tr-last-item">
                            <td class="td-table"></td>
                            <td class="td-table table-wrap" colspan="6">
                                <table class="table table-striped">
                                    <thead class="bg-body-dark">
                                        <th style="width: 2px;">#</th>
                                        <th>{{ __('long_term_rentals.car_accessory') }}</th>
                                        <th>{{ __('long_term_rentals.amount_per_car') }}</th>
                                        <th>{{ __('purchase_requisitions.amount_accessory') }}</th>
                                        <th>{{ __('purchase_requisitions.remark') }}</th>
                                        <th></th>
                                    </thead>
                                    <tbody v-for="(accessory, accessory_index) in item.accessory_list">
                                        <td>@{{ accessory_index + 1 }}</td>
                                        <td>@{{ accessory.accessory_text }}</td>
                                        <td>@{{ accessory.amount_per_car_accessory }}</td>
                                        <td>@{{ accessory.amount_accessory }}</td>
                                        <td>@{{ accessory.remark }}</td>
                                        <td></td>
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][id]'"
                                            id="accessory_id" v-bind:value="accessory.accessory_id">
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][amount_per_car]'"
                                            id="accessory_amount_per_car"
                                            v-bind:value="accessory.amount_per_car_accessory">
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][amount]'"
                                            id="accessory_amount" v-bind:value="accessory.amount_accessory">
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][remark]'"
                                            v-bind:value="accessory.remark">
                                        <input type="hidden"
                                            v-bind:name="'cars['+ index +'][accessory]['+ accessory_index +'][type_accessories]'"
                                            v-bind:value="accessory.type_accessories">
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </template>
                </template>
                <tr>
                    <td></td>
                    <td>{{ __('long_term_rentals.summary_car_detail') }}</td>
                    <td></td>
                    <td></td>
                    <td id="total_car" v-bind:value="total_car">@{{ total_car }}
                        {{ __('long_term_rentals.car_unit') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr class="table-empty">
                    <td class="text-center" colspan="7">"
                        {{ __('lang.no_list') . __('long_term_rentals.car_table') }} "</td>
                </tr>
            </tbody>
        </table>
        <div v-for="(item, index) in all_accessories">
            <input type="hidden" v-bind:name="'accessories[' + index + '][accessory_id]'"
                v-bind:value="item.accessory_id">
            <input type="hidden" v-bind:name="'accessories[' + index + '][car_index]'" v-bind:value="item.car_index">
            <input type="hidden" v-bind:name="'accessories[' + index + '][accessory_amount]'"
                v-bind:value="item.amount_accessory">

        </div>
        <div v-for="(item) in pending_delete_car_ids">
            <input type="hidden" v-bind:name="'pending_delete_car_ids[]'" v-bind:value="item">
        </div>
    </div>
    @if (!isset($view_only))
        <div class="row">
        </div>
    @endif
</div>
<br>
@include('admin.long-term-rental-spec-check-cars.modals.car-accessory-modal')
